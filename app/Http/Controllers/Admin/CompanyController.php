<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyServices;
use App\Models\Services;
use App\Models\SubServices;
use App\Models\Descriptive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Traits\CurrencyTrait;

class CompanyController extends Controller
{
    use CurrencyTrait;  // Use the CurrencyTrait

    public function index()
    {
        $services = Services::where('status','enable')->orderBy('title','asc')->get();
        return view('admin.companies.index',compact('services'));
    }

    public function getCompanies()
    {
        if (request()->ajax()) {
            $companies = Company::latest()->get();

            return DataTables::of($companies)
                ->addIndexColumn()
                ->addColumn('name',function ($comp){
                    $name=ucwords($comp->company_name);
                    return $name;
                })
                ->addColumn('email',function ($comp){
                    $email=strtolower($comp->company_email);
                    return $email;
                })
                ->addColumn('address',function ($comp){
                    $dec = ucwords($comp->company_address);
                    return $dec;
                })
                ->addColumn('phone',function ($comp){
                    $phone=$comp->company_phone_number;
                    return $phone;
                })
                ->addColumn('landline_number',function ($comp){
                    $phone=$comp->company_landline_number;
                    return $phone;
                })
                ->addColumn('status',function ($comp){
                    $btn='';
                    if ($comp->status=="enable"){
                        $btn .='<span class="badge badge-success">Enable</span>';
                    }else{
                        $btn .='<span class="badge badge-danger">Disable</span>';

                    }
                    return $btn;
                })
                ->addColumn('updated_at',function ($comp){
                    $date=Carbon::parse($comp->updated_at)->format('d-m-Y, h:i A');
                    return $date;
                })
                ->addColumn('services', function ($comp) {
                    // Access the services related to the company
                    $services = $comp->services;
                
                    // Map the services to generate badge HTML and access pivot attributes if needed
                    $servicesData = $services->map(function ($service) {
                        return [
                            'service_title' => ucwords($service->title),
                            'created_at' => $service->pivot->created_at,  // You can access pivot attributes here
                            'updated_at' => $service->pivot->updated_at,  // You can access pivot attributes here
                        ];
                    });
                
                    // Get unique service titles and generate badges
                    $badges = $servicesData->pluck('service_title')
                                           ->unique()
                                           ->map(function ($title) {
                                               return '<span class="badge badge-info p-1" style="margin-bottom:3px">' . $title . '</span>';
                                           });
                
                    // Concatenate badges into a string
                    $badgeHtml = $badges->implode(' '); // Space between badges
                
                    // Return the badge HTML to be rendered in the view
                    return $badgeHtml;
                })
                
                ->rawColumns(['services'])
                ->addColumn('action', function ($comp) {
                    $editUrl = route('vendors.setupServices', ['id' => $comp->id]);
                    $editBtn = '<a href="#" class="btn btn-sm btn-primary" id="editBtn" title="Edit" data-toggle="modal"  data-target="#editModal" data-id="' . $comp->id . '" data-name="' . $comp->title . '"><i class="fa fa-edit"></i></a>&nbsp;';
                    $setupBtn = '<a href="'. $editUrl .'" class="btn btn-sm btn-secondary" title="Setup"><i class="fa fa-gear"></i></a>&nbsp;';
                    $deleteBtn = '<a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="deleteCompany('.$comp->id.')"><i class="fa fa-trash"></i></a>&nbsp;';
                    if ($comp->status == 'enable') {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-info" title="Block Status" onclick="changeStatus(' . $comp->id . ')"><i class="fa fa-ban"></i></a>';
                    } else {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-warning" title="Unblock Status" onclick="changeStatus(' . $comp->id . ')"><i class="fa fa-check"></i></a>';
                    }
                   
                    return $editBtn. $setupBtn . $deleteBtn . $statusBtn;
                })

                ->rawColumns(['action', 'name', 'status', 'services', 'address', 'email', 'phone', 'landline_number', 'updated_at'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'CompanyName' => 'required|string|max:255|unique:companies,company_name',
                'CompanyEmail' => 'nullable|email|unique:companies,company_email|max:255',
                'phone' => 'nullable|string|max:255',
                // 'landline' => 'nullable|string|max:255',
                'services' => 'required|array|min:1',
                'services.*' => 'exists:services,id',
                'address' => 'nullable|string|max:255',
            ],
            [
                'CompanyName.required' => 'Vendor name is required.',
                'CompanyName.unique' => 'This name is already in use.',
                'CompanyEmail.email' => 'Enter a valid email address.',
                'CompanyEmail.unique' => 'This email is already in use.',
                'services.exists' => 'One or more selected services are invalid.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $serviceIds=$request->input('services');

            // Create a new company using the validated data
            $company = Company::create([
                'user_id'=>Auth::id(),
                'company_name'=>$request->CompanyName,
                'company_email'=>$request->CompanyEmail,
                'company_phone_number'=>$request->phone,
                'company_landline_number'=>$request->landline,
                'company_address'=>$request->address,
            ]);
            foreach ($serviceIds as $serviceId) {
                $company->services()->attach($serviceId, ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }

            DB::commit();
            return response()->json(['message' => 'Vendor created successfully'], 200);
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function edit(Request $request)
    {
        $company = Company::with('services')->find($request->id);

        return response()->json(['company' => $company]);
    }

    public function setupServices($id)
    {
        $company = Company::find($id);
        $services = $company->services()->get();
        $descriptive = Descriptive::where('status','enable')->get();
        $currency = $this->currencySymbol();

        //  Group services by service_id to get unique services
        $groupedServices = $services->groupBy('id');
        return view('admin.companies.service_sheet', compact('groupedServices', 'currency', 'descriptive','company'));
    }

    public function update(Request $request)
    {
        try{
            $requestData = $request->all();
            $validator = Validator::make($requestData, [
                'editCompanyName' => 'required|string|max:255|unique:companies,company_name,'.$requestData['id'],
                'editCompanyEmail' => 'nullable|email|max:255|unique:companies,company_email,'.$requestData['id'],
                'editPhone' => 'nullable|string|max:255',
                'editLandline' => 'nullable|string|max:255',
                'editServices' => 'required|array|min:1',
                'editServices.*' => 'exists:services,id',
                'editAddress' => 'nullable|string|max:255',
            ],
            [
                'editCompanyName.required' => 'Vendor name is required.',
                'editCompanyEmail.email' => 'Please enter a valid email address.',
                'editCompanyEmail.unique' => 'This email is already in use.',
                'editServices.exists' => 'One or more selected services are invalid.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $company = Company::find($requestData['id']);

            $company->update([
                'company_name' => $requestData['editCompanyName'],
                'company_email' => $requestData['editCompanyEmail'],
                'company_phone_number' => $requestData['editPhone'],
                'company_landline_number' => $requestData['editLandline'],
                'company_address' => $requestData['editAddress'],
            ]);
 
            $currentServices = $company->services()->pluck('service_id')->toArray();
            $requestedServices = $requestData['editServices'];
            
            // Detach services that are currently in the database but not in the request
            $servicesToDetach = array_diff($currentServices, $requestedServices);
            foreach ($servicesToDetach as $serviceId) {
                $company->services()->detach($serviceId);
            }
            
            // Attach services that are in the request but not currently in the database
            $servicesToAttach = array_diff($requestedServices, $currentServices);
            foreach ($servicesToAttach as $serviceId) {
                // foreach ($descriptive as $desc) {
                    // $company->services()->attach($serviceId, ['descriptive_id' => $desc->id]);
                // }
                $company->services()->attach($serviceId, ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }

            return response()->json(['message' => 'Vendor updated successfully']);

        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateCompanyServicesRates(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'service_id' => 'required|integer',
            'company_id' => 'required|integer',
            'buy_rate' => 'required|array',
            'sell_rate' => 'required|array',
        ]);

        $serviceId = $validated['service_id'];
        $companyId = $validated['company_id'];
        $buyRates = $validated['buy_rate'];
        $sellRates = $validated['sell_rate'];
    
        foreach ($buyRates as $descriptiveId => $buyRate) {
            // Get the corresponding increase rate for this descriptive ID
            $sellRate = $sellRates[$descriptiveId] ?? 0;

            CompanyServices::where('company_id', $companyId)
                ->where('service_id', $serviceId)
                ->whereNull('descriptive_id')->delete();
    
            // Check if the record exists with company_id, service_id, and descriptive_id
            $serviceData = CompanyServices::where('company_id', $companyId)
                ->where('service_id', $serviceId)
                ->where('descriptive_id', $descriptiveId)
                ->first();
    
            if ($serviceData) {
                // If record exists, update it
                $serviceData->update([
                    'buy_rate' => $buyRate,
                    'sell_rate' => $sellRate,
                ]);
            } else {
                // If record does not exist, create a new one
                CompanyServices::create([
                    'company_id' => $companyId,
                    'service_id' => $serviceId,
                    'descriptive_id' => $descriptiveId,
                    'buy_rate' => $buyRate,
                    'sell_rate' => $sellRate,
                ]);
            }
        }
    
        return response()->json(['success' => true, 'message' => 'Services updated successfully.'], 200);

    }
    
    public function destroy(Request $request)
    {
        try {
            $company = Company::find($request->id);
            if ($company){
                $company->services()->detach($company->id);
                $company->update(['is_deleted'=>'1','status'=>'disable']);

                return response()->json(['message' => 'Vendor & Services deleted successfully'], 200);

            }else{
                return response()->json(['error' => 'Service id not found!'], 422);
            }
        }catch (\Exception $exception){
            return response()->json(['error' => 'SomeThing went wrong!'], 400);

        }
    }

    public function getSubServices($serviceId)
    {
        // Retrieve sub services based on the selected service ID
        $subServices = SubServices::where('service_id', $serviceId)->get();

        // Return the sub services as JSON
        return response()->json($subServices);
    }

    public function changeStatus(Request $request)
    {
        try {
            $company = Company::find($request->id);
            if ($company != null){
                if ($company->status == 'enable') {
                    $company->update([
                        'status' => 'disable'
                    ]);

                }elseif($company->status == 'disable'){
                    $company->update([
                        'status' => 'enable'
                    ]);
                }

                return response()->json(['success'=>true,'message' => 'Status changed successfully!'], 200);
            }else{
                return response()->json(['success'=>false,'message' => 'Vendor not found!'], 422);
            }
        }catch (\Exception $exception){
            return response()->json(['error' => 'Something went wrong!'], 400);

        }
    }

    public function getCompanyServices($companyId) {
        $company = Company::find($companyId);
        $subServices = $company->services()->pluck('title')->flatten()->unique()->get();

        return response()->json($subServices);
    }

}
