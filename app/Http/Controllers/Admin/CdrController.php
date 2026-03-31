<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cdr;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Illuminate\Support\Str;
use App\Jobs\ProcessCsvFile;

class CdrController extends Controller
{

    public function index()
    {
        $cdrProviders = DB::table('cdr_providers')->get();
        return view('admin.cdrs.index', compact('cdrProviders'));
    }

    public function getCdrs(Request $request)
    {
        if ($request->ajax()) {
            $query = Cdr::with('cdr_providers')->orderBy('cdrs.date', 'asc');
            $providerId = $request->input('provider');
            $statusFilter = $request->input('status');

            if ($request->has('month')) {
                $monthYear = $request->get('month');
                $year = Carbon::parse($monthYear)->format('Y');
                $month = Carbon::parse($monthYear)->format('m');

                // Filter by the extracted year and month
                $query->whereYear('cdrs.date', $year)
                    ->whereMonth('cdrs.date', $month);
            } else {
                // Default filter for the current month
                $currentMonth = Carbon::now();
                $query->whereYear('cdrs.date', $currentMonth->year)
                    ->whereMonth('cdrs.date', $currentMonth->month);
            }

            if ($providerId) {
                $query->where('cdrs.provider_id', $providerId);
            }

            if ($statusFilter == 'un_assigned') {
                $query->whereNull('cdrs.client_id');
            }elseif($statusFilter == 'assigned'){
                $query->whereNotNull('cdrs.client_id');
            }

            // Handle search queries
            if ($request->has('search') && !empty($request->get('search')['value'])) {
                $searchValue = $request->get('search')['value'];
                $query->where(function ($q) use ($searchValue) {
                    $q->where('cdrs.trunk', 'like', '%' . $searchValue . '%')
                        ->orWhere('cdrs.tag', 'like', '%' . $searchValue . '%')
                        ->orWhere('cdrs.from_cli', 'like', '%' . $searchValue . '%')
                        ->orWhere('cdrs.to_number', 'like', '%' . $searchValue . '%')
                        ->orWhere('cdrs.date', 'like', '%' . $searchValue . '%')
                        ->orWhere('cdrs.simplified_to_descriptive', 'like', '%' . $searchValue . '%')
                        ->orWhere('cdrs.reference', 'like', '%' . $searchValue . '%');
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('trunk', function ($cdr) {
                    return $cdr->trunk;
                })
                ->addColumn('provider_name', function ($cdr) {
                    return $cdr->cdr_providers ? $cdr->cdr_providers->name : '-';
                })
                ->addColumn('from_cli', function ($cdr) {
                    return $cdr->from_cli;
                })
                ->addColumn('to_number', function ($cdr) {
                    return $cdr->to_number;
                })
                ->addColumn('date', function ($cdr) {
                    // return Carbon::parse($cdr->date . ' ' . $cdr->time)->format('d-m-Y H:i:s');
                    return Carbon::parse($cdr->date)->format('d-m-Y');
                })
                ->addColumn('calculated_duration', function ($cdr) {
                    return $cdr->calculated_duration;
                })
                ->addColumn('sell_price', function ($cdr) {
                    return $cdr->selling_price;
                })
                ->addColumn('simplified_to_descriptive', function ($cdr) {
                    return $cdr->simplified_to_descriptive;
                })
                ->addColumn('currency', function ($cdr) {
                    return $cdr->currency;
                })
                ->addColumn('status', function ($cdr) {
                    $client_id = $cdr->client_id;
                    $status = '';
                    if($client_id == null){
                        $status = '<span class="badge badge-danger">Un-Assigned</span>';
                    }else{
                        $status = '<span class="badge badge-success">Assigned</span>';
                    }

                    return $status;
                })
                ->rawColumns(['date', 'from_cli', 'to_number', 'status','calculated_duration', 'sell_price', 'simplified_to_descriptive', 'currency'])
                ->make(true);
        }
    }

    public function importCSVFile(Request $request)
    {
        try {
            // Validate the CSV file
            $validator = Validator::make($request->all(), [
                'csv_file' => 'required|file|mimetypes:text/plain,text/csv,application/csv|max:20000',
                'provider_id' => 'required',
            ], [
                'csv_file.required' => 'Attachment is required.',
                'csv_file.mimetypes' => 'The file must be a type of: csv.',
                'csv_file.max' => 'The file must not be greater than 20MB.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Upload the file to the server temporarily
            $file = $request->file('csv_file');
            $path = $file->storeAs('uploads/csv', $file->getClientOriginalName());

            // Get the provider_id from the request
            $providerId = $request->input('provider_id');

            // Dispatch the file processing to a queue with provider_id
            ProcessCsvFile::dispatch($path, $providerId);

            // Return a response immediately
            return response()->json(['message' => 'File uploaded successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
