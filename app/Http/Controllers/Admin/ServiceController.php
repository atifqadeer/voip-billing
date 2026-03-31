<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Services;
use App\Models\Company;
use App\Models\CompanyServices;
use App\Models\SubServices;
use App\Models\AdditionalServiceCharges;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{

    public function index()
    {
        return view('admin.services.index');
    }

    public function getServices(){
        if (request()->ajax()) {
            $services =Services::latest()->get(); // Logic to retrieve categories data, e.g., from the database

            return DataTables::of($services)
                ->addIndexColumn()
                ->addColumn('name',function ($service){
                    $name = ucfirst($service->title);
                    return $name;
                })
                ->addColumn('description',function ($service){
                    $dec = $service->description;
                    return $dec;
                })
                ->addColumn('status',function ($service){
                    $btn = '';
                    if ($service->status=="enable"){
                        $btn .= '<span class="badge badge-success">Enable</span>';
                    }else{
                        $btn .= '<span class="badge badge-danger">Disable</span>';

                    }
                    return $btn;
                })
                ->addColumn('action', function ($service) {
                    $editBtn = '<a href="#" class="btn btn-sm btn-primary" title="Edit" data-toggle="modal" onclick="editService(' . $service->id . ', \'' . $service->title . '\', \'' . $service->description . '\')" data-target="#editModal" data-id="' . $service->id . '" data-name="' . $service->title . '"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';

                    $deleteBtn = '<a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="deleteService('.$service->id.')"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;';
                    if ($service->status == 'enable') {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-info" title="Block Status" onclick="changeStatus(' . $service->id . ')"><i class="fa fa-ban"></i></a>';
                    } else {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-warning" title="Unblock Status" onclick="changeStatus(' . $service->id . ')"><i class="fa fa-check"></i></a>';
                    }
                    return $editBtn . $deleteBtn . $statusBtn;
                })

                ->rawColumns(['action', 'name', 'status', 'description', 'sub_service'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255|unique:services,title'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            Services::create([
                'user_id'=>Auth::id(),
                'title'=>$request->title,
                'added_date'=>Carbon::now()->format('Y-m-d'),
                'added_time'=>Carbon::now()->format('H:i:s'),
                'description'=>$request->description
            ]);

            return response()->json(['message' => 'Service created successfully'], 200);
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'editServiceName' => 'required|max:255'
            ],
            [
                'editServiceName.required' => 'Service name is required.',
                'editServiceName.max' => 'Max limit is 255.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $service = Services::find($request->id);

            if ($service!=null){
                $service->update([
                   'title'=>$request->editServiceName,
                   'description'=>$request->editDescription,
                ]);
                return response()->json(['message' => 'Service updated successfully'], 200);

            }else{
                return response()->json(['error' => 'Service not found!'], 422);

            }
        }catch (\Exception $exception){
            return response()->json(['error' => 'Something went wrong. Please try again!'], 400);

        }
    }

    public function destroy($id)
    {
        try {
            $service=Services::find($id);
            if ($service != null){
                //                $sub_services=SubServices::where('service_id',$service->id)->delete();
                $service->delete();
                return response()->json(['message' => 'Service deleted successfully'], 200);

            }else{
                return response()->json(['error' => 'Service not found!'], 422);

            }
        }catch (\Exception $exception){
            return response()->json(['error' => 'Something went wrong!'], 400);

        }
    }

    // public function subService(Request $request ,$serivce_id)
    // {
    //     try {
    //         $service=Services::find($serivce_id);
    //         if ($service!=null){
    //             $sub_service=SubServices::create([
    //                 'title'=> $request->title,
    //                 'service_id'=>$serivce_id,
    //                 'added_date'=>Carbon::now()->format('Y-m-d'),
    //                 'added_time'=>Carbon::now()->format('H:i:s')
    //             ]);
    //         }
    //         return response()->json(['message' => 'Sub Category created successfully'], 200);
    //     }catch (ValidationException $e) {
    //         // Validation failed
    //         return response()->json(['errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'An error occurred. Please try again.'], 500);
    //     }
    // }

    public function changeStatus($id)
    {
        try {
            $service=Services::find($id);
            if ($service != null){
                if ($service->status=='enable') {
                    // $sub_services = SubServices::where('service_id', $service->id)->where('status', 'enable')->update([
                    //     'status' => 'disable'
                    // ]);
                    $service->update([
                        'status' => 'disable'
                    ]);
                    return response()->json(['message' => 'Service status disabled successfully'], 200);
                }elseif($service->status='disable'){
                    // $sub_services = SubServices::where('service_id', $service->id)->where('status', 'disable')->update([
                    //     'status' => 'enable'
                    // ]);
                    $service->update([
                        'status' => 'enable'
                    ]);
                    return response()->json(['message' => 'Service status enabled successfully'], 200);

                }
            }else{
                return response()->json(['error' => 'Service not found!'], 422);
            }
        }catch (\Exception $exception){
            return response()->json(['error' => 'Something went wrong!'], 400);

        }
    }

    public function getServicesByCompany(Request $request)
    {
        $company_ids = $request->companyId;

        if (empty($company_ids)) {
            return response()->json([]); // Return an empty array if no companies are selected
        }

        // Fetch companies with their services
        $companies = Company::with('services:id,title')
            ->whereIn('id', (array)$company_ids)
            ->get();

        if ($companies->isEmpty()) {
            return response()->json([]); // Return an empty array if no services are found
        }

        // Collect unique services across all selected companies
        $services = $companies->pluck('services')->flatten()->unique('id');

        return response()->json($services); // Return the services as JSON
    }
}
