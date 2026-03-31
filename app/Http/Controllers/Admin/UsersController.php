<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalService;
use App\Models\Client;
use App\Models\Company;
use App\Models\ClientServiceUsage;
use App\Models\CompanyServices;
use App\Models\Services;
use App\Models\ClientInhouseServiceUsage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Traits\CurrencyTrait;

class UsersController extends Controller
{
    use CurrencyTrait;  // Use the CurrencyTrait

    public function index()
    {
        $inhouseServices = AdditionalService::where('status', 'enable')->orderBy('title', 'asc')->get();
        $currency = $this->currencySymbol();

        $companies = Company::where('status', 'enable')->orderBy('company_name', 'asc')->get();
        return view('admin.clients.index', compact(['companies', 'currency', 'inhouseServices']));
    }

    public function getClients()
    {
        if (request()->ajax()) {
            $clients = Client::with('clientServiceUsages')->orderBy('updated_at', 'desc')->get();

            foreach ($clients as $client) {
                foreach ($client->clientServiceUsages as $usage) {
                    // Fetch the service title for each usage
                    $service = Services::where('id', $usage->service_id)->first();
                    $usage->service_title = $service ? $service->title : '';
                }
            }

            return DataTables::of($clients)
                ->addIndexColumn()
                // Add columns according to your data structure

                ->addColumn('name', function ($client) {
                    $name = ucwords($client->client_name);
                    return $name;
                })
                ->addColumn('frequency', function ($client) {
                    $frequency = ucwords($client->frequency);
                    return $frequency;
                })
                ->addColumn('email', function ($client) {
                    $email = strtolower($client->client_email);
                    return $email;
                })
                ->addColumn('tag_name', function ($client) {
                    $dec = $client->tag_name ? ucwords($client->tag_name) : '-';
                    return $dec;
                })
                // ->addColumn('phone', function ($client) {
                //     // Split phone numbers by comma and wrap each in a badge
                //     $phones = explode(',', $client->client_phone_number);
                //     return implode(' ', array_map(function ($phone) {
                //         return '<span class="badge badge-secondary">' . trim($phone) . '</span>';
                //     }, $phones));
                // })
                ->addColumn('client_outgoing_number', function ($client) {
                    // Split landline numbers by comma and wrap each in a badge
                    $numbers = explode(',', $client->client_outgoing_number);
                    return implode(' ', array_map(function ($number) {
                        return '<span class="badge badge-secondary">' . trim($number) . '</span>';
                    }, $numbers));
                })
                ->addColumn('client_incoming_number', function ($client) {
                    // Split landline numbers by comma and wrap each in a badge
                    $numbers = explode(',', $client->client_incoming_number);
                    return implode(' ', array_map(function ($number) {
                        return '<span class="badge badge-secondary">' . trim($number) . '</span>';
                    }, $numbers));
                })
                ->addColumn('status', function ($client) {
                    $btn = '';
                    if ($client->status == "enable") {
                        $btn .= '<span class="badge badge-success">Enable</span>';
                    } else {
                        $btn .= '<span class="badge badge-danger">Disable</span>';
                    }
                    return $btn;
                })
                ->addColumn('updated_at', function ($row) {
                    $date = Carbon::parse($row->updated_at)->format('d M Y, h:i A'); // Example format
                    return $date;
                })

                ->addColumn('services', function ($client) {
                    $services = $client->services;

                    // Map each service to create a badge
                    $servicesData = $services->map(function ($service) {
                        return '<span class="badge badge-info p-1">' . ucwords($service->title) . '</span>';
                    });

                    // Join all badges with a comma or any separator you prefer
                    $conclientnatedBadges = $servicesData->implode(' ');  // Space between badges

                    // Return the concatenated badges as HTML
                    return $conclientnatedBadges;
                })
                ->addColumn('company_ids', function ($client) {
                    $clientServiceUsages = $client->clientServiceUsages;

                    // Initialize an array to store company IDs
                    $companyIds = [];

                    // Use a foreach loop to extract company_id
                    foreach ($clientServiceUsages as $usage) {
                        $companyIds[] = $usage['company_id']; // Extract the company_id
                    }

                    // Get unique company IDs
                    $uniqueCompanyIds = array_unique($companyIds);

                    // Return the unique company IDs as an array (or a string if needed)
                    return $uniqueCompanyIds; // Return as an array
                })
                ->addColumn('service_ids', function ($client) {
                    $clientServiceUsages = $client->clientServiceUsages;

                    // Initialize an array to store company IDs
                    $serviceIds = [];

                    // Use a foreach loop to extract company_id
                    foreach ($clientServiceUsages as $usage) {
                        $serviceIds[] = $usage['service_id']; // Extract the company_id
                    }

                    // Get unique company IDs
                    $uniqueServiceIds = array_unique($serviceIds);

                    // Return the unique company IDs as an array (or a string if needed)
                    return $uniqueServiceIds; // Return as an array
                })

                ->rawColumns(['services'])
                ->addColumn('action', function ($client) {
                    $editBtn = '<a href="#" class="btn btn-sm btn-primary" id="editBtn" title="Edit" data-toggle="modal"  data-target="#editModal" data-id="' . $client->id . '" data-name="' . $client->title . '"><i class="fa fa-edit"></i></a>&nbsp;';
                    $setupBtn = '<a href="#" class="btn btn-sm btn-secondary" id="setupBtn" title="Services Setup" data-toggle="modal"  data-target="#setupModal" data-id="' . $client->id . '" data-name="' . $client->title . '"><i class="fa fa-gear"></i></a>&nbsp;';
                    $inhouseSetupBtn = '<a href="#" class="btn btn-sm btn-secondary" id="inhouseSetupBtn" title="Inhouse Services Setup" data-toggle="modal"  data-target="#inhouseSetupModal" data-id="' . $client->id . '" data-name="' . $client->title . '"><i class="fa fa-home"></i></a>&nbsp;';

                    $deleteBtn = '<a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="deleteClient(' . $client->id . ')"><i class="fa fa-trash"></i></a>&nbsp;';
                    if ($client->status == 'enable') {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-info" title="Block Status" onclick="changeStatus(' . $client->id . ')"><i class="fa fa-ban"></i></a>';
                    } else {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-warning" title="Unblock Status" onclick="changeStatus(' . $client->id . ')"><i class="fa fa-check"></i></a>';
                    }
                    return $editBtn . $setupBtn . $inhouseSetupBtn . $deleteBtn . $statusBtn;
                })
                ->rawColumns(['action', 'name', 'status', 'frequency', 'services', 'tag_name', 'client_outgoing_number', 'client_incoming_number', 'email', 'landline_number', 'updated_at'])
                ->make(true);
        }
    }

