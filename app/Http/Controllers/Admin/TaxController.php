<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use App\Models\CurrencyList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\CurrencyTrait;

class TaxController extends Controller
{
    use CurrencyTrait;  // Use the CurrencyTrait

    public function index()
    {
        $currency = $this->currencySymbol();
        return view('admin.taxes.index', compact('currency'));
    }

    public function getTaxes()
    {
        if (request()->ajax()) {
            $taxes = Tax::latest()->get();

            return DataTables::of($taxes)
                ->addIndexColumn()
                ->addColumn('name', function ($tax) {
                    $name = ucfirst($tax->name);
                    return $name;
                })
                ->addColumn('type', function ($tax) {
                    $type = ucfirst($tax->type);
                    return $type;
                })
                ->addColumn('rate', function ($tax) {
                    if ($tax->type == 'percentage') {
                        return $tax->rate . '%';
                    } else {
                        return $this->formatCurrency($tax->rate);
                    }
                })
                ->addColumn('status', function ($tax) {
                    $btn = '';
                    if ($tax->status == "enable") {
                        $btn .= '<span class="badge badge-success">Enable</span>';
                    } else {
                        $btn .= '<span class="badge badge-danger">Disable</span>';
                    }
                    return $btn;
                })
                ->addColumn('action', function ($tax) {
                    $editBtn = '<a href="#" class="btn btn-sm btn-primary" title="Edit" data-toggle="modal" onclick="editTax(' . $tax->id . ', \'' . $tax->name . '\', \'' . $tax->rate . '\', \'' . $tax->type . '\',\'' . $tax->currency . '\',\'' . $tax->applies_to . '\')" data-target="#editModal" data-id="' . $tax->id . '" data-name="' . $tax->name . '"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
                    $deleteBtn = '<a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="deleteTax(' . $tax->id . ')"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;';
                    if ($tax->status == 'enable') {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-info" title="Block Status" onclick="changeTaxStatus(' . $tax->id . ')"><i class="fa fa-ban"></i></a>';
                    } else {
                        $statusBtn = '<a href="#" class="btn btn-sm btn-warning" title="Unblock Status" onclick="changeTaxStatus(' . $tax->id . ')"><i class="fa fa-check"></i></a>';
                    }
                    return $editBtn . $deleteBtn . $statusBtn;
                })

                ->rawColumns(['action', 'name', 'status', 'rate', 'type'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:50|unique:taxes,name',
                'tax_rate' => 'required',
                'tax_type' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            Tax::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'rate' => $request->tax_rate,
                'type' => $request->tax_type,
                'currency' => $this->getCurrency(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Tax created successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'editTaxName' => 'required|max:50|unique:taxes,name,' . $request->id,
                'editRate' => 'required',
                'editTaxType' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $tax = Tax::find($request->id);

            if ($tax != null) {
                $tax->update([
                    'name' => $request->editTaxName,
                    'type' => $request->editTaxType,
                    'rate' => $request->editRate,
                    'currency' => $this->getCurrency(),
                ]);
                return response()->json(['message' => 'Tax updated successfully'], 200);
            } else {
                return response()->json(['error' => 'Tax not found!'], 422);
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Something went wrong. Please try again!'], 400);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $id = $request->id;
            $tax = Tax::find($id);
            if ($tax != null) {
                $tax->delete();
                return response()->json(['message' => 'Tax deleted successfully'], 200);
            } else {
                return response()->json(['error' => 'Tax not found!'], 422);
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Something went wrong!'], 400);
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $id = $request->id;

            $tax = Tax::find($id);
            if ($tax != null) {
                if ($tax->status == 'enable') {
                    $tax->update([
                        'status' => 'disable'
                    ]);
                    return response()->json(['message' => 'Tax Status changed successfully'], 200);
                } elseif ($tax->status = 'disable') {
                    $tax->update([
                        'status' => 'enable'
                    ]);
                    return response()->json(['message' => 'Tax Status changed successfully'], 200);
                }
            } else {
                return response()->json(['error' => 'Tax not found!'], 422);
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Something went wrong!'], 400);
        }
    }
}
