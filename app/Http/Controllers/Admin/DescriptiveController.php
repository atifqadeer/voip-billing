<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Descriptive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class DescriptiveController extends Controller
{
    public function index()
    {
        return view('admin.descriptives.index');
    }

    public function getDescriptives(){
        if (request()->ajax()) {
            $descriptives = Descriptive::latest()->get();

            return DataTables::of($descriptives)
                ->addIndexColumn()
                ->addColumn('description_name',function ($descriptive){
                    $name = ucwords($descriptive->description_name);
                    return $name;
                })
                ->addColumn('replace_with',function ($descriptive){
                    $replaceWith = strtolower($descriptive->replace_with);
                    return $replaceWith;
                })
                ->addColumn('status',function ($descriptive){
                    $btn='';
                    if ($descriptive->status=="enable"){
                        $btn .='<span class="badge badge-success">Enable</span>';
                    }else{
                        $btn .='<span class="badge badge-danger">Disable</span>';

                    }
                    return $btn;
                })
                ->addColumn('updated_at',function ($descriptive){
                    $date=Carbon::parse($descriptive->updated_at)->format('M j Y, h:i A');
                    return $date;
                })
                ->addColumn('action', function ($descriptive) {
                    $editBtn = '<a href="#" class="btn btn-sm btn-primary" id="editBtn" title="Edit" data-toggle="modal"  data-target="#editModal" data-id="' . $descriptive->id . '" data-name="' . $descriptive->title . '"><i class="fa fa-edit"></i></a>&nbsp;';
                    $deleteBtn = '<a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="deleteCompany('.$descriptive->id.')"><i class="fa fa-trash"></i></a>&nbsp;';
                    if ($descriptive->status == 'enable') {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-info" title="Block Status" onclick="changeStatus(' . $descriptive->id . ')"><i class="fa fa-ban"></i></a>';
                    } else {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-warning" title="Unblock Status" onclick="changeStatus(' . $descriptive->id . ')"><i class="fa fa-check"></i></a>';
                    }
                   
                    return $editBtn. $deleteBtn . $statusBtn;
                })

                ->rawColumns(['action', 'description_name', 'status', 'replace_with', 'updated_at'])
                ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;

        $data = Descriptive::findOrFail($id);

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $DescriptiveName = $request->input('DescriptiveName');
        $replaceWith = $request->input('replaceWith');

        Descriptive::insert([
            'description_name' => $DescriptiveName,
            'replace_with' => $replaceWith
        ]);

        return response()->json(['success' => true, 'message' => 'Descriptive saved successfully!']);
    }

    public function update(Request $request)
    {
        try{
            $requestData = $request->all();
            $validator = Validator::make($requestData, [
                'editDescriptiveName' => 'required|string|max:255|unique:descriptive,description_name,'.$requestData['id'],
                'editReplaceWith' => 'required',
            ],
            [
                'editCompanyName.required' => 'Descriptive name is required.',
                'editReplaceWith.required' => 'Replace with is required.',
                'editCompanyName.exists' => 'One or more selected services are invalid.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $descriptive = Descriptive::find($requestData['id']);

            $descriptive->update([
                'description_name' => $requestData['editDescriptiveName'],
                'replace_with' => $requestData['editReplaceWith'],
            ]);

            return response()->json(['message' => 'Descriptive updated successfully']);

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
            $descriptive = Descriptive::find($request->id);
            if ($descriptive){
                $descriptive->delete();

                return response()->json(['message' => 'Descriptive deleted successfully'], 200);

            }else{
                return response()->json(['error' => 'Descriptive id not found!'], 422);
            }
        }catch (\Exception $exception){
            return response()->json(['error' => 'Something went wrong!'], 400);

        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $descriptive = Descriptive::find($request->id);
            if ($descriptive != null){
                if ($descriptive->status == 'enable') {
                    $descriptive->update([
                        'status' => 'disable'
                    ]);

                }elseif($descriptive->status == 'disable'){
                    $descriptive->update([
                        'status' => 'enable'
                    ]);
                }

                return response()->json(['success'=>true,'message' => 'Status changed successfully!'], 200);
            }else{
                return response()->json(['success'=>false,'message' => 'Descriptive not found!'], 422);
            }
        }catch (\Exception $exception){
            return response()->json(['error' => 'Something went wrong!'], 400);

        }
    }
}
