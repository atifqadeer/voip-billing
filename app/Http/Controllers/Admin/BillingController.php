<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Cdr;
use App\Models\Tax;
use App\Models\TaxBillingDetail;
use App\Models\Billing;
use App\Models\BillingDetail;
use App\Models\AdditionalService;
use App\Models\AdditionalBillingDetail;
use App\Models\CurrencyList;
use App\Models\CompanyServices;
use App\Models\clientFixedLineServicesBillDetails;
use App\Models\Setting;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use App\Actions\GenerateBillingPDF;
use App\Models\CdrProvider;
use App\Models\ClientCDRHistory;
use App\Models\ClientInhouseServiceUsage;
use App\Models\Inclusive;
use App\Traits\CurrencyTrait;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    use CurrencyTrait;  // Use the CurrencyTrait

    public function index()
    {
        $clients = Client::where('status', 'enable')->get();
        return view('admin.billings.index', compact('clients'));
    }

    public function getBillingList(Request $request)
    {
        if ($request->ajax()) {
            $billings = Billing::with('client')
                ->orderBy('created_at', 'desc');

            // Apply date range filter if provided
            if ($request->date) {
                // Assuming $request->date is in the format 'YYYY-MM'
                $monthYear = explode('-', $request->date);

                if (count($monthYear) == 2) {
                    $year = $monthYear[0];
                    $month = $monthYear[1];

                    // Filter based on the separate 'year' and 'month' columns
                    $billings->where('billings.year', $year)
                        ->where('billings.month', $month);
                }
            }

            // Apply payment status filter
            if ($request->paymentStatus && $request->paymentStatus != 'all') {
                $billings->where('billings.payment_status', $request->paymentStatus);
            }

            return DataTables::of($billings)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($billing) {
                    return '<input type="checkbox" class="row-checkbox" value="' . $billing->id . '">';
                })
                ->addColumn('client_name', function ($billing) {
                    return ucwords($billing->client->client_name);
                })
                ->addColumn('payment_status', function ($billing) {
                    return $billing->payment_status == "paid"
                        ? '<span class="badge badge-success">Paid</span>'
                        : '<span class="badge badge-danger">Unpaid</span>';
                })
                ->addColumn('billing_month', function ($billing) {
                    return Carbon::parse($billing->year . '-' . $billing->month . '-01')->format('M, Y');
                })
                ->addColumn('total_payment', function ($billing) {
                    return $this->formatCurrency($billing->total_payment);  // Assume formatCurrency() is a custom method to format prices
                })
                ->addColumn('generated_at', function ($billing) {
                    return Carbon::parse($billing->created_at)->format('M j Y, h:i A');
                })
                ->addColumn('action', function ($billing) {
                    $pdfUrl = route('billing.pdfData', ['id' => $billing->pdf_file_name]);
                    $pdfBtn = '<a href="' . $pdfUrl . '" target="_blank" class="btn btn-sm btn-info" title="PDF"><i class="fa-solid fa-file-pdf"></i></a>&nbsp;';

                    $deleteBtn = '<a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="deleteBill(' . $billing->id . ')"><i class="fa fa-trash"></i></a>&nbsp;';
                    $historyBtn = '<a href="' . route('bill.downloadBillDetails', ['bill_id' => $billing->id, 'user_id' => $billing->client->id]) . '" 
                                    class="btn btn-sm btn-primary" title="Download Call Summary"><i class="fa fa-download"></i></a>&nbsp;';
                    $paymentStatusBtn = $billing->payment_status == 'unpaid'
                        ? '<button class="btn btn-sm btn-danger" title="Mark as Paid" onclick="togglePaymentStatus(' . $billing->id . ')"><i class="fa fa-credit-card"></i></button>'
                        : '<button class="btn btn-sm btn-success" title="Paid" disabled><i class="fa fa-check"></i></button>';

                    $exists = ClientCDRHistory::where('bill_id', $billing->id)->exists();  // Delete client CDR history records
                    return $deleteBtn . $pdfBtn . ($exists ? $historyBtn : '' ). $paymentStatusBtn;
                })
                ->rawColumns(['action', 'payment_status', 'checkbox'])
                ->make(true);
        }
    }

    public function generateBill(Request $request)
    {
        $validatedData = $request->validate([
            'monthYear' => 'required|date_format:Y-m',
        ]);

        $monthYear = $validatedData['monthYear'];
        $month = Carbon::parse($monthYear)->format('m');
        $year = Carbon::parse($monthYear)->format('Y');

        DB::beginTransaction();

        try {
            // Ensure client request is an array and clean up any spaces
            $clientRequest = is_array($request->client) ? array_map('trim', $request->client) : [];

            // Determine which clients to process
            $clients = (in_array('all', $clientRequest))
                ? Client::where('status', 'enable')->get()
                : Client::whereIn('id', $clientRequest)->get();

            // Log retrieved clients
            if ($clients->isEmpty()) {
                return response()->json(['error' => 'No clients found for the given criteria.'], 422);
            }

            foreach ($clients as $client) {
                DB::beginTransaction();  // Ensure client-specific transaction

                try {
                    $cdrQuery = Cdr::whereYear('date', '=', $year)
                        ->whereMonth('date', '=', $month);

                    if (!$cdrQuery->exists()) {
                        return response()->json(['error' => 'No CDR records found for the given date.'], 422);
                    }

                    $billExists = Billing::where('client_id', $client->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->exists();

                    if (!$billExists) {
                        // Process billing for the client
                        $numbers = $this->processNumbers($client);
                        $clientInhouseServiceArray = $this->fetchClientInhouseServices($client->id);
                        $billingRecord = $this->createBillingRecord($client, $monthYear);
                        $cdrData = $this->fetchCdrData($billingRecord, $client->id, $numbers, $client->trunk_number, $monthYear, $clientInhouseServiceArray);

                        $this->additionalBillingDetails($billingRecord, $client);
                        $this->billingDetails($billingRecord, $cdrData);

                        if ($client->is_enable_fixed_line_services == "1") {
                            $this->fetchCdrDataForFixedLine($client->fixed_line_service_number, $client->trunk_number, $monthYear, $billingRecord);
                        }

                        if ($client->is_enable_vat_tax == "1") {
                            $this->applyTaxes($billingRecord->id);
                        }

                        $this->pdfMaker($billingRecord->id);

                        DB::commit();  // Commit the client transaction after successful processing
                    } else {
                        // Log and skip processing for this client
                        Log::warning('Bill already exists for client:', [
                            'client_id' => $client->id,
                            'month_year' => $monthYear,
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log individual client processing errors
                    DB::rollBack();  // Rollback client-specific transaction in case of error
                    Log::error("Error processing client {$client->id}: " . $e->getMessage());
                }
            }

            DB::commit();  // Commit the entire transaction after all clients have been processed successfully
            return response()->json(['message' => 'Bill generated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback entire transaction if any error occurs
            Log::error('Error generating bills:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error generating bills: ' . $e->getMessage()], 500);
        }
    }

    private function processNumbers($client)
    {
        $clientOutgoing = $client->client_outgoing_number ? explode(',', $client->client_outgoing_number) : [];
        $clientIncoming = $client->client_incoming_number ? explode(',', $client->client_incoming_number) : [];
        $clientPhone = $client->client_phone_number ? explode(',', $client->client_phone_number) : [];

        $mergedNumbers = array_merge($clientOutgoing, $clientIncoming, $clientPhone);

        return array_values(array_unique(array_map(function ($number) {
            $number = ltrim($number, '0');
            return ltrim($number, '44');
        }, $mergedNumbers)));
    }

    // private function fetchCdrData($billingRecord, $clientID, $numbers, $trunk, $monthYear, $clientInhouseServiceArray)
    // {
    //     // Extract year and month from the monthYear parameter
    //     $year = substr($monthYear, 0, 4);
    //     $month = substr($monthYear, 5, 2);

    //     $cdrRecords = [];
    //     $trunks = explode(',', $trunk);
        
    //     // Base date filter
    //     $baseQuery = Cdr::whereYear('date', '=', $year)
    //         ->whereMonth('date', '=', $month);
        
    //     // First attempt: filter by trunk and optionally numbers
    //     $cdrQuery = clone $baseQuery;
    //     $cdrQuery->whereIn('trunk', $trunks);

    //     if (!empty($numbers)) {
    //         $cdrQuery->orWhere(function ($query) use ($numbers) {
    //             foreach ($numbers as $number) {
    //                 $query->Where('to_number', 'like', "%$number%")
    //                     ->orWhere('from_cli', 'like', "%$number%");
    //             }
    //         });
    //     }
        
    //     $cdrRecords = $cdrQuery->distinct()->get();

    //     // Fetch inclusives where status is enabled and apply additional filters
    //     $inclusives = Inclusive::where('status', 'enable')
    //         ->whereIn('inhouse_service_id', $clientInhouseServiceArray)
    //         ->get();

    //     if ($inclusives) {
    //         // Combine all skip_to values into one array and apply the filter
    //         $skipToValues = $inclusives->flatMap(function ($inclusive) {
    //             return explode(',', $inclusive->skip_to);
    //         })->toArray();

    //         // Apply the 'whereNotIn' filter to exclude simplified_to_descriptive values
    //         $cdrQuery->whereNotIn('simplified_to_descriptive', $skipToValues);
    //     }

    //     // Execute the query and get the result
    //     $cdrRecords = $cdrQuery->get();

    //     // Now, update the client_id for each record found
    //     foreach ($cdrRecords as $cdr) {
    //         // Check if the record already exists in the client_cdr_history table
    //         $exists = ClientCDRHistory::where('reference', $cdr->reference)
    //             ->where('client_id', $clientID)
    //             ->where('cdr_id', $cdr->id)
    //             ->exists();

    //         if (!$exists) {
    //             // Save each CDR record in the client_cdr_history table
    //             ClientCDRHistory::insert([
    //                 'bill_id' => $billingRecord->id,
    //                 'reference' => $cdr->reference,
    //                 'client_id' => $clientID,
    //                 'cdr_id' => $cdr->id,
    //                 'trunk' => $cdr->trunk,
    //                 'tag' => $cdr->tag,
    //                 'date' => $cdr->date,
    //                 'time' => $cdr->time,
    //                 'from_cli' => $cdr->from_cli,
    //                 'from_descriptive' => $cdr->from_descriptive,
    //                 'to_number' => $cdr->to_number,
    //                 'to_descriptive' => $cdr->to_descriptive,
    //                 'destination_id' => $cdr->destination_id,
    //                 'duration_seconds' => $cdr->duration_seconds,
    //                 'billable_duration_seconds' => $cdr->billable_duration_seconds,
    //                 'peak_duration' => $cdr->peak_duration,
    //                 'off_peak_duration' => $cdr->off_peak_duration,
    //                 'weekend_duration' => $cdr->weekend_duration,
    //                 'peak_rate' => $cdr->peak_rate,
    //                 'off_peak_rate' => $cdr->off_peak_rate,
    //                 'weekend_rate' => $cdr->weekend_rate,
    //                 'connection_rate' => $cdr->connection_rate,
    //                 'total_charge' => $cdr->total_charge,
    //                 'currency' => $cdr->currency,
    //                 'simplified_to_descriptive' => $cdr->simplified_to_descriptive,
    //                 'bill_amount' => $cdr->bill_amount,
    //                 'calculated_duration' => $cdr->calculated_duration,
    //                 'provider_id' => $cdr->provider_id,
    //             ]);

    //             // Update the client_id field for each CDR record
    //             $cdr->client_id = $clientID;
    //             $cdr->save(); // Save the updated record
    //         }
    //     }

    //     return $cdrRecords; // Return the updated CDR records
    // }

    private function fetchCdrData($billingRecord, $clientID, $numbers, $trunk, $monthYear, $clientInhouseServiceArray)
    {
        // Extract year and month from the monthYear parameter
        $year = substr($monthYear, 0, 4);
        $month = substr($monthYear, 5, 2);

        $cdrRecords = [];
        $trunks = explode(',', $trunk);

        // Start building the base query for CDRs based on date
        $cdrQuery = Cdr::whereYear('date', '=', $year)
            ->whereMonth('date', '=', $month);

        // Fetch data by trunks, including records with null or empty trunks
        $cdrByTrunks = $cdrQuery->where(function ($query) use ($trunks) {
            $query->whereIn('trunk', $trunks)
            ->orWhereNull('trunk')
            ->orWhere('trunk', '');
        })->get();

        // If data exists for trunks or null trunks, proceed
        if ($cdrByTrunks->isNotEmpty()) {
            $cdrRecords = $cdrByTrunks;
        }

        // If numbers are provided, fetch data by numbers and merge with trunk data
        if (!empty($numbers)) {
            $cdrByNumbers = $cdrQuery->where(function ($query) use ($numbers) {
            foreach ($numbers as $number) {
                $query->orWhere('to_number', 'like', "%$number%")
                ->orWhere('from_cli', 'like', "%$number%");
            }
            })->get();

            // Merge and ensure distinct records
            $cdrRecords = $cdrRecords->merge($cdrByNumbers)->unique('id');
        }

        // Ensure the final result is distinct
        $cdrRecords = $cdrRecords->unique('id');

        // Fetch inclusives where status is enabled and apply additional filters
        $inclusives = Inclusive::where('status', 'enable')
            ->whereIn('inhouse_service_id', $clientInhouseServiceArray)
            ->get();

        // if ($inclusives) {
        //     // Combine all skip_to values into one array and apply the filter
        //     $skipToValues = $inclusives->flatMap(function ($inclusive) {
        //         return explode(',', $inclusive->skip_to);
        //     })->toArray();

        //     // Apply the 'whereNotIn' filter to exclude simplified_to_descriptive values
        //     $cdrQuery->whereNotIn('simplified_to_descriptive', $skipToValues);
        // }

       if ($inclusives) {
    // Combine all skip_to values into one array
    $skipToValues = $inclusives->flatMap(function ($inclusive) {
        return explode(',', $inclusive->skip_to);
    })->toArray();

    $cdrQuery->where(function ($query) use ($skipToValues) {

        foreach ($skipToValues as $skip) {
            $skip = trim($skip);

            if (strtolower($skip) === 'uk - mobile') {
                // Exclude all UK Mobile variations
                $query->whereRaw("simplified_to_descriptive NOT LIKE ?", ['UK - Mobile%']);
            } else {
                // Exclude exact matches
                $query->where('simplified_to_descriptive', '<>', $skip);
            }
        }

    });
}



        // Execute the query and get the result
        $cdrRecords = $cdrQuery->get();

        // Now, update the client_id for each record found
        foreach ($cdrRecords as $cdr) {
            // Check if the record already exists in the client_cdr_history table
            $exists = ClientCDRHistory::where('reference', $cdr->reference)
                ->where('client_id', $clientID)
                ->where('cdr_id', $cdr->id)
                ->exists();

            if (!$exists) {
                // Save each CDR record in the client_cdr_history table
                ClientCDRHistory::insert([
                    'bill_id' => $billingRecord->id,
                    'reference' => $cdr->reference,
                    'client_id' => $clientID,
                    'cdr_id' => $cdr->id,
                    'trunk' => $cdr->trunk,
                    'tag' => $cdr->tag,
                    'date' => $cdr->date,
                    'time' => $cdr->time,
                    'from_cli' => $cdr->from_cli,
                    'from_descriptive' => $cdr->from_descriptive,
                    'to_number' => $cdr->to_number,
                    'to_descriptive' => $cdr->to_descriptive,
                    'destination_id' => $cdr->destination_id,
                    'duration_seconds' => $cdr->duration_seconds,
                    'billable_duration_seconds' => $cdr->billable_duration_seconds,
                    'peak_duration' => $cdr->peak_duration,
                    'off_peak_duration' => $cdr->off_peak_duration,
                    'weekend_duration' => $cdr->weekend_duration,
                    'peak_rate' => $cdr->peak_rate,
                    'off_peak_rate' => $cdr->off_peak_rate,
                    'weekend_rate' => $cdr->weekend_rate,
                    'connection_rate' => $cdr->connection_rate,
                    'total_charge' => $cdr->total_charge,
                    'currency' => $cdr->currency,
                    'simplified_to_descriptive' => $cdr->simplified_to_descriptive,
                    'bill_amount' => $cdr->bill_amount,
                    'calculated_duration' => $cdr->calculated_duration,
                    'provider_id' => $cdr->provider_id,
                ]);

                // Update the client_id field for each CDR record
                $cdr->client_id = $clientID;
                $cdr->save(); // Save the updated record
            }
        }

        return $cdrRecords; // Return the updated CDR records
    }

    private function fetchClientInhouseServices($client_id)
    {
        return  ClientInhouseServiceUsage::where('client_id', $client_id)
            ->pluck('additional_service_id as client_inhouse_service_id')
            ->toArray();
    }

    private function createBillingRecord($client, $monthYear)
    {
        $totalDuration = 0;
        $totalPayment = 0;

        $billing = Billing::create([
            'client_id' => $client->id,
            'year' => Carbon::parse($monthYear)->format('Y'),
            'month' => Carbon::parse($monthYear)->format('m'),
            'total_duration' => $totalDuration,
            'total_payment' => $totalPayment,
            'payment_status' => 'unpaid',
            'currency' => $this->getCurrency(),
            'created_at' => now(),
        ]);

        // Get settings
        $settings = Setting::whereIn('param', ['invoice_prefix_index_1', 'invoice_prefix_index_2'])->get();

        // Retrieve the values
        $index_1 = $settings->firstWhere('param', 'invoice_prefix_index_1') ? $settings->firstWhere('param', 'invoice_prefix_index_1')->value : '';
        $index_2 = $settings->firstWhere('param', 'invoice_prefix_index_2') ? $settings->firstWhere('param', 'invoice_prefix_index_2')->value : '';

        // Create the UUID
        $uuid = $index_1 . '-' . $index_2 . '-' . $billing->id;

        // Update the billing record with the UUID
        Billing::where('id', $billing->id)->update(['uuid' => $uuid]);

        // Fetch the updated billing record
        $fresh_billing = Billing::find($billing->id);

        return $fresh_billing;
    }

    private function additionalBillingDetails($billingRecord, $client)
    {
        $inhouseServices = ClientInhouseServiceUsage::join('additional_services', 'additional_services.id', '=', 'client_inhouse_service_usage.additional_service_id')
            ->where('client_id', $client->id)
            ->select(
                'additional_services.title',
                'additional_services.description as additional_description',
                'additional_services.currency',
                'client_inhouse_service_usage.*'
            )->get();

        $additionalBillDetails = [];
        $totalBill = 0;
        $currency = $this->getCurrency();

        // Get the current month and year from the billing record
        $billingMonth = $billingRecord->month;
        $billingYear = $billingRecord->year;

        // Create a Carbon date from the billing month and year (start with the 1st day of the current month)
        $billingDate = Carbon::create($billingYear, $billingMonth, 1);

        // Add one month to the billing date
        $billingDate->addMonth();

        // Format the start and end dates (optional, if needed in a specific format)
        $startDateFormatted = $billingDate->format('Y-m-d');
        $endDateFormatted = $billingDate->endOfMonth()->format('Y-m-d'); // End of the next month
        Log::info('Start and end dates:', ['start' => $startDateFormatted, 'end' => $endDateFormatted]);

        if ($inhouseServices) {
            foreach ($inhouseServices as $inhouseService) {
                $rate = $inhouseService->rate;
                $quantity = $inhouseService->quantity;

                if (strtolower($inhouseService->title) == 'incoming number') {
                    $clientIncomingNumbers = $client->client_incoming_number ? explode(',', $client->client_incoming_number) : [];

                    foreach ($clientIncomingNumbers as $phone) {
                        $totalBill += $rate * $quantity;
                        $additionalBillDetails[] = [
                            'additional_service_id' => $inhouseService->additional_service_id,
                            'description' => $phone,
                            'quantity' => $quantity,
                            'rate' => $rate,
                            'start_from' => $startDateFormatted,
                            'end_to' => $endDateFormatted,
                            'frequency' => $client->frequency,
                            'currency' => $currency,
                        ];
                    }
                } elseif (strtolower($inhouseService->title) == 'pstn line rental') {
                    $totalBill += $rate * $quantity;
                    $additionalBillDetails[] = [
                        'additional_service_id' => $inhouseService->additional_service_id,
                        'description' => $inhouseService->description,
                        'quantity' => $quantity,
                        'rate' => $rate,
                        'start_from' => $startDateFormatted,
                        'end_to' => $endDateFormatted,
                        'frequency' => $client->frequency,
                        'currency' => $currency,
                    ];
                } else {
                    $totalBill += $rate * $quantity;
                    $additionalBillDetails[] = [
                        'additional_service_id' => $inhouseService->additional_service_id,
                        'description' => $inhouseService->additional_description,
                        'quantity' => $quantity,
                        'rate' => $rate,
                        'start_from' => $startDateFormatted,
                        'end_to' => $endDateFormatted,
                        'frequency' => $client->frequency,
                        'currency' => $currency,
                    ];
                }
            }

            // Save additional billing details
            foreach ($additionalBillDetails as $detail) {
                AdditionalBillingDetail::create([
                    'bill_id' => $billingRecord->id,
                    'additional_service_id' => $detail['additional_service_id'],
                    'description' => $detail['description'],
                    'quantity' => $detail['quantity'],
                    'start_from' => $detail['start_from'],
                    'end_to' => $detail['end_to'],
                    'rate' => $detail['rate'],
                    'total' => ($detail['rate'] * $detail['quantity']),
                    'frequency' => $detail['frequency'],
                    'currency' => $detail['currency'],
                ]);
            }

            $billingRecord->update(['total_payment' => $billingRecord->total_payment + $totalBill]);
            return $billingRecord;
        } else {
            return false;
        }
    }

    private function billingDetails($billingRecord, $cdrData)
    {
        // Initialize totals
        $totalDuration = 0;
        $totalBill = 0;
        $currency = $this->getCurrency();

        // Process CDR data
        foreach ($cdrData as $cdr) {
            $totalDuration += $cdr->duration_seconds;
            $totalBill += $cdr->bill_amount;

            BillingDetail::create([
                'bill_id' => $billingRecord->id,
                'to_number' => $cdr->to_number,
                'from_cli' => $cdr->from_cli,
                'simplified_to_descriptive' => $cdr->simplified_to_descriptive,
                'total_duration' =>  $cdr->duration_seconds,
                'total_amount' =>  $cdr->bill_amount,
                'currency' => $currency,
            ]);
        }

        $billingRecord->update([
            'total_duration' => $totalDuration,
            'total_payment' => $billingRecord->total_payment + $totalBill,
        ]);
        return $billingRecord;
    }

    private function applyTaxes($billID)
    {
        $taxes = Tax::where('status', 'enable')->get();
        $totalTaxAmount = 0;

        $billingRecord = Billing::where('id', $billID)->first();

        foreach ($taxes as $tax) {
            $taxValue = $tax->type === 'percentage'
                ? ($billingRecord->total_payment * $tax->rate) / 100
                : $tax->rate;

            $totalTaxAmount += $taxValue;

            TaxBillingDetail::create([
                'bill_id' => $billingRecord->id,
                'tax_type' => $tax->type,
                'tax_id' => $tax->id,
                'tax_name' => $tax->name,
                'tax_rate' => $tax->rate,
                'tax_amount' => $taxValue,
                'currency' => $this->getCurrency(),
            ]);
        }

        $billingRecord->update([
            'total_payment' => $billingRecord->total_payment + $totalTaxAmount
        ]);
        return $billingRecord;
    }

    // private function fetchCdrDataForFixedLine($numbers, $trunk, $monthYear, $billingRecord)
    // {
    //     // Extract year and month from the monthYear parameter
    //     $year = substr($monthYear, 0, 4);
    //     $month = substr($monthYear, 5, 2);

    //     $cdrRecords = [];
    //     // Start building the query
    //     $trunks = explode(',', $trunk);
    //     // Start building the query for CDRs based on trunk and date
    //     $cdrQuery = Cdr::whereIn('trunk', $trunks)
    //         ->whereYear('date', '=', $year)
    //         ->whereMonth('date', '=', $month);

    //     // If numbers are provided, apply filtering to to_number and from_cli
    //     if (!empty($numbers)) {
    //         $cdrQuery->where(function ($query) use ($numbers) {
    //             foreach ($numbers as $number) {
    //                 $query->orWhere('to_number', 'like', "%$number%")
    //                     ->orWhere('from_cli', 'like', "%$number%");
    //             }
    //         });
    //     }

    //     $cdrRecords = $cdrQuery->get();

    //     // Process CDR data
    //     if ($cdrRecords) {
    //         // Initialize totals
    //         $totalDuration = 0;
    //         $totalBill = 0;
    //         $currency = $this->getCurrency();

    //         foreach ($cdrRecords as $cdr) {
    //             $totalDuration += $cdr->duration_seconds;
    //             $totalBill += $cdr->bill_amount;

    //             clientFixedLineServicesBillDetails::create([
    //                 'bill_id' => $billingRecord->id,
    //                 'to_number' => $cdr->to_number,
    //                 'from_cli' => $cdr->from_cli,
    //                 'simplified_to_descriptive' => $cdr->simplified_to_descriptive,
    //                 'total_duration' =>  $cdr->duration_seconds,
    //                 'total_amount' =>  $cdr->bill_amount,
    //                 'currency' => $currency,
    //             ]);
    //         }


    //         // Execute and return the query result
    //         $billingRecord->update([
    //             'total_duration' => $totalDuration,
    //             'total_payment' => $billingRecord->total_payment + $totalBill,
    //         ]);

    //         return true;
    //     }

    //     return false;
    // }

    private function fetchCdrDataForFixedLine($numbers, $trunk, $monthYear, $billingRecord)
    {
        // Extract year and month from the monthYear parameter
        $year = substr($monthYear, 0, 4);
        $month = substr($monthYear, 5, 2);

        try {
            // Split trunk into an array (if it's not already an array)
            $trunks = is_array($trunk) ? $trunk : explode(',', $trunk);

            // Validate trunks array
            if (empty($trunks)) {
                Log::warning('No trunks provided for fixed-line services.', [
                    'client_id' => $billingRecord->client_id,
                    'month_year' => $monthYear,
                ]);
                return false;
            }

            $cdrFixedRecords = [];

            // Start building the query for CDRs based on trunk and date
            $cdrFixedQuery = Cdr::whereIn('trunk', $trunks)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month);

            // If numbers are provided, filter by to_number and from_cli
            if (is_array($numbers) && !empty(array_filter($numbers))) {
                $cdrFixedQuery->where(function ($query) use ($numbers) {
                    foreach ($numbers as $number) {
                        if (!empty($number)) { // Ensure each number is not empty
                            $query->orWhere('to_number', 'like', "%$number%")
                                ->orWhere('from_cli', 'like', "%$number%");
                        }
                    }
                });
            }

            // Ensure the query returns unique records
            $cdrFixedRecords = $cdrFixedQuery->distinct()->get();

            // Check if CDR records are empty
            if ($cdrFixedRecords->isEmpty()) {
                Log::warning('No CDR records found for fixed-line services.', [
                    'client_id' => $billingRecord->client_id,
                    'month_year' => $monthYear,
                ]);
                return false; // Return boolean false
            }

            // Initialize totals
            $totalDuration = 0;
            $totalBill = 0;
            $currency = $this->getCurrency();

            // Process CDR records
            foreach ($cdrFixedRecords as $cdr) {
                $totalDuration += $cdr->duration_seconds;
                $totalBill += $cdr->bill_amount;

                // Create fixed-line service bill details
                clientFixedLineServicesBillDetails::create([
                    'bill_id' => $billingRecord->id,
                    'to_number' => $cdr->to_number,
                    'from_cli' => $cdr->from_cli,
                    'simplified_to_descriptive' => $cdr->simplified_to_descriptive,
                    'total_duration' => $cdr->duration_seconds,
                    'total_amount' => $cdr->bill_amount,
                    'currency' => $currency,
                ]);
            }

            // Update billing record with totals
            $billingRecord->update([
                'total_duration' => $totalDuration,
                'total_payment' => $billingRecord->total_payment + $totalBill,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching CDR data for fixed-line services:', [
                'client_id' => $billingRecord->client_id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function destroy(Request $request)
    {
        try {
            // Validate input: Ensure that ids is an array
            $ids = $request->input('ids');
            if (empty($ids) || !is_array($ids)) {
                return response()->json(['success' => false, 'message' => 'No bill IDs provided or invalid data'], 422);
            }

            // Find the billings by ids
            $billings = Billing::whereIn('id', $ids)->get();

            if ($billings->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No bill found!'], 422);
            }

            // Loop through each billing record and delete related records
            foreach ($billings as $billing) {
                AdditionalBillingDetail::where('bill_id', $billing->id)->delete();
                BillingDetail::where('bill_id', $billing->id)->delete();
                TaxBillingDetail::where('bill_id', $billing->id)->delete();
                clientFixedLineServicesBillDetails::where('bill_id', $billing->id)->delete();
                ClientCDRHistory::where('bill_id', $billing->id)->delete();

                // If PDF file exists, delete it
                if ($billing->pdf_file_name != null) {
                    $pdfPath = public_path('bills/' . $billing->pdf_file_name);
                    if (file_exists($pdfPath)) {
                        unlink($pdfPath);
                    }
                }

                $billing->delete();
            }

            // Return a success response after deleting all
            return response()->json(['success' => true, 'message' => 'Bill deleted successfully'], 200);
        } catch (\Exception $exception) {
            // Log the exception (for debugging purposes)
            Log::error('Error deleting bill: ' . $exception->getMessage());

            // Return a failure response with error message
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 400);
        }
    }

    public function show($file) {}

    public function pdfMaker($id)
    {
        $billing = Billing::with('client', 'billing_details', 'additional_billing_details', 'tax_billing_details', 'client_fixedLineService_bill_details')
            ->where('id', $id)
            ->first();

        if (!$billing) {
            return response()->json(['error' => 'Bill not found!'], 404);
        }

        // Group billing details by simplified_to_descriptive and calculate totals
        $groupedBillingDetails = $billing->billing_details->groupBy('simplified_to_descriptive')
            ->map(function ($group) {
                $currency_symbol = $this->getCurrencySymbol($group->first()->currency);
                $duration = $group->sum('total_duration');
                return [
                    'simplified_to_descriptive' => $group->first()->simplified_to_descriptive,
                    'total_duration' => $duration,
                    'total_amount' => $currency_symbol . number_format($group->sum('total_amount'), 2),
                    'count' => $group->count(),
                ];
            });

        // Group billing details by simplified_to_descriptive and calculate totals
        $groupedFixedLineServiceBillingDetails = $billing->client_fixedLineService_bill_details->groupBy('simplified_to_descriptive')
            ->map(function ($group) {
                $currency_symbol = $this->getCurrencySymbol($group->first()->currency);
                $duration = $group->sum('total_duration');
                return [
                    'simplified_to_descriptive' => $group->first()->simplified_to_descriptive,
                    'total_duration' => $duration,
                    'total_amount' => $currency_symbol . number_format($group->sum('total_amount'), 2),
                    'count' => $group->count(),
                ];
            });

        $groupedAdditionalBillDetails = $billing->additional_billing_details->map(function ($adBD) {
            $currency_symbol = $this->getCurrencySymbol($adBD->currency);
            $additional_service_name = $this->getServiceName($adBD->additional_service_id);

            return [
                'rate' => $currency_symbol . $adBD->rate,
                'frequency' => $adBD->frequency,
                'description' => $adBD->description,
                'item_name' => $additional_service_name,
                'qty' => $adBD->quantity,
                'total_amount' => $adBD->total,
                'start_from' => $adBD->start_from,
                'end_to' => $adBD->end_to,
            ];
        });

        $total_additional_bill = $billing->additional_billing_details->sum('total');
        $total_call_bill_amount = round($billing->billing_details->sum('total_amount'), 2);
        $total_tax_amount = $billing->tax_billing_details->sum('tax_amount');
        $total_fixedLineService_amount = round($billing->client_fixedLineService_bill_details->sum('total_amount'), 2);
        $currency = $this->getCurrencySymbol($billing->currency);

        $settings = Setting::all();

        $data = [
            'billing' => $billing,
            'client' => $billing->client,
            'groupedBillingDetails' => $groupedBillingDetails,
            'groupedFixedLineServiceBillingDetails' => $groupedFixedLineServiceBillingDetails,
            'settings' => $settings,
            'total_additional_bill' => $total_additional_bill,
            'total_call_bill_amount' => $total_call_bill_amount,
            'total_tax_amount' => $total_tax_amount,
            'total_fixedLineService_amount' => $total_fixedLineService_amount,
            'currency' => $currency,
            'groupedAdditionalBillDetails' => $groupedAdditionalBillDetails,
        ];

        // Generate PDF in a background job
        dispatch(new GenerateBillingPDF($data));

        return response()->json(['message' => 'PDF generation in progress.']);
    }

    public function togglePaymentStatus(Request $request)
    {
        $billing = Billing::find($request->id); // Find the billing by ID

        if ($billing) {
            if ($billing->payment_status == 'unpaid') {
                // Toggle the payment status
                $billing->payment_status = 'paid';
                $billing->save();
            }

            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Status not updated']);
    }

    function getServiceName($id)
    {
        $service = AdditionalService::where('id', $id)->first();
        if ($service) {
            return $service->title;
        }
        return ''; // Handle cases where currency symbol is not found
    }

    function getCurrencySymbol($value)
    {
        $currency = CurrencyList::where('code', $value)->first();
        if ($currency) {
            return $currency->symbol;
        }
        return ''; // Handle cases where currency symbol is not found
    }

    function secondsToTimeFormat($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
    }

    public function pdfData($id)
    {
        $billing = Billing::with('client', 'billing_details', 'additional_billing_details', 'tax_billing_details')
            ->where('pdf_file_name', $id)
            ->first();

        return response()->file(public_path('bills/' . $billing->pdf_file_name), ['content-type' => 'application/pdf']);
    }

    public function downloadBillDetails($id)
    {
        $history = ClientCDRHistory::where('bill_id', $id)->get();
        $billing = Billing::find($id);
        $uuid = $billing ? $billing->uuid : 'N/A';
        if ($history->isEmpty()) {
            return back()->with('error', 'No billing records found');
        }

        // Initialize sum variables as floats
        $totalBillAmount = 0.00;
        $totalSeconds = 0; // We'll sum in seconds then convert

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="CALL_SUMMARY_DETAILS_' . $uuid . '.csv"',
        ];

        return response()->stream(function () use ($history, &$totalBillAmount, &$totalSeconds) {
            $handle = fopen('php://output', 'w');

            // Add BOM to fix UTF-8 in Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV headers
            fputcsv($handle, [
                'CDR ID',
                'Reference',
                'Trunk',
                'Tag',
                'Date',
                'Time',
                'From CLI',
                'From Descriptive',
                'To Number',
                'To Descriptive',
                'Simplified to Descriptive',
                'Destination ID',
                'Duration Seconds',
                'Billable Duration Seconds',
                'Peak Duration',
                'Off Peak Duration',
                'Weekend Duration',
                'Peak Rate',
                'Off Peak Rate',
                'Weekend Rate',
                'Connection Rate',
                'Currency',
                'Bill Amount',
                'Calculated Duration',
                'Provider'
            ]);

            // CSV data rows
            foreach ($history as $row) {
                $provider = CdrProvider::find($row->provider_id);

                // Convert null values to empty string for CSV
                $providerName = $provider ? $provider->name : '';

                // Convert HH:MM:SS to seconds
                $durationParts = explode(':', $row->calculated_duration);
                $hours = (int)$durationParts[0];
                $minutes = (int)$durationParts[1];
                $seconds = (int)$durationParts[2];
                $durationInSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;

                $totalSeconds += $durationInSeconds;
                $totalBillAmount += (float)($row->bill_amount ?? 0);

                fputcsv($handle, [
                    $row->cdr_id ?? '',
                    $row->reference ?? '',
                    $row->trunk ?? '',
                    $row->tag ?? '',
                    $row->date ?? '',
                    $row->time ?? '',
                    $row->from_cli ?? '',
                    $row->from_descriptive ?? '',
                    $row->to_number ?? '',
                    $row->to_descriptive ?? '',
                    $row->simplified_to_descriptive ?? '',
                    $row->destination_id ?? '',
                    $row->duration_seconds ?? '',
                    $row->billable_duration_seconds ?? '',
                    $row->peak_duration ?? '',
                    $row->off_peak_duration ?? '',
                    $row->weekend_duration ?? '',
                    $row->peak_rate ?? '',
                    $row->off_peak_rate ?? '',
                    $row->weekend_rate ?? '',
                    $row->connection_rate ?? '',
                    $row->currency ?? '',
                    $row->bill_amount ?? '',
                    $row->calculated_duration ?? '',
                    $providerName
                ]);
            }

            // Convert total seconds back to HH:MM:SS
            $totalHours = floor($totalSeconds / 3600);
            $remainingSeconds = $totalSeconds % 3600;
            $totalMinutes = floor($remainingSeconds / 60);
            $totalSecondsRemaining = $remainingSeconds % 60;
            $formattedTotalDuration = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSecondsRemaining);

            // Add summary row
            fputcsv($handle, []); // Empty row for separation
            fputcsv($handle, [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Total:',
                number_format($totalBillAmount, 2),
                $formattedTotalDuration, // Format duration as well
                ''
            ]);

            fclose($handle);
        }, 200, $headers);
    }
}