    public function getClientByID(Request $request)
    {

        $clientID = $request->client_id;
        $client = Client::with('clientServiceUsages', 'clientInHouseServices')->find($clientID);

        return $client;
    }

    public function getClientInhouseServicesByID(Request $request)
    {

        $clientID = $request->client_id;
        $client = Client::with('clientInHouseServices')->select('clients.id')->find($clientID);

        foreach ($client->clientInHouseServices as $service) {
            // Fetch the service title for each usage
            $serviceData = AdditionalService::where('id', $service->additional_service_id)->first();
            $service->service_title = $serviceData ? $serviceData->title : '';
            $service->rate = $service ? $service->rate : $serviceData->rate;
            $service->description = $service->description ? $service->description : '';
        }

        return $client;
    }

    public function getClientServicesByID(Request $request)
    {

        $clientID = $request->client_id;
        $client = Client::with('clientServiceUsages')->select('clients.id')->find($clientID);

        foreach ($client->clientServiceUsages as $usage) {
            // Fetch the service title for each usage
            $service = Services::where('id', $usage->service_id)->first();
            $usage->service_title = $service ? $service->title : '';
        }

        return $client;
    }

    public function saveClientInhouseServices(Request $request)
    {
        // Validate the input data
        $request->validate([
            'service_id.*' => 'required',
        ]);

        // Retrieve the service IDs and rates
        $serviceIds = $request->input('service_id');
        $rates = $request->input('rate');
        $qty = $request->input('qty');
        $charges_description = $request->input('description');
        // Get the input dates array
        $dates = $request->input('dates'); // dates[]: 2024-12-10 / 2024-12-10

        // Initialize empty arrays for start and end dates
        $startDates = [];
        $endDates = [];

        // Loop through the dates array and process each date range
        foreach ($dates as $dateRange) {
            // Split the date range by ' / '
            $datesParts = explode(' / ', $dateRange);

            // Check if we have exactly 2 parts (start date and end date)
            if (count($datesParts) === 2) {
                $startDates[] = $datesParts[0];  // First part is the start date
                $endDates[] = $datesParts[1];    // Second part is the end date
            } else {
                // Handle cases where the date range doesn't match the expected format
                // For example, if the dates array contains just one date without a range
                $startDates[] = $datesParts[0];
                $endDates[] = $datesParts[0];  // If there's only one date, treat it as both start and end date
            }
        }

        // Loop through each service and rate to update
        foreach ($serviceIds as $index => $serviceId) {
            // Find the record in the ClientServiceUsage table based on service_id and client_id
            $serviceUsage = ClientInhouseServiceUsage::where('additional_service_id', $serviceId)
                ->where('client_id', $request->client_id)
                ->first();

            // If the service usage exists, update the rate
            if ($serviceUsage) {
                $serviceUsage->update([
                    'quantity' => $qty[$index],
                    'rate' => $rates[$index],
                    'description' => $charges_description[$index],
                    'start_from' => $startDates[$index],
                    'end_to' => $endDates[$index]
                ]);
            }
        }

        // Return a success response
        return response()->json(['success' => true, 'message' => 'Inhouse Services rates updated successfully!']);
    }

