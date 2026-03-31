@extends('layouts.app')

<!-- Select2 CSS CDN -->
<title>{{ getSetting('app_name', env('APP_NAME')) }} | Clients Management</title>

@section('content')
<style>
    .daterangepicker {
    z-index: 1050 !important; /* Ensure calendar appears above the modal (Bootstrap modal z-index is 1040) */
}

</style>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Clients Management</h1>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#clientModal">
                                  <i class="fa fa-plus"></i>  Add Client
                                </button>
                            </div>
                            <div class="card-body">
                                <table id="myTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Ref# Account</th>
                                            <th>Name</th>
                                            {{-- <th>Phone (Searchable)</th> --}}
                                            <th width="5%">Services</th>
                                            <th width="15%">OutGoing Numbers</th>
                                            <th width="15%">Incoming Numbers</th>
                                            <th>Frequency</th>
                                            <th>Status</th>
                                            <th>Updated At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        
        <div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Client</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addClientForm">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <label for="clientAccountNumber" class="col-form-label">Reference Account <span class="required">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="clientAccountNumber" id="clientAccountNumber" placeholder="Enter Reference Account">
                                    </div>
                                    <span id="clientAccountNumberError" class="text-danger"></span>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <label for="clientName" class="col-form-label">Name <span class="required">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="clientName" id="clientName" placeholder="Enter Client Name">
                                    </div>
                                    <span id="clientNameError" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-lg-4 col-sm-12">
                                    <label for="tag_name" class="col-form-label">Tag Name</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="tag_name" id="tag_name" placeholder="Enter Tag Name">
                                    </div>
                                    <span id="tagNameError" class="text-danger"></span>
                                </div>
                                <div class="col-md-12 col-lg-4 col-sm-12">
                                    <label for="trunkID" class="col-form-label">Trunk ID <span class="required">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="trunkID" name="trunkID" placeholder="Enter Trunk ID">
                                    </div>
                                    <span id="trunkIDError" class="text-danger"></span>
                                </div>
                                <div class="col-md-12 col-lg-4 col-sm-12">
                                    <label for="clientEmail" class="col-form-label">Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control" name="clientEmail" id="clientEmail" placeholder="contact@example.com">
                                    </div>
                                    <span id="clientEmailError" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Phone Number -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="phone" class="col-form-label">Phone</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="phone" id="phone" placeholder="Enter Phone">
                                    </div>
                                    <span id="phoneError" class="text-danger"></span>
                                </div>
                                <!-- Frequency Select Dropdown -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="frequency" class="col-form-label">Frequency <span class="required">*</span></label>
                                    <div class="input-group select2-blue">
                                        <select class="form-control select2" name="frequency" id="frequency" required 
                                        data-placeholder="Select a frequency" data-dropdown-css-class="select2-blue" 
                                        style="width: 100%;">
                                            <option value="" disabled selected>Select a frequency</option>
                                            <option value="monthly">Monthly</option>
                                            <option value="quarterly">Quarterly</option>
                                            <option value="annually">Annually</option>
                                        </select>
                                    </div>
                                    <span id="frequencyError" class="text-danger"></span> <!-- Error message for frequency -->
                                </div>
                            </div>

                            <div class="row">
                                <!-- Outgoing Numbers -->
                                <div class="col-md-12 col-lg-6 col-sm-12">
                                    <label for="outgoing_number" class="col-form-label">Outgoing Numbers</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        </div>
                                        <textarea class="form-control" rows="2" id="outgoing_number" name="outgoing_number" placeholder="Enter Outgoing Number"></textarea>
                                    </div>
                                    <span id="outgoing_numberError" class="text-danger"></span>
                                </div>

                                <!-- Incoming Numbers -->
                                <div class="col-md-12 col-lg-6 col-sm-12">
                                    <label for="incoming_number" class="col-form-label">Incoming Numbers</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        </div>
                                        <textarea class="form-control" rows="2" id="incoming_number" name="incoming_number" placeholder="Enter Incoming Number"></textarea>
                                    </div>
                                    <span id="incoming_numberError" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="row">
                                <!-- In-House Services Select Dropdown -->
                                <div class="col-md-12 col-lg-6 col-sm-12">
                                    <!-- Address -->
                                    <label for="address" class="col-form-label">Address</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        </div>
                                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter Address"></textarea>
                                    </div>
                                    <span id="addressError" class="text-danger"></span>
                                </div>
                                <!-- Note -->
                                <div class="col-md-12 col-lg-6 col-sm-12">
                                    <label for="note" class="col-form-label">Note</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                        </div>
                                        <textarea class="form-control" id="note" rows="2" name="note" placeholder="Enter Notes"></textarea>
                                    </div>
                                    <span id="noteError" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-12 form-group">
                                    <label for="inhouseServices" class="col-form-label">In-House Services</label>
                                    <div class="input-group select2-blue">
                                        <select class="select2" name="inhouseServices[]" id="inhouseServices" multiple 
                                                data-placeholder="Select In-House Services" data-dropdown-css-class="select2-blue" 
                                                style="width: 100%;">
                                            <option value="" disabled>Select In-House Services</option>
                                            @foreach($inhouseServices as $service)
                                                <option value="{{ $service->id }}">{{ ucwords($service->title) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span id="inhouseServicesError" class="text-danger"></span> <!-- Error message for in-house services -->
                                </div>
                                <div class="col-md-6 col-sm-12 form-group">
                                    <label for="call_rate" class="col-form-label">Call Rate <span class="required">*</span></label>
                                    <div class="input-group select2-blue">
                                        <select class="select2" name="call_rate" id="call_rate" required multiple 
                                                data-placeholder="Select Call Rates" data-dropdown-css-class="select2-blue" 
                                                style="width: 100%;">
                                            <option value="" disabled>Select Call Rates</option>
                                            <option value="standard">Standard</option>
                                            <option value="special">Special / Specific Destination</option>
                                        </select>
                                    </div>
                                    <span id="callRatesError" class="text-danger"></span> <!-- Error message for in-house services -->
                                </div>
                            </div>

                            <div class="row">
                                <!-- Company -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="company" class="col-form-label">Vendors <span class="required">*</span></label>
                                    <div class="input-group select2-blue">
                                        <select class="select2" name="company[]" id="company" required multiple 
                                                data-placeholder="Select Vendors" data-dropdown-css-class="select2-blue" 
                                                style="width: 100%;" onchange="getServicesByCompany('new')">
                                            <option value="" disabled>Select Vendors</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}">{{ ucwords($company->company_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span id="companyError" class="text-danger"></span>
                                </div>

                                <!-- Services -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="landline" class="col-form-label">Services <span class="required">*</span></label>
                                    <div class="input-group select2-blue">
                                        <select class="select2" multiple="multiple" required name="services[]" id="services" 
                                                data-placeholder="Select Services" data-dropdown-css-class="select2-blue" 
                                                style="width: 100%;">
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="enableVatTax" checked onclick="updateCheckboxValues()">
                                        <label for="enableVatTax" class="form-check-label">Enable VAT Tax</label>
                                    </div>
                                    <span id="enableVatTaxError" class="text-danger"></span>    
                                </div>
                                <div class="col-md-3 mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="fixedLineService" onclick="updateCheckboxValues()">
                                        <label for="fixedLineService" class="form-check-label">Fixed Line Services</label>
                                    </div>
                                    <span id="fixedLineServiceError" class="text-danger"></span>    
                                </div>
                            </div>

                            <!-- Hidden input fields to store the checkbox values -->
                            <input type="hidden" name="enableVatTaxValue" id="enableVatTaxValue" value="1">
                            <input type="hidden" name="fixedLineServiceValue" id="fixedLineServiceValue" value="0">

                            <div class="row" style="display: none;" id="fixedLineNumberDiv">    
                                <!-- Phone Number -->
                                <div class="col-md-12 col-sm-12 mt-3">
                                    <label for="fixedLineServiceNumber" class="col-form-label">Fixed Line Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        </div>
                                        <textarea class="form-control" name="fixedLineServiceNumber" id="fixedLineServiceNumber" placeholder="Enter Fixed Line Number"></textarea>
                                    </div>
                                    <span id="fixedLineServiceNumberError" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="form-group float-right mt-3">
                                <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Cancel</button>
                                <button type="submit" id="addClientSubmitButton" class="btn btn-primary">
                                    <span id="addClientbuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span id="addClientbuttonText">Submit</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="statusConfirmationModalClient" tabindex="-1" role="dialog" aria-labelledby="statusConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statusConfirmationModalLabel">Confirm Status Change</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to change the status of this Client?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmStatusChange" class="btn btn-danger">
                            <span id="confirmStatusbuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span id="confirmStatusbuttonText">Change Status</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this Client?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">
                            <span id="deletebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span id="deletebuttonText">Submit</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Client</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Edit form -->
                        <form id="editFormClient">
                            <input type="hidden" name="id" id="editClientId">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <label for="editClientAccount" class="col-form-label">Reference Account <span class="required">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="editClientAccount" name="editClientAccount" placeholder="Enter Reference Account">
                                    </div>
                                    <span id="editClientAccountError" class="text-danger"></span>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <label for="clientName" class="col-form-label">Name <span class="required">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="editClientName" name="editClientName" placeholder="Enter Client Name">
                                    </div>
                                    <span id="editClientNameError" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-lg-4 col-sm-12">
                                    <label for="editTagName" class="col-form-label">Tag Name</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="editTagName" name="editTagName" placeholder="Enter Tag Name">
                                    </div>
                                    <span id="editTagNameError" class="text-danger"></span>
                                </div>
                                <div class="col-md-12 col-lg-4 col-sm-12">
                                    <label for="editTrunkID" class="col-form-label">Trunk ID <span class="required">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="editTrunkID" name="editTrunkID" placeholder="Enter Trunk ID">
                                    </div>
                                    <span id="editTrunkIDError" class="text-danger"></span>
                                </div>
                                <div class="col-md-12 col-lg-4 col-sm-12">
                                    <label for="editClientEmail" class="col-form-label">Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control" id="editClientEmail" name="editClientEmail" placeholder="contact@example.com">
                                    </div>
                                    <span id="editClientEmailError" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Phone Number -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="editPhone" class="col-form-label">Phone</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="editPhone" id="editPhone" placeholder="Enter Phone">
                                    </div>
                                    <span id="editPhoneError" class="text-danger"></span>
                                </div>
                                <!-- Frequency Select Dropdown -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="editClientFrequency" class="col-form-label">Frequency <span class="required">*</span></label>
                                    <select class="form-control" name="editClientFrequency" id="editClientFrequency">
                                        <option value="" disabled >Select Frequency</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="quarterly">Quarterly</option>
                                        <option value="annually">Annually</option>
                                    </select>
                                    <span id="editClientFrequencyError" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Outgoing Numbers -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="editOutgoingNumber" class="col-form-label">Outgoing Numbers</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        </div>
                                        <textarea class="form-control" rows="2" id="editOutgoingNumber" name="editOutgoingNumber" placeholder="Enter Outgoing Number"></textarea>
                                    </div>
                                    <span id="editOutgoingNumberError" class="text-danger"></span>
                                </div>
                        
                                <!-- Incoming Numbers -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="incoming_number" class="col-form-label">Incoming Numbers</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        </div>
                                        <textarea class="form-control" rows="2" id="editIncomingNumber" name="editIncomingNumber" placeholder="Enter Incoming Number"></textarea>
                                    </div>
                                    <span id="editIncomingNumberError" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="row">
                                <!-- In-House Services Select Dropdown -->
                                <div class="col-md-6 col-sm-12">
                                    <!-- Address -->
                                    <label for="editAddress" class="col-form-label">Address</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        </div>
                                        <textarea class="form-control" id="editAddress" name="editAddress" rows="2"  placeholder="Enter Address"></textarea>
                                    </div>
                                    <span id="editAddressError" class="text-danger"></span>
                                </div>
                                <!-- Note -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="editNote" class="col-form-label">Note</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                        </div>
                                        <textarea class="form-control" id="editNote" rows="2" name="editNote" placeholder="Enter Notes"></textarea>
                                    </div>
                                    <span id="editNoteError" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12 form-group">
                                    <label for="editInhouseServices" class="col-form-label">In-House Services</label>
                                    <div class="input-group select2-blue">
                                        <select class="select2" name="editInhouseServices[]" id="editInhouseServices" multiple
                                        data-placeholder="Select In-House Services" data-dropdown-css-class="select2-blue" style="width: 100%;">
                                        <option value="" disabled>Select In-House Services</option>
                                        @foreach($inhouseServices as $service)
                                            <option value="{{ $service->id }}">{{ ucwords($service->title) }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                    <span id="editInhouseServicesError" class="text-danger"></span> <!-- Error message for in-house services -->
                                </div>
                                <div class="col-md-6 col-sm-12 form-group">
                                    <label for="call_rate" class="col-form-label">Call Rate <span class="required">*</span></label>
                                    <div class="input-group select2-blue">
                                        <select class="select2" name="call_rate" id="call_rate" required multiple 
                                                data-placeholder="Select Call Rates" data-dropdown-css-class="select2-blue" 
                                                style="width: 100%;">
                                            <option value="" disabled>Select Call Rates</option>
                                            <option value="standard">Standard</option>
                                            <option value="special">Special / Specific Destination</option>
                                        </select>
                                    </div>
                                    <span id="callRatesError" class="text-danger"></span> <!-- Error message for in-house services -->
                                </div>
                            </div>

                            <div class="row">
                                <!-- Company -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="editCompany" class="col-form-label">Vendors <span class="required">*</span></label>
                                    <div class="input-group select2-blue">
                                        <select class="select2" name="editCompany[]" id="editCompany" required multiple 
                                                data-placeholder="Select Vendors" data-dropdown-css-class="select2-blue" 
                                                style="width: 100%;" onchange="getServicesByCompany('new')">
                                            <option value="" disabled>Select Vendors</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}">{{ ucwords($company->company_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span id="editVendorsError" class="text-danger"></span>
                                </div>
                        
                                <!-- Services -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="editServices" class="col-form-label">Services <span class="required">*</span></label>
                                    <div class="input-group select2-blue">
                                        <select class="form-control" multiple="multiple" name="editServices[]" id="editServices" data-placeholder="Select Services" data-dropdown-css-class="select2-blue" style="width: 100%;"></select>
                                    </div>
                                    <span id="editServicesError" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="enableVatTaxEdit" checked onclick="updateCheckboxValuesForEdit()">
                                        <label for="enableVatTaxEdit" class="form-check-label">Enable VAT Tax</label>
                                    </div>
                                    <span id="enableVatTaxEditError" class="text-danger"></span>    
                                </div>
                                <div class="col-md-3 mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="fixedLineServiceEdit" onclick="updateCheckboxValuesForEdit()">
                                        <label for="fixedLineServiceEdit" class="form-check-label">Fixed Line Services</label>
                                    </div>
                                    <span id="fixedLineServiceEditError" class="text-danger"></span>    
                                </div>
                            </div>
                            <!-- Hidden input fields to store the checkbox values -->
                            <input type="hidden" name="enableVatTaxValueEdit" id="enableVatTaxValueEdit">
                            <input type="hidden" name="fixedLineServiceValueEdit" id="fixedLineServiceValueEdit">

                            <div class="row" style="display: none;" id="EditFixedLineNumberDiv">    
                                <!-- Phone Number -->
                                <div class="col-md-12 col-sm-12 mt-3">
                                    <label for="fixedLineServiceNumberEdit" class="col-form-label">Fixed Line Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        </div>
                                        <textarea class="form-control" name="fixedLineServiceNumberEdit" id="fixedLineServiceNumberEdit" placeholder="Enter Fixed Line Number"></textarea>
                                    </div>
                                    <span id="fixedLineServiceNumberEditError" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="form-group float-right mt-3">
                                <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Cancel
                                </button>
                                <button type="submit" id="editClientSubmitButton" class="btn btn-primary">
                                    <span id="editClientbuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span id="editClientbuttonText">Submit</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="setupModal" tabindex="-1" role="dialog" aria-labelledby="setupModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="setupModalLabel">Services Setup</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Edit form -->
                        <form id="editServiceRatesForm">
                            @csrf()
                            <!-- Specific container for dynamic fields -->
                            <div id="dynamicFieldsContainer"></div>
                
                            <div class="form-group float-right mt-3">
                                <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Cancel
                                </button>
                                <button type="submit" id="setUpSubmitButton" class="btn btn-primary">
                                    <span id="setUpbuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span id="setUpbuttonText">Submit</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="inhouseSetupModal" tabindex="-1" role="dialog" aria-labelledby="inhouseSetupModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inhouseSetupModalLabel">Inhouse Services Setup</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Edit form -->
                        <form id="editInhouseServiceRatesForm">
                            @csrf()
                            <!-- Specific container for dynamic fields -->
                            <div id="inhouseDynamicFieldsContainer"></div>
        
                            <div class="form-group float-right mt-3">
                                <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Cancel
                                </button>
                                <button type="submit" id="inhouseSubmitButton" class="btn btn-primary">
                                    <span id="inhousebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span id="inhousebuttonText">Submit</span>
                                </button>                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection

@section('scripts')

    <script type="text/javascript">
        $('.select2').select2();
       
        $('#editModal').on('shown.bs.modal', function () {
            $('#editModal #editServices').select2();  // Initialize Select2 for Services
            $('#editModal #editCompany').select2();   // Initialize Select2 for Company
        });

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        $(function () {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: "{{ route('getClients') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'account_number', name: 'account_number', render: function(data) {
                        return data ? data.toUpperCase() : '';
                    }},
                    {data: 'name', name: 'name'},
                    {data: 'services', name: 'services'},  // This contains the service IDs
                    {data: 'client_outgoing_number', name: 'client_outgoing_number'},
                    {data: 'client_incoming_number', name: 'client_incoming_number'},
                    {data: 'frequency', name: 'frequency'},  // This contains the service IDs
                    {data: 'status', name: 'status'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action'}
                ],
                rowCallback: function(row, data, index) {
                    // When a row is clicked, get the services and pre-select them in the dropdown
                    $(row).on('click', function() {
                        // Fetch the services associated with this row
                        var companies = data.company_ids; // Array of service IDs for the current row
                        var services = data.service_ids; // Array of service IDs for the current row

                        // Call function to update the dropdown with selected services
                        updateServicesDropdown(companies,services);
                    });
                }
            });
            // Add an event listener to the "editCompany" dropdown
            $('#editCompany').on('change', function () {
                var selectedCompanies = $(this).val(); // Get selected company IDs as an array
                updateServicesDropdown(selectedCompanies, []); // Fetch services for the selected companies
            });
        });

        $(document).ready(function () {
            $(document).on('click', '#editBtn', function () {
                var clientID = $(this).data('id');

                // Make AJAX request to get client details
                $.ajax({
                    data: { client_id: clientID },
                    type: 'GET',
                    url: 'getClientByID',
                    success: function (response) {
                        // Populate client details in the modal
                        $('#editModal #editClientId').val(response.id);
                        $('#editModal #editClientAccount').val(response.account_number);
                        $('#editModal #editClientName').val(response.client_name);
                        $('#editModal #editClientEmail').val(response.client_email);
                        $('#editModal #editPhone').val(response.client_phone_number);
                        $('#editModal #editOutgoingNumber').val(response.client_outgoing_number);
                        $('#editModal #editIncomingNumber').val(response.client_incoming_number);
                        $('#editModal #editAddress').val(response.client_address);
                        $('#editModal #editTagName').val(response.tag_name);
                        $('#editModal #editTrunkID').val(response.trunk_number);
                        $('#editModal #editNote').val(response.notes);
                        
                         // Set the Frequency field based on response.frequency
                        var frequencyValue = response.frequency;  // Get frequency from response

                        // Check if the frequency is one of the valid options
                        if (['monthly', 'quarterly', 'annually'].includes(frequencyValue)) {
                            $('#editModal #editClientFrequency').val(frequencyValue).trigger('change.select2');  // This updates the dropdown
                        } else {
                            $('#editModal #editClientFrequency').val('').trigger('change.select2');  // If frequency is not valid, clear the selection
                        }

                        // Pre-select services
                        var selectedServices = response.client_service_usages.map(service => service.service_id);
                        $('#editModal #editServices').val(selectedServices).trigger('change.select2');  // Make sure select2 is initialized

                        // Pre-select companies
                        var selectedCompanies = response.client_service_usages.map(service => service.company_id);
                        $('#editModal #editCompany').val(selectedCompanies).trigger('change.select2'); // Make sure select2 is initialized
                       
                        // Pre-select companies
                        var selectedInhouseServices = response.client_in_house_services.map(service => service.additional_service_id);
                        $('#editModal #editInhouseServices').val(selectedInhouseServices).trigger('change.select2'); // Make sure select2 is initialized

                        // Set the Enable VAT Tax checkbox based on response.enableVatTax
                        var enableVatTax = response.is_enable_vat_tax;  // Assuming the response has enableVatTax
                        if (enableVatTax == '1') {
                            $('#editModal #enableVatTaxEdit').prop('checked', true);
                            $('#editModal #enableVatTaxValueEdit').val('1');  // Set hidden input to 1 if checked
                        } else {
                            $('#editModal #enableVatTaxEdit').prop('checked', false);
                            $('#editModal #enableVatTaxValueEdit').val('0');  // Set hidden input to 0 if unchecked
                        }

                        // Assuming you have this part in your HTML
                        var fixedLineService = response.is_enable_fixed_line_services;  // Assuming the response has 'is_enable_fixed_line_services'
                        if (fixedLineService == '1') {
                            // Check the checkbox
                            $('#editModal #fixedLineServiceEdit').prop('checked', true);
                            // Set hidden input value to '1' if checked
                            $('#editModal #fixedLineServiceValueEdit').val('1');
                            
                            // Show the fixed line number input div by changing display style to 'block'
                            $('#editModal #EditFixedLineNumberDiv').css('display', 'block');  // Set display to 'block'
                            $('#editModal #fixedLineServiceNumberEdit').attr('required', 'required');
                            $('#editModal #fixedLineServiceNumberEdit').val(response.fixed_line_service_number);

                        } else {
                            // Uncheck the checkbox
                            $('#editModal #fixedLineServiceEdit').prop('checked', false);
                            // Set hidden input value to '0' if unchecked
                            $('#editModal #fixedLineServiceValueEdit').val('0');
                            
                            // Hide the fixed line number input div by setting display style to 'none'
                            $('#editModal #EditFixedLineNumberDiv').css('display', 'none');  // Set display to 'none'
                            $('#editModal #fixedLineServiceNumberEdit').removeAttr('required');
                        }

                        // Show the modal
                        $('#editModal').modal('show');
                    },
                    error: function (error) {
                        console.log('Error fetching client details: ', error);
                    }
                });
            });

            $(document).on('click', '#setupBtn', function () {
                var clientID = $(this).data('id');

                // Make AJAX request to get category details
                $.ajax({
                    data: { client_id: clientID },
                    type: 'GET',
                    url: 'getClientServicesByID',
                    success: function (response) {
                        // Clear previous dynamic fields or messages
                        $('#dynamicFieldsContainer').empty();
                        var currencySymbol = '{{ $currency }}';
                        
                        var client_id = response.id;
                        // Check if the response contains data
                        if (response.client_service_usages && response.client_service_usages.length > 0) {
                            // Loop through the client_service_usages array and add input fields
                            response.client_service_usages.forEach((service, index) => {
                                const serviceFieldHTML = `
                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <label for="serviceTitle_${index}">Service Title</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="serviceTitle_${index}" name="service_title[]" value="${service.service_title}" readonly>
                                            </div>
                                            <input type="hidden" class="form-control" id="serviceID_${index}" name="service_id[]" value="${service.service_id}">
                                        </div>
                                        <div class="col-2">
                                            <label for="rate_${index}">Fixed Rate</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">${currencySymbol}</span>
                                                </div>
                                                <input type="number" class="form-control" id="rate_${index}" name="service_rate[]" value="${service.fixed_rate || ''}" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label for="percentage_${index}">Percentage %</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="percentage_${index}" name="service_percentage[]" value="${service.percentage || ''}" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <label for="charges_description_${index}">Charges Description</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="charges_description_${index}" name="charges_description[]" value="${service.charges_description || ''}" placeholder="Enter Description">
                                            </div>
                                        </div>
                                    </div>
                                `;


                                // Append the generated fields to the specific container
                                $('#dynamicFieldsContainer').append(serviceFieldHTML);
                            });
                            const client = `<input type="hidden" name="client_id" value="${client_id}">`;
                            $('#dynamicFieldsContainer').append(client);
                        } else {
                            // Show "No data found" message
                            $('#dynamicFieldsContainer').append(`
                                <div class="text-center text-muted">No data found for this client.</div>
                            `);
                        }

                        // Show the modal
                        $('#setupModal').modal('show');
                    },
                    error: function (error) {
                        console.log('Error fetching category details: ', error);

                        // Clear previous dynamic fields or messages
                        $('#dynamicFieldsContainer').empty();

                        // Show an error message
                        $('#dynamicFieldsContainer').append(`
                            <div class="text-center text-danger">An error occurred while fetching data.</div>
                        `);
                    }
                });
            });
          
            $(document).on('click', '#inhouseSetupBtn', function () {
                var clientID = $(this).data('id');

                // Make AJAX request to get category details
                $.ajax({
                    data: { client_id: clientID },
                    type: 'GET',
                    url: 'getClientInhouseServicesByID',
                    success: function (response) {
                        // Clear previous dynamic fields or messages
                        $('#inhouseDynamicFieldsContainer').empty();
                        var currencySymbol = '{{ $currency }}';
                        var client_id = response.id;

                        // Get today's date in the format YYYY-MM-DD
                        var today = moment().format('YYYY-MM-DD');

                        // Check if the response contains data
                        if (response.client_in_house_services && response.client_in_house_services.length > 0) {
                            // Loop through the client_in_house_services array and add input fields
                            response.client_in_house_services.forEach((service, index) => {
                                var startDate = service.start_from || today;
                                var endDate = service.end_to || today;
                                var dateRange = startDate + ' / ' + endDate;

                                // Generate HTML for each service
                                const serviceFieldHTML = `
                                    <div class="row mb-3">
                                        <div class="col-3">
                                            <label for="serviceTitle_${index}">Service Title</label>
                                            <input type="text" class="form-control" id="serviceTitle_${index}" name="service_title[]" value="${service.service_title}" readonly>
                                            <input type="hidden" class="form-control" id="service_id_${index}" name="service_id[]" value="${service.additional_service_id}">
                                        </div>
                                        <div class="col-3">
                                            <label for="dates_${index}">From / To</label>
                                            <input type="text" class="form-control date-range-picker" id="dates_${index}" name="dates[]" value="${dateRange}">
                                        </div>
                                        <div class="col-2">
                                            <label for="qty_${index}">Qty</label>
                                            <input type="number" class="form-control" id="qty_${index}" name="qty[]" value="${service.quantity}" placeholder="0">
                                        </div>
                                        <div class="col-2">
                                            <label for="rate_${index}">Rate</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">${currencySymbol}</span>
                                                </div>
                                                <input type="text" class="form-control" id="rate_${index}" name="rate[]" value="${service.rate}" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label for="charges_description_${index}">Description</label>
                                            <input type="text" class="form-control" id="description_${index}" name="description[]" value="${service.description}" placeholder="Enter Description">
                                        </div>
                                    </div>
                                `;

                                // Append the generated fields to the specific container
                                $('#inhouseDynamicFieldsContainer').append(serviceFieldHTML);
                            });

                            // Append hidden client ID
                            const client = `<input type="hidden" name="client_id" value="${client_id}">`;
                            $('#inhouseDynamicFieldsContainer').append(client);
                        } else {
                            // Show "No data found" message
                            $('#inhouseDynamicFieldsContainer').append(`
                                <div class="text-center text-muted">No data found for this client.</div>
                            `);
                        }

                        // Show the modal
                        $('#inhouseSetupModal').modal('show');

                        // Initialize Date Range Picker for dynamically created inputs
                        $('#inhouseSetupModal').on('shown.bs.modal', function () {
                            $('.date-range-picker').each(function () {
                                var input = $(this);
                                var initialDates = input.val().split(' / ');
                                var startDate = initialDates[0] || today;
                                var endDate = initialDates[1] || today;

                                input.daterangepicker({
                                    autoUpdateInput: false,
                                    locale: {
                                        format: 'YYYY-MM-DD',
                                        cancelLabel: 'Clear'
                                    },
                                    startDate: startDate,
                                    endDate: endDate,
                                    parentEl: '#inhouseSetupModal' // Attach to modal to fix z-index issue
                                });

                                input.on('apply.daterangepicker', function (ev, picker) {
                                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' / ' + picker.endDate.format('YYYY-MM-DD'));
                                });

                                input.on('cancel.daterangepicker', function () {
                                    $(this).val('');
                                });
                            });
                        });
                    },

                    error: function (error) {
                        console.error('Error fetching category details:', error);

                        // Clear previous dynamic fields or messages
                        $('#inhouseDynamicFieldsContainer').empty();

                        // Show an error message
                        $('#inhouseDynamicFieldsContainer').append(`
                            <div class="text-center text-danger">An error occurred while fetching data.</div>
                        `);
                    }
                });
            });

            $(document).on('submit', '#editServiceRatesForm', function (e) {
                e.preventDefault(); // Prevent the default form submission behavior

                // Serialize form data
                const formData = $(this).serialize();

                // Show the spinner and disable the button
                const submitButton = $('#setUpSubmitButton');
                const buttonSpinner = $('#setUpbuttonSpinner');
                const buttonText = $('#setUpbuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                // Make AJAX POST request to save data
                $.ajax({
                    url: 'saveClientServices', // Replace with your backend route
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            // Show success message
                            toastr.success('Services saved successfully!');
                            // Close the modal
                            $('#setupModal').modal('hide');
                            // Optionally, refresh the data or table
                        } else {
                            // Show error message
                            toastr.error(response.message || 'Failed to save services.');
                        }
                    },
                    error: function (error) {
                        console.error('Error saving services: ', error);
                        toastr.error('An error occurred while saving the services.');
                    },
                    complete: function () {
                        // Hide the spinner and re-enable the button
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Submit');
                        submitButton.prop('disabled', false);
                    }
                });
            });
            
            $(document).on('submit', '#editInhouseServiceRatesForm', function (e) {
                e.preventDefault(); // Prevent the default form submission behavior

                // Serialize form data
                const formData = $(this).serialize();

                // Show the spinner and disable the button
                const submitButton = $('#inhouseSubmitButton');
                const buttonSpinner = $('#inhousebuttonSpinner');
                const buttonText = $('#inhousebuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                // Make AJAX POST request to save data
                $.ajax({
                    url: 'saveClientInhouseServices', // Replace with your backend route
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            // Show success message
                            toastr.success('Inhouse Services saved successfully!');
                            // Close the modal
                            $('#inhouseSetupModal').modal('hide');
                            // Optionally, refresh the data or table
                        } else {
                            // Show error message
                            toastr.error(response.message || 'Failed to save inhouse services.');
                        }
                    },
                    error: function (error) {
                        console.error('Error saving inhouse services: ', error);
                        toastr.error('An error occurred while saving the services.');
                    },
                    complete: function () {
                        // Hide the spinner and re-enable the button
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Submit');
                        submitButton.prop('disabled', false);
                    }
                });
            });

            $(document).on('submit', '#editFormClient', function (e){
                e.preventDefault();

                var formData = new FormData();

                // Show the spinner and disable the button
                const submitButton = $('#editClientSubmitButton');
                const buttonSpinner = $('#editClientbuttonSpinner');
                const buttonText = $('#editClientbuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                // Append regular fields
                formData.append('editClientId', $('#editClientId').val());
                formData.append('editClientName', $('#editClientName').val());
                formData.append('editTagName', $('#editTagName').val());
                formData.append('editClientAccount', $('#editClientAccount').val());
                formData.append('editClientEmail', $('#editClientEmail').val());
                formData.append('editOutgoingNumber', $('#editOutgoingNumber').val());
                formData.append('editIncomingNumber', $('#editIncomingNumber').val());
                formData.append('editPhone', $('#editPhone').val());
                formData.append('editNote', $('#editNote').val());
                formData.append('editClientFrequency', $('#editClientFrequency').val());
                formData.append('editAddress', $('#editAddress').val());
                formData.append('editTrunkID', $('#editTrunkID').val());
                formData.append('editEnableVatTaxValue', $('#enableVatTaxValueEdit').val());
                formData.append('editFixedLineServiceValue', $('#fixedLineServiceValueEdit').val());
                
                if($('#fixedLineServiceValueEdit').val() == '1'){
                    formData.append('editFixedLineNumber', $('#fixedLineServiceNumberEdit').val());
                }

                // Append array fields
                var services = $('#editServices').val();
                if (services) {
                    services.forEach(function (service, index) {
                        formData.append('editServices[' + index + ']', service);
                    });
                }

                var companies = $('#editCompany').val();
                if (companies) {
                    companies.forEach(function (company, index) {
                        formData.append('editCompanies[' + index + ']', company);
                    });
                }

                var editInhouseServices = $('#editInhouseServices').val();
                if (editInhouseServices) {
                    editInhouseServices.forEach(function (service, index) {
                        formData.append('editInhouseServices[' + index + ']', service);
                    });
                }

                // Add hidden method for PUT
                formData.append('_method', 'PUT');

                $.ajax({
                    url: '{{ route("updateClient") }}', // Ensure this route is correct
                    type: 'POST', // Use POST to simulate PUT/PATCH
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    processData: false, // Important: Prevent jQuery from processing FormData
                    contentType: false, // Important: Prevent jQuery from setting contentType
                    success: function (response) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function (xhr) {
                        try {
                            var errors = JSON.parse(xhr.responseText).errors;
                            $('#editClientAccountError').text(errors.editClientAccount ? errors.editClientAccount[0] : '');
                            $('#editClientNameError').text(errors.editClientName ? errors.editClientName[0] : '');
                            $('#editClientEmailError').text(errors.editClientEmail ? errors.editClientEmail[0] : '');
                            $('#editPhoneError').text(errors.editPhone ? errors.editPhone[0] : '');
                            $('#editLandlineError').text(errors.editLandline ? errors.editLandline[0] : '');
                            $('#editCompanyError').text(errors.editCompany ? errors.editCompany[0] : '');
                            $('#editServicesError').text(errors.editServices ? errors.editServices[0] : '');
                            $('#editAddressError').text(errors.editAddress ? errors.editAddress[0] : '');
                            $('#editNoteError').text(errors.editNote ? errors.editNote[0] : '');
                            $('#editClientFrequencyError').text(errors.editClientFrequency ? errors.editClientFrequency[0] : '');
                            $('#editInhouseServicesError').text(errors.editInhouseServices ? errors.editInhouseServices[0] : '');
                            $('#editTrunkIDError').text(errors.editTrunkID ? errors.editTrunkID[0] : '');
                        } catch (e) {
                            toastr.error('Something went wrong, Please try again');
                        } finally {
                            // Hide loading indicator
                            $("#loadingIndicator2").addClass("d-none");
                        }
                    },
                    complete: function () {
                        // Hide the spinner and re-enable the button
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Submit');
                        submitButton.prop('disabled', false);
                    }
                });
            });

            $(document).on('submit', '#addClientForm', function (e){
                e.preventDefault();

                var formData = new FormData();

                // Show the spinner and disable the button
                const submitButton = $('#addClientSubmitButton');
                const buttonSpinner = $('#addClientbuttonSpinner');
                const buttonText = $('#addClientbuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');
                
                // Append regular fields
                formData.append('clientName', $('#clientName').val());
                formData.append('clientAccountNumber', $('#clientAccountNumber').val());
                formData.append('clientEmail', $('#clientEmail').val());
                formData.append('phone', $('#phone').val());
                formData.append('outgoing_number', $('#outgoing_number').val());
                formData.append('incoming_number', $('#incoming_number').val());
                formData.append('address', $('#address').val());
                formData.append('frequency', $('#frequency').val());
                formData.append('note', $('#note').val());
                formData.append('tag_name', $('#tag_name').val());
                formData.append('trunkID', $('#trunkID').val());
                formData.append('enableVatTaxValue', $('#enableVatTaxValue').val());
                formData.append('fixedLineServiceValue', $('#fixedLineServiceValue').val());
                formData.append('fixedLineServiceNumber', $('#fixedLineServiceNumber').val());

                if($('#fixedLineServiceNumber').val() == '1'){
                    formData.append('fixedLineNumber', $('#fixedLineServiceNumber').val());
                }
                
                // Append array fields
                var services = $('#services').val();
                if (services) {
                    services.forEach(function(service, index) {
                        formData.append('services[' + index + ']', service);
                    });
                }
                
                var companies = $('#company').val();
                if (companies) {
                    companies.forEach(function(company, index) {
                        formData.append('companies[' + index + ']', company);
                    });
                }
                
                var inhouseServices = $('#inhouseServices').val();
                if (inhouseServices) {
                    inhouseServices.forEach(function(service, index) {
                        formData.append('inhouseServices[' + index + ']', service);
                    });
                }

                // Send AJAX request
                $.ajax({
                    url: "{{ route('clients.store') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        toastr.success(response.message);
                        $('#clientModal').modal('hide');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        try {
                            var errors = JSON.parse(xhr.responseText).errors;

                            // Display validation errors in the respective <span> tags
                            $('#clientAccountNumberError').text(errors.clientAccountNumber ? errors.clientAccountNumber[0] : '');
                            $('#clientNameError').text(errors.clientName ? errors.clientName[0] : '');
                            $('#clientEmailError').text(errors.clientEmail ? errors.clientEmail[0] : '');
                            $('#phoneError').text(errors.phone ? errors.phone[0] : '');
                            $('#landlineError').text(errors.landline ? errors.landline[0] : '');
                            $('#servicesError').text(errors.services ? errors.services[0] : '');
                            $('#vendorsError').text(errors.vendors ? errors.vendors[0] : '');
                            $('#addressError').text(errors.address ? errors.address[0] : '');
                            $('#frequencyError').text(errors.frequency ? errors.frequency[0] : '');
                            $('#noteError').text(errors.note ? errors.note[0] : '');
                            $('#tag_nameError').text(errors.tag_name ? errors.tag_name[0] : '');
                            $('#trunkIDError').text(errors.trunkID ? errors.trunkID[0] : '');
                        } catch (e) {
                            toastr.error('Something went wrong, Please try again');
                        }finally {
                            // Hide loading indicator
                            $("#loadingIndicator").addClass("d-none");
                        }
                    },
                    complete: function () {
                        // Hide the spinner and re-enable the button
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Submit');
                        submitButton.prop('disabled', false);
                    }
                });
            });

        });

        function updateCheckboxValues() {
            var enableVatTax = document.getElementById("enableVatTax");
            var fixedLineService = document.getElementById("fixedLineService");

            // Update hidden input for 'enableVatTax' checkbox
            var enableVatTaxValue = document.getElementById("enableVatTaxValue");
            enableVatTaxValue.value = enableVatTax.checked ? "1" : "0";

            // Update hidden input for 'fixedLineService' checkbox
            var fixedLineServiceValue = document.getElementById("fixedLineServiceValue");
            fixedLineServiceValue.value = fixedLineService.checked ? "1" : "0";

            // Show or hide the fixed line number input div based on 'fixedLineService' checkbox
            if (fixedLineService.checked) {
                $('#fixedLineNumberDiv').css('display', 'block');
                $('#fixedLineServiceNumber').attr('required', 'required');  // Set the required attribute
            } else {
                $('#fixedLineNumberDiv').css('display', 'none');
                $('#fixedLineServiceNumber').removeAttr('required');  // Remove the required attribute when hidden
            }
        }
        
        function updateCheckboxValuesForEdit() {
            var enableVatTaxEdit = document.getElementById("enableVatTaxEdit");
            var fixedLineServiceEdit = document.getElementById("fixedLineServiceEdit");

            // Update hidden input for 'enableVatTax' checkbox
            var enableVatTaxValueEdit = document.getElementById("enableVatTaxValueEdit");
            enableVatTaxValueEdit.value = enableVatTaxEdit.checked ? "1" : "0";

            // Update hidden input for 'fixedLineService' checkbox
            var fixedLineServiceValueEdit = document.getElementById("fixedLineServiceValueEdit");
            fixedLineServiceValueEdit.value = fixedLineServiceEdit.checked ? "1" : "0";

            // Show or hide the fixed line number input div based on 'fixedLineService' checkbox
            if (fixedLineServiceEdit.checked) {
                $('#EditFixedLineNumberDiv').css('display', 'block');
                $('#fixedLineServiceNumberEdit').attr('required', 'required');  // Set the required attribute
            } else {
                $('#EditFixedLineNumberDiv').css('display', 'none');
                $('#fixedLineServiceNumberEdit').removeAttr('required');  // Remove the required attribute when hidden
            }
        }

        function getServicesByCompany(type) {
            // Get the company ID based on the type (new or edit)
            var companyId = (type === 'new') ? $('#company').val() : $('#editCompany').val();

            if (companyId) {
                $.ajax({
                    url: '{{ route("getServicesByCompany") }}',
                    method: 'GET',
                    data: { companyId: companyId },
                    success: function(response) {
                        // Determine the correct select element based on the type (new or edit)
                        var $servicesSelect = (type === 'new') ? $('#services') : $('#editServices');
                        $servicesSelect.empty(); // Clear existing options

                        // Loop through the services and append them to the select element
                        $.each(response, function(index, service) {
                            var option = $('<option></option>')
                                .attr('value', service.id)
                                .text(service.title);

                            // Check if the service id exists in the `services` array and select the option if it does
                            if (Array.isArray(services) && services.includes(service.id)) {
                                option.prop('selected', true); // Select the option if it exists in the services array
                            }

                            // Append the option to the select dropdown
                            $servicesSelect.append(option);
                        });
                    },
                    error: function() {
                        alert('Failed to fetch services. Please try again.');
                    }
                });
            } else {
                $('#servicesContainer').html(''); // Clear the services if no company is selected
            }
        }

        function updateServicesDropdown(selectedCompanies, selectedServices) {
            var $servicesSelect = $('#editServices'); // Target the "editServices" dropdown

            // Save the current selections
            var currentSelections = $servicesSelect.val() || [];

            // Clear the dropdown
            $servicesSelect.empty();

            if (!selectedCompanies || selectedCompanies.length === 0) {
                console.log("No companies selected.");
                return;
            }

            // Fetch services based on the selected companies
            $.ajax({
                url: '{{ route("getServicesByCompany") }}',
                method: 'GET',
                data: { companyId: selectedCompanies },
                success: function (response) {
                    console.log("AJAX Response:", response);

                    // Convert the response object into an array
                    var servicesArray = Object.values(response);

                    if (Array.isArray(servicesArray) && servicesArray.length > 0) {
                        servicesArray.forEach(function (service) {
                            if (service.id && service.title) {
                                // Create a new option for the dropdown
                                var option = $('<option></option>')
                                    .attr('value', service.id)
                                    .text(service.title);

                                // Pre-select if the service is in selectedServices or currentSelections
                                if (selectedServices.includes(service.id) || currentSelections.includes(String(service.id))) {
                                    option.prop('selected', true);
                                }

                                $servicesSelect.append(option);
                            } else {
                                console.warn("Invalid service data:", service);
                            }
                        });

                        // Trigger change event (for libraries like Select2)
                        $servicesSelect.trigger('change');
                        $servicesSelect.select2({ placeholder: "Select Services", allowClear: true });
                    } else {
                        console.warn("No services found for the selected companies.");
                        alert('No services available for the selected companies.');
                    }
                },
                error: function () {
                    alert('Failed to fetch services. Please try again.');
                }
            });
        }

        function changeStatus(id) {
            // Show confirmation modal
            $('#statusConfirmationModalClient').modal('show');

            // Handle status change on confirmation
            $('#confirmStatusChange').on('click', function () {
                // Show the spinner and disable the button
                const submitButton = $('#confirmStatusChange');
                const buttonSpinner = $('#confirmStatusbuttonSpinner');
                const buttonText = $('#confirmStatusbuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    url: '{{ url('changeClientStatus') }}/' + id,
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        // Add any other data you need to pass to the server
                    },
                    success: function (response) {
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function (error) {
                        // Handle error
                        console.error(error);
                    },
                    complete: function () {
                        // Hide the spinner and re-enable the button
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Change Status');
                        submitButton.prop('disabled', false);
                    }
                });

                // Close the modal
                $('#statusConfirmationModalClient').modal('hide');
            });
        }

        function deleteClient(clientID) {
            // Show confirmation modal
            $('#deleteConfirmationModal').modal('show');

            // Handle deletion on confirmation
            $('#confirmDelete').on('click', function () {
                // Show the spinner and disable the button
                const submitButton = $('#confirmDelete');
                const buttonSpinner = $('#deletebuttonSpinner');
                const buttonText = $('#deletebuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    data: { client_id: clientID },
                    url: '{{ route('deleteClientByID') }}',
                    type: 'get',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        // Handle success (e.g., refresh the page)
                        location.reload();
                    },
                    error: function (error) {
                        // Handle error
                        console.error(error);
                    },
                    complete: function () {
                        // Hide the spinner and re-enable the button
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Delete');
                        submitButton.prop('disabled', false);
                    }
                });

                // Close the modal
                $('#deleteConfirmationModal').modal('hide');
            });
        }

    </script>
@endsection