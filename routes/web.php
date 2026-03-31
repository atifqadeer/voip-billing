<?php

use App\Http\Controllers\Admin\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CdrController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\DescriptiveController;
use App\Http\Controllers\Admin\InclusiveController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\AdditionalServiceController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    //    Route::view('password/update', 'auth.passwords.update')->name('passwords.update');

    //Services
    Route::resource('services', ServiceController::class);
    Route::post('/sub-services/add/{id}', [ServiceController::class, 'subService']);
    Route::get('/getServices', [ServiceController::class, 'getServices'])->name('getServices');
    Route::put('/services/{id}/change-status', [ServiceController::class, 'changeStatus'])->name('changeStatus');
    Route::put('updateService', [ServiceController::class, 'update'])->name('updateService');
    Route::delete('serviceDestroy/{id}', [ServiceController::class, 'destroy'])->name('serviceDestroy');
    Route::get('getServicesByCompany', [ServiceController::class, 'getServicesByCompany'])->name('getServicesByCompany');

    //Additional Services
    Route::resource('additional_services', AdditionalServiceController::class);
    Route::get('/getAdditionalServices', [AdditionalServiceController::class, 'getAdditionalServices'])->name('getAdditionalServices');
    Route::put('updateAdditionalService', [AdditionalServiceController::class, 'update'])->name('updateAdditionalService');
    Route::put('/additional-services/{id}/change-status', [AdditionalServiceController::class, 'changeStatus'])->name('changeAdditionalServiceStatus');
    Route::delete('additional-service-destroy/{id}', [AdditionalServiceController::class, 'destroy']);

    //Tax
    Route::resource('taxes', TaxController::class);
    Route::get('/getTaxes', [TaxController::class, 'getTaxes'])->name('getTaxes');
    Route::put('updateTax', [TaxController::class, 'update'])->name('updateTax');
    Route::put('taxChangeStatus', [TaxController::class, 'changeStatus'])->name('taxChangeStatus');
    Route::delete('destroyTax', [TaxController::class, 'destroy'])->name('destroyTax');

    //Descriptives
    Route::resource('descriptives', DescriptiveController::class);
    Route::get('/getDescriptives', [DescriptiveController::class, 'getDescriptives'])->name('getDescriptives');
    Route::get('editDescriptive', [DescriptiveController::class, 'edit'])->name('editDescriptive');
    Route::put('updateDescriptive', [DescriptiveController::class, 'update'])->name('updateDescriptive');
    Route::put('descriptiveChangeStatus', [DescriptiveController::class, 'changeStatus'])->name('descriptiveChangeStatus');
    Route::delete('descriptiveDestroy', [DescriptiveController::class, 'destroy'])->name('descriptiveDestroy');

    //Inclusives
    Route::resource('inclusives', InclusiveController::class);
    Route::get('/getInclusives', [InclusiveController::class, 'getInclusives'])->name('getInclusives');
    Route::get('editInclusive', [InclusiveController::class, 'edit'])->name('editInclusive');
    Route::put('updateInclusive', [InclusiveController::class, 'update'])->name('updateInclusive');
    Route::put('inclusivesChangeStatus', [InclusiveController::class, 'changeStatus'])->name('inclusivesChangeStatus');
    Route::delete('inclusiveDestroy', [InclusiveController::class, 'destroy'])->name('inclusiveDestroy');

    //Company
    Route::resource('vendors', CompanyController::class);
    Route::get('/get-sub-services/{serviceId}', [CompanyController::class, 'getSubServices']);
    Route::get('/getCompanies', [CompanyController::class, 'getCompanies'])->name('getCompanies');
    Route::put('vendorsChangeStatus', [CompanyController::class, 'changeStatus'])->name('vendorsChangeStatus');
    Route::get('editCompany', [CompanyController::class, 'edit'])->name('vendors.editCompany');
    Route::delete('companyDestroy', [CompanyController::class, 'destroy'])->name('companyDestroy');
    Route::put('updateCompany', [CompanyController::class, 'update'])->name('vendors.updateCompany');
    Route::get('vendors/setup-services/{id}', [CompanyController::class, 'setupServices'])->name('vendors.setupServices');
    Route::put('updateCompanyServicesRates', [CompanyController::class, 'updateCompanyServicesRates'])->name('vendors.updateCompanyServicesRates');

    //client
    Route::resource('clients', UsersController::class);
    Route::get('/getClients', [UsersController::class, 'getClients'])->name('getClients');
    Route::get('/getClientByID', [UsersController::class, 'getClientByID'])->name('getClientByID');
    Route::post('/saveClientServices', [UsersController::class, 'saveClientServices'])->name('saveClientServices');
    Route::post('/saveClientInhouseServices', [UsersController::class, 'saveClientInhouseServices'])->name('saveClientInhouseServices');
    Route::get('/getClientServicesByID', [UsersController::class, 'getClientServicesByID'])->name('getClientServicesByID');
    Route::get('/getClientInhouseServicesByID', [UsersController::class, 'getClientInhouseServicesByID'])->name('getClientInhouseServicesByID');
    Route::put('/updateClient', [UsersController::class, 'update'])->name('updateClient');
    Route::get('/deleteClientByID', [UsersController::class, 'deleteClientByID'])->name('deleteClientByID');
    Route::put('/changeClientStatus/{id}', [UsersController::class, 'changeClientStatus'])->name('changeClientStatus');
    Route::get('get-company-services/{company_id}', [CompanyController::class, 'getCompanyServices'])->name('get.company.services');

    //CDRS
    Route::resource('cdrs', CdrController::class);
    Route::get('getCdrs', [CdrController::class, 'getCdrs'])->name('getCdrs');
    Route::post('importCDR', [CdrController::class, 'importCSVFile'])->name('importCDR');

    //Billings
    Route::resource('billing', BillingController::class);
    Route::get('/getBillingList', [BillingController::class, 'getBillingList'])->name('getBillingList');
    Route::get('/generate-bill', [BillingController::class, 'generateBill'])->name('generate_bill');
    Route::get('/download-bill-details/{bill_id}/{user_id}', [BillingController::class, 'downloadBillDetails'])->name('bill.downloadBillDetails');
    Route::get('/bills/{file}', [BillingController::class, 'show'])->name('bills.show');
    Route::get('/billing/pdf/{id}', [BillingController::class, 'pdfData'])->name('billing.pdfData');
    Route::post('billing/toggle-payment-status', [BillingController::class, 'togglePaymentStatus'])->name('billing.togglePaymentStatus');
    Route::delete('/billing/destroy', [BillingController::class, 'destroy']);

    //setup
    Route::resource('settings', SettingController::class);
    Route::get('settings/general-setting', [SettingController::class, 'generalSetting'])->name('general_setting');
    Route::get('getSettings', [SettingController::class, 'getSettings'])->name('settings.getSettings');
    Route::post('settings/create', [SettingController::class, 'create'])->name('settings.store');
    Route::put('settings/{id}', [SettingController::class, 'update'])->name('settingsUpdate');
});
