<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalService;
use App\Models\CurrencyList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\CurrencyTrait;

class AdditionalServiceController extends Controller
{
    use CurrencyTrait;  // Use the CurrencyTrait

    public function index()
    {
        $currency = $this->currencySymbol();
        return view('admin.additional_services.index', compact('currency'));
    }

    public function getAdditionalServices()
    {
        if (request()->ajax()) {
            $services = AdditionalService::latest()->get();

            return DataTables::of($services)
                ->addIndexColumn()
                ->addColumn('name', function ($service) {
                    $name = ucfirst($service->title);
                    return $name;
                })
                ->addColumn('description', function ($service) {
                    $dec = $service->description;
                    return $dec;
                })
                ->addColumn('rate', function ($service) {
                    $symbol = $this->formatCurrency($service->rate);
                    return $symbol;
                })
                ->addColumn('frequency', function ($service) {
                    $freq = ucfirst($service->frequency);
                    return $freq;
                })
                ->addColumn('status', function ($service) {
                    $btn = '';
                    if ($service->status == "enable") {
                        $btn .= '<span class="badge badge-success">Enable</span>';
                    } else {
                        $btn .= '<span class="badge badge-danger">Disable</span>';
                    }
                    return $btn;
                })
                ->addColumn('action', function ($service) {
                    $editBtn = '<a href="#" class="btn btn-sm btn-primary" title="Edit" data-toggle="modal" onclick="editAdditionalService(' . $service->id . ', \'' . $service->title . '\', \'' . $service->description . '\', \'' . $service->rate . '\',\'' . $service->currency . '\',\'' . $service->frequency . '\')" data-target="#editModal" data-id="' . $service->id . '" data-name="' . $service->title . '"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';

                    $deleteBtn = '<a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="deleteAdditionalService(' . $service->id . ')"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;';
                    if ($service->status == 'enable') {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-info" title="Block Status" onclick="changeAdditionalServiceStatus(' . $service->id . ')"><i class="fa fa-ban"></i></a>';
                    } else {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-warning" title="Unblock Status" onclick="changeAdditionalServiceStatus(' . $service->id . ')"><i class="fa fa-check"></i></a>';
                    }
                    return $editBtn . $deleteBtn . $statusBtn;
                })

                ->rawColumns(['action', 'name', 'status', 'rate', 'description', 'sub_service', 'frequency'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255|unique:additional_services,title',
                'rate' => 'required',
                'frequency' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            AdditionalService::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'rate' => $request->rate,
                'frequency' => $request->frequency,
                'description' => $request->description,
                'currency' => $this->getCurrency()
            ]);

            DB::commit();
            return response()->json(['message' => 'Inhouse Service created successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function update(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'editServiceName' => 'required|max:255|unique:additional_services,title,' . $request->id,
                'editRate' => 'required',
                'editFrequency' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $service = AdditionalService::find($request->id);

            if ($service != null) {
                $service->update([
                    'title' => $request->editServiceName,
                    'description' => $request->editDescription,
                    'rate' => $request->editRate,
                    'frequency' => $request->editFrequency,
                    'currency' => $this->getCurrency()
                ]);
                return response()->json(['message' => 'Inhouse Service updated successfully'], 200);
            } else {
                return response()->json(['error' => 'Inhouse Service not found!'], 422);
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Something went wrong. Please try again!'], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $service = AdditionalService::find($id);
            if ($service != null) {
                $service->delete();
                return response()->json(['message' => 'Inhouse Service Deleted Successfully'], 200);
            } else {
                return response()->json(['error' => 'Inhouse Service not found!'], 422);
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Something went wrong!'], 400);
        }
    }

    public function changeStatus($id)
    {
        try {
            $service = AdditionalService::find($id);
            if ($service != null) {
                if ($service->status == 'enable') {
                    $service->update([
                        'status' => 'disable'
                    ]);
                    return response()->json(['message' => 'Inhouse Service Status changed successfully'], 200);
                } elseif ($service->status = 'disable') {
                    $service->update([
                        'status' => 'enable'
                    ]);
                    return response()->json(['message' => 'Inhouse Service Status changed successfully'], 200);
                }
            } else {
                return response()->json(['error' => 'Inhouse Service not found!'], 422);
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Something went wrong!'], 400);
        }
    }
}