    public function saveClientServices(Request $request)
    {

        // Validate the input data
        $request->validate([
            'service_id.*' => 'required|exists:services,id',
        ]);

        // Retrieve the service IDs and rates
        $serviceIds = $request->input('service_id');
        $rates = $request->input('service_rate');
        $percentage = $request->input('service_percentage');
        $charges_description = $request->input('charges_description');

        // Loop through each service and rate to update
        foreach ($serviceIds as $index => $serviceId) {
            // Find the record in the ClientServiceUsage table based on service_id and client_id
            $serviceUsage = ClientServiceUsage::where('service_id', $serviceId)
                ->where('client_id', $request->client_id)
                ->first();

            // If the service usage exists, update the rate
            if ($serviceUsage) {
                $serviceUsage->update([
                    'fixed_rate' => $rates[$index],
                    'percentage' => $percentage[$index],
                    'charges_description' => $charges_description[$index]
                ]);
            }
        }

        // Return a success response
        return response()->json(['success' => true, 'message' => 'Services rates updated successfully!']);
    }

    public function deleteClientByID(Request $request)
    {
        $clientID = $request->client_id;
        $client = Client::findOrFail($clientID);
        if ($client) {
            $client->update(['is_deleted' => '1']);

            return response()->json(['success' => 'true', 'message' => 'Client deleted successfully']);
        }

        return response()->json(['success' => 'false', 'message' => 'Client not found']);
    }

