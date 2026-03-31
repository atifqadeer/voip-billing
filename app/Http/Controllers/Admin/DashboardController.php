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
use App\Models\Company;
use App\Models\Services;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;    
use App\Actions\GenerateBillingPDF;
use App\Models\ClientInhouseServiceUsage;
use App\Models\ClientServiceUsage;

class DashboardController extends Controller
{
    public function index()
    {
        // Get the counts for clients, vendors, and users
        $clientsCount = Client::where('status', 'enable')->count();
        $vendorsCount = Company::where('status', 'enable')->count();
        $usersCount = User::count();

        // Get all active services
        $services = Services::where('status', 'enable')->get();
        
        // Initialize arrays to store chart data
        $serviceLabels = [];
        $serviceData = [];
        
        // Loop through each service to count usage
        foreach ($services as $ser) {
            $dataCount = ClientServiceUsage::where('service_id', $ser->id)->count();
            
            // Add service title to labels array
            $serviceLabels[] = $ser->title;
            
            // Add data count to data array (0 if no usage)
            $serviceData[] = $dataCount ? $dataCount : 0;
        }
        
        // Get all active services
        $inhouseServices = AdditionalService::where('status', 'enable')->get();
        
        // Initialize arrays to store chart data
        $inhouseServiceLabels = [];
        $inhouseServiceData = [];
        
        // Loop through each service to count usage
        foreach ($inhouseServices as $ser) {
            $dataCount = ClientInhouseServiceUsage::where('additional_service_id', $ser->id)->count();
            
            // Add service title to labels array
            $inhouseServiceLabels[] = $ser->title;
            
            // Add data count to data array (0 if no usage)
            $inhouseServiceData[] = $dataCount ? $dataCount : 0;
        }
        
        // Get the total number of services
        $servicesCount = $services->count();
    
        // Example chart data (replace with actual data)
        $servicesUsageData = [
            'labels' => $serviceLabels,
            'data' => $serviceData,
        ];
        
        // Example chart data (replace with actual data)
        $inhouseServicesUsageData = [
            'labels' => $inhouseServiceLabels,
            'data' => $inhouseServiceData,
        ];
        
        // Example clients activity data (replace with actual dynamic data)
        $clientsActivityData = [
            'labels' => ['Client A', 'Vendor B', 'Client C', 'Vendor D'],
            'data' => [7, 13, 5, 10],
        ];
    
        // Return the data to the view
        return view('home', compact(
            'clientsCount', 'vendorsCount', 'usersCount', 'servicesCount',
            'servicesUsageData', 'clientsActivityData', 'inhouseServicesUsageData'
        ));
    }
    
}
