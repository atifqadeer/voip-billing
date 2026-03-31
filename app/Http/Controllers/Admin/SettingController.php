<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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

class SettingController extends Controller
{
    public function generalSetting()
    {
        return view('admin.settings.index');
    }

    // Show saved settings
    public function getSettings()
    {
        $settings = Setting::all();

        return response()->json(['data' => $settings]);
    }

    public function show()
    {
        return view('admin.settings.index');
    }

    public function create(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'param' => 'required|string|max:255|unique:settings,param',
            'value' => 'nullable',
        ]);

        // Create the new setting
        $setting = new Setting();
        $setting->param = $request->input('param');
        $setting->value = $request->input('value');
        $setting->save();

        return response()->json([
            'message' => 'Setting created successfully!',
            'data' => $setting,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'value' => 'nullable',  // Adjust validation as per your requirements
        ]);

        $setting = Setting::find($id);
        if ($setting) {
            $setting->value = $request->input('value');
            $setting->save();
            return response()->json(['message' => 'Setting updated successfully']);
        } else {
            return response()->json(['error' => 'Setting not found'], 404);
        }
    }
}