    public function changeClientStatus($id)
    {

        try {
            $client = Client::findOrFail($id);
            if ($client) {

                if ($client->status == 'enable') {
                    $client->update(['status' => 'disable']);
                } elseif ($client->status == 'disable') {
                    $client->update(['status' => 'enable']);
                }
                return response()->json(['message' => 'Client status changed successfully'], 200);
            } else {
                return response()->json(['error' => 'Client not found!'], 422);
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception], 400);
        }
    }

    public function store(Request $request)
    {
        try {

            DB::beginTransaction();
            // Validate the incoming request data
            $validator = Validator::make(
                $request->all(),
                [
                    'clientAccountNumber' => 'required|string|max:255|unique:clients,account_number',
                    'clientName' => 'required|string|max:255',
                    'clientEmail' => 'nullable|email|unique:clients,client_email|max:255',
                    'outgoing_number' => 'nullable|string|max:255',
                    'incoming_number' => 'nullable|string|max:255',
                    'companies' => 'required|array|min:1',
                    'companies.*' => 'required|string|max:255',
                    'inhouseServices' => 'nullable|array',
                    'services' => 'required|array',
                    'services.*' => 'exists:services,id',
                    'address' => 'nullable|string|max:255',
                    'frequency' => 'required',
                    'trunkID' => 'required',
                ],
                [
                    'clientAccountNumber.required' => 'Client account number is required.',
                    'clientName.required' => 'Client name is required.',
                    'clientEmail.email' => 'Please enter a valid email address.',
                    'clientEmail.unique' => 'This email is already in use.',
                    'companies.required' => 'Company is required.',
                    // 'inhouseServices.required' => 'Inhouse Services are required.',
                    'services.exists' => 'One or more selected services are invalid.',
                    'services.required' => 'Service is required.',
                    'frequency.required' => 'Frequency is required.',
                    'trunkID.required' => 'Trunk ID is required.'
                ]
            );

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $client = Client::create([
                'user_id' => auth()->user()->id,
                'account_number' => $request->clientAccountNumber,
                'client_name' => $request->clientName,
                'client_email' => $request->clientEmail,
                'client_phone_number' => $request->phone,
                'client_outgoing_number' => $request->outgoing_number,
                'client_incoming_number' => $request->incoming_number,
                'client_address' => $request->address,
                'tag_name' => $request->tag_name,
                'trunk_number' => $request->trunkID,
                'notes' => $request->note,
                'is_enable_vat_tax' => $request->enableVatTaxValue,
                'is_enable_fixed_line_services' => $request->fixedLineServiceValue,
                'fixed_line_service_number' => $request->fixedLineServiceValue == '1' ? $request->fixedLineNumber : null,
                'frequency' => $request->frequency,
                'added_date' => Carbon::now()->format('Y-m-d'),
                'added_time' => Carbon::now()->format('H:i:s'),
            ]);

            // Iterate over companies and services to create client_service_usage entries
            $companies = $request->input('companies');
            $services = $request->input('services');

            foreach ($companies as $companyId) {
                foreach ($services as $serviceId) {
                    ClientServiceUsage::create([
                        'company_id' => $companyId,
                        'service_id' => $serviceId,
                        'client_id' => $client->id,
                    ]);
                }
            }

            // Iterate over companies and services to create client_service_usage entries
            $inhouseServices = $request->input('inhouseServices');

            if ($inhouseServices) {
                foreach ($inhouseServices as $service_id) {
                    $additionalService = AdditionalService::find($service_id);
                    ClientInhouseServiceUsage::create([
                        'additional_service_id' => $additionalService->id,
                        'client_id' => $client->id,
                        'rate' => $additionalService->rate
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Client created successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {

        DB::beginTransaction(); // Begin transaction
        try {
            $validator = Validator::make($request->all(), [
                'editClientAccount' => 'required|string|max:255|unique:clients,id,' . $request->editClientId,
                'editClientName' => 'required|string|max:255',
                'editClientEmail' => 'nullable|email|max:255|unique:clients,client_email,' . $request->editClientId,
                'editOutgoingNumber' => 'nullable|string|max:255',
                'editIncomingNumber' => 'nullable|string|max:255',
                'editCompanies' => 'required|array|min:1',
                'editCompanies.*' => 'exists:companies,id',
                'editServices' => 'required|array|min:1',
                'editServices.*' => 'exists:services,id',
                'editInhouseServices' => 'nullable|array',
                'editAddress' => 'nullable|string|max:255',
                'editClientFrequency' => 'required',
                'editTrunkID' => 'required',
            ], [
                'editClientAccount.required' => 'Client account number is required.',
                'editClientName.required' => 'Client name is required.',
                'editClientEmail.email' => 'Please enter a valid email address.',
                'editInhouseServices.required' => 'Inhouse Services are required.',
                'editClientEmail.unique' => 'This email is already in use.',
                'editCompanies.required' => 'At least one company is required.',
                'editServices.required' => 'At least one service is required.',
                'editClientFrequency.required' => 'Frequency is required.',
                'editTrunkID.required' => 'Trunk ID is required.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $client = Client::find($request->editClientId);

            if (!$client) {
                return response()->json(['message' => 'Client not found'], 404);
            }

            // Update client details
            $client->update([
                'account_number' => $request->editClientAccount,
                'client_name' => $request->editClientName,
                'client_email' => $request->editClientEmail,
                'client_phone_number' => $request->editPhone,
                'client_outgoing_number' => $request->editOutgoingNumber,
                'client_incoming_number' => $request->editIncomingNumber,
                'client_address' => $request->editAddress,
                'tag_name' => $request->editTagName,
                'trunk_number' => $request->editTrunkID,
                'notes' => $request->editNote,
                'is_enable_vat_tax' => $request->editEnableVatTaxValue,
                'is_enable_fixed_line_services' => $request->editFixedLineServiceValue,
                // Use a conditional operator to include fixed_line_service_number if needed
                'fixed_line_service_number' => $request->editFixedLineServiceValue == '1' ? $request->editFixedLineNumber : null,
                'frequency' => $request->editClientFrequency ? $request->editClientFrequency : 'monthly'
            ]);

            // Process service and company usage
            $requestedCompanies = $request->input('editCompanies');
            $requestedServices = $request->input('editServices');

            $toRetain = [];
            $requestedPairs = [];

            // Iterate through the requested companies and services
            foreach ($requestedCompanies as $companyId) {
                foreach ($requestedServices as $serviceId) {
                    // Find the service by serviceId
                    $companyService = CompanyServices::where('service_id', $serviceId)->first();

                    // Ensure the service exists, otherwise skip
                    if (!$companyService) {
                        continue; // Skip if the service does not exist
                    }

                    // Ensure the company_id is set correctly
                    $company_id = ($companyId == $companyService->company_id) ? $companyId : $companyService->company_id;

                    // Prepare the pair of company_id and service_id to check
                    $requestedPairs[] = ['company_id' => $company_id, 'service_id' => $serviceId];
                }
            }

            $editInhouseServices = $request->input('editInhouseServices');

            if (empty($editInhouseServices)) {
                // If the request is empty, remove all existing inhouse services for this client
                ClientInhouseServiceUsage::where('client_id', $client->id)->delete();
            } else {
                // Get all existing inhouse service IDs for this client
                $existingInhouseServices = ClientInhouseServiceUsage::where('client_id', $client->id)
                    ->pluck('additional_service_id')
                    ->toArray();

                // Find the IDs that are not in the existing list
                $newInhouseServices = array_diff($editInhouseServices, $existingInhouseServices);

                // Add the new inhouse services
                foreach ($newInhouseServices as $serviceId) {
                    $additionalService = AdditionalService::find($serviceId);

                    // Check if the additional service exists before trying to use its rate
                    if ($additionalService) {
                        ClientInhouseServiceUsage::create([
                            'additional_service_id' => $serviceId,
                            'client_id' => $client->id,
                            'rate' => $additionalService->rate
                        ]);
                    }
                }

                // Remove inhouse services that are not in the request
                ClientInhouseServiceUsage::where('client_id', $client->id)
                    ->whereNotIn('additional_service_id', $editInhouseServices)
                    ->delete();
            }

            // Loop through the requested pairs (service and company combinations)
            foreach ($requestedPairs as $pair) {
                // Check if the service already exists for this client and company
                $existing = ClientServiceUsage::where('client_id', $client->id)
                    ->where('company_id', $pair['company_id'])
                    ->where('service_id', $pair['service_id'])
                    ->first();

                // If it exists, add the ID to the retention list (do not insert into DB)
                if ($existing) {
                    $toRetain[] = $existing->id;
                } else {
                    // If it does not exist, prepare a new record for insertion
                    ClientServiceUsage::insert([
                        'company_id' => $pair['company_id'],
                        'service_id' => $pair['service_id'],
                        'client_id' => $client->id,
                        'sub_service_id' => null,  // Assuming sub_service_id is null for new records
                    ]);
                }
            }

            // Delete records that exist for the client but are not in the retention list
            if (!empty($toRetain)) {
                ClientServiceUsage::where('client_id', $client->id)
                    ->whereNotIn('id', $toRetain)
                    ->delete();
            }

            DB::commit(); // Commit transaction
            return response()->json(['message' => 'Client updated successfully'], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
