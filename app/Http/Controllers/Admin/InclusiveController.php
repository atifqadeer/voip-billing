<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalService;
use App\Models\Descriptive;
use Illuminate\Http\Request;
use App\Models\Inclusive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class InclusiveController extends Controller
{
    public function index()
    {
        $inhouseServices = AdditionalService::where('status','enable')->get();
        $descriptives = Descriptive::where('status','enable')->get();
        return view('admin.inclusives.index',compact('inhouseServices','descriptives'));
    }

    public function getInclusives()
    {
        if (request()->ajax()) {
            // Load inclusives with related inhouse service
            $inclusives = Inclusive::with('inhouseService')->latest()->get();
    
            return DataTables::of($inclusives)
                ->addIndexColumn()
                ->addColumn('inhouse_service', function ($inclusive) {
                    // Safely access relationship and handle null
                    return $inclusive->inhouseService ? $inclusive->inhouseService->title : 'N/A';
                })
                ->addColumn('skip_to', function ($inclusive) {
                    // Split the comma-separated string into an array
                    $skipToArray = explode(',', $inclusive->skip_to);
                
                    // Generate a badge for each word
                    $badges = array_map(function ($word) {
                        return '<span class="badge badge-secondary">' . trim($word) . '</span>';
                    }, $skipToArray);
                
                    // Join the badges with a space
                    return implode(' ', $badges);
                })
                
                ->addColumn('status', function ($inclusive) {
                    return $inclusive->status == 'enable'
                        ? '<span class="badge badge-success">Enable</span>'
                        : '<span class="badge badge-danger">Disable</span>';
                })
                ->addColumn('updated_at', function ($inclusive) {
                    return \Carbon\Carbon::parse($inclusive->updated_at)->format('M j Y, h:i A');
                })
                ->addColumn('action', function ($inclusive) {
                    $editBtn = '<a href="#" class="btn btn-sm btn-primary" id="editBtn" title="Edit" data-toggle="modal" data-target="#editModal" data-id="' . $inclusive->id . '" data-name="' . $inclusive->title . '"><i class="fa fa-edit"></i></a>&nbsp;';
                    $deleteBtn = '<a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="deleteCompany(' . $inclusive->id . ')"><i class="fa fa-trash"></i></a>&nbsp;';
                    $statusBtn = $inclusive->status == 'enable'
                        ? '<a href="#" class="btn btn-sm btn-info" title="Block Status" onclick="changeStatus(' . $inclusive->id . ')"><i class="fa fa-ban"></i></a>'
                        : '<a href="#" class="btn btn-sm btn-warning" title="Unblock Status" onclick="changeStatus(' . $inclusive->id . ')"><i class="fa fa-check"></i></a>';
    
                    return $editBtn . $deleteBtn . $statusBtn;
                })
                ->rawColumns(['action', 'inhouse_service', 'skip_to', 'status', 'updated_at'])
                ->make(true);
        }
    }
    
    public function edit(Request $request)
    {
        $id = $request->id;

        $data = Inclusive::findOrFail($id);

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {   
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'inhouse_services_id' => 'required|unique:inclusives,inhouse_service_id',
            'skipToDescriptives' => 'required',
        ],
        [
            'inhouse_services_id.required' => 'Inhouse Service is required.',
            'inhouse_services_id.exists' => 'This id is already exists.',
            'skipToDescriptives.required' => 'Skip to descriptives are required.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Prepare data
        $inhouse_services_id = $requestData['inhouse_services_id'];
        $skipTo = $requestData['skipToDescriptives'];
    
        try {
            // Insert data into the database
            Inclusive::create([
                'inhouse_service_id' => $inhouse_services_id,
                'skip_to' => implode(',', $skipTo), // Convert the array back to a comma-separated string
            ]);
    
            return response()->json(['success' => true, 'message' => 'Inclusives saved successfully!']);
        } catch (\Exception $e) {
            // Log and handle errors
            Log::error('Error saving inclusives:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to save inclusives.'], 500);
        }
    }

    public function update(Request $request)
    {
        try{
            $requestData = $request->all();
            $validator = Validator::make($requestData, [
                'edit_inhouse_services_id' => 'required|unique:inclusives,id,'.$requestData['id'],
                'edit_skipToDescriptives' => 'required',
            ],
            [
                'edit_inhouse_services_id.required' => 'Inhouse Service is required.',
                'edit_inhouse_services_id.exists' => 'This id is already exists.',
                'edit_skipToDescriptives.required' => 'Skip to descriptives are required.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $inclusive = Inclusive::find($requestData['id']);

            $inclusive->update([
                'inhouse_service_id' => $requestData['edit_inhouse_services_id'],
                'skip_to' => implode(',', $requestData['edit_skipToDescriptives']),
            ]);

            return response()->json(['message' => 'Inclusive updated successfully']);

        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $inclusive = Inclusive::find($request->id);
            if ($inclusive){
                $inclusive->delete();

                return response()->json(['message' => 'Inclusive deleted successfully'], 200);

            }else{
                return response()->json(['error' => 'Inclusive id not found!'], 422);
            }
        }catch (\Exception $exception){
            return response()->json(['error' => 'Something went wrong!'], 400);

        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $inclusive = Inclusive::find($request->id);
            if ($inclusive != null){
                if ($inclusive->status == 'enable') {
                    $inclusive->update([
                        'status' => 'disable'
                    ]);

                }elseif($inclusive->status == 'disable'){
                    $inclusive->update([
                        'status' => 'enable'
                    ]);
                }

                return response()->json(['success'=>true,'message' => 'Status changed successfully!'], 200);
            }else{
                return response()->json(['success'=>false,'message' => 'Inclusive not found!'], 422);
            }
        }catch (\Exception $exception){
            return response()->json(['error' => 'Something went wrong!'], 400);

        }
    }
}
