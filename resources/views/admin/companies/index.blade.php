@extends('layouts.app')

<!-- Select2 CSS CDN -->
<title>{{ getSetting('app_name', env('APP_NAME')) }} | Vendors Management</title>

@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Vendors Management</h1>
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
                                                       {{-- <h3 class="card-title">DataTable with minimal features & hover style</h3> --}}
                            <button type="button" style="background: linear-gradient(to right, #007bff, #0056b3); float: right" class="btn btn-primary" data-toggle="modal" data-target="#companyModal">
                               <i class="fa fa-plus"></i> Add Vendor
                            </button>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="myTable" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Sr.</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    {{-- <th>Phone</th> --}}
                                    <th width="30%">Services</th>
                                    {{-- <th>Address</th> --}}
                                    <th>Status</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>

    <div class="modal fade" id="companyModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Vendor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addVendorForm">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="CompanyName">Name <span class="required">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="CompanyName" placeholder="Enter Vendor Name">
                                </div>
                                <span id="CompanyNameError" class="text-danger"></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="CompanyEmail">Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" class="form-control" id="CompanyEmail" placeholder="contact@example.com">
                                </div>
                                <span id="CompanyEmailError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="phone">Phone</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="tel" class="form-control" id="phone" placeholder="Enter Phone">
                                </div>
                                <span id="phoneError" class="text-danger"></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="landline">Landline</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                    </div>
                                    <input type="tel" class="form-control" id="landline" placeholder="Enter Landline">
                                </div>
                                <span id="landlineError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label for="services">Services <span class="required">*</span></label>
                                <div class="select2-purple">
                                    <select class="select2" multiple="multiple" name="services[]" id="services" data-placeholder="Select Services" data-dropdown-css-class="select2-purple" style="width: 100%;">
                                        @foreach($services as $service)
                                        <option value="{{$service->id}}">{{ucwords($service->title)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="servicesError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                <textarea class="form-control" id="address" rows="2" placeholder="Enter Address"></textarea>
                            </div>
                            <span id="addressError" class="text-danger"></span>
                        </div>
                        <div class="form-group float-right">
                            <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button type="submit" id="addVenderSubmitButton" class="btn btn-primary">
                                <span id="addVenderbuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span id="addVenderbuttonText">Submit</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="statusConfirmationModalCompany" tabindex="-1" role="dialog" aria-labelledby="statusConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusConfirmationModalLabel">Confirm Status Change</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to change the status of this Vendor?
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
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDelete" class="btn btn-danger">
                        <span id="deletebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="deletebuttonText">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Vendor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Edit form -->
                    <form id="editFormCompany">
                        <input type="hidden" name="id" id="editCategoryId">
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label for="editCompanyName">Name <span class="required">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="editCompanyName" name="editCompanyName" placeholder="Enter Vendor Name">
                                </div>
                                <span id="editCompanyNameError" class="text-danger"></span>
                            </div>
                            <div class="col-6 mb-2">
                                <label for="editCompanyEmail">Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" class="form-control" id="editCompanyEmail" name="editCompanyEmail" placeholder="contact@example.com">
                                </div>
                                <span id="editCompanyEmailError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label for="editPhone">Phone</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                    </div>
                                    <input type="tel" class="form-control" id="editPhone" name="editPhone" placeholder="Enter Phone">
                                </div>
                                <span id="editPhoneError" class="text-danger"></span>
                            </div>
                            <div class="col-6 mb-2">
                                <label for="editLandline">Landline</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="tel" class="form-control" id="editLandline" name="editLandline" placeholder="Enter Landline">
                                </div>
                                <span id="editLandlineError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label for="editServices">Services <span class="required">*</span></label>
                                <div class="select2-blue">
                                    <select class="select2" multiple="multiple" name="editServices[]" 
                                            id="editServices" data-placeholder="Select Services" 
                                            data-dropdown-css-class="select2-blue" style="width: 100%;">
                                        @foreach($services as $service)
                                            <option value="{{$service->id}}">{{ucwords($service->title)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="editServicesError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editAddress">Address</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                <textarea class="form-control" id="editAddress" name="editAddress" rows="2" placeholder="Enter Address"></textarea>
                            </div>
                            <span id="editAddressError" class="text-danger"></span>
                        </div>
                        <div class="form-group float-right">
                            <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button type="submit" id="editVendorSubmitButton" class="btn btn-primary">
                                <span id="editVendorbuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span id="editVendorbuttonText">Submit</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="setupModal" tabindex="-1" role="dialog" aria-labelledby="setupModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setupModalLabel">Setup</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Edit form -->
                    <form id="companySetupForm">
                        <!-- Container for dynamic rows -->
                        <div class="dynamic-rows-container"></div>
                        <button type="submit" style="float: right" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
    
@endsection
@section('scripts')
    <script type="text/javascript">
        $('#services').select2();
        $('#editServices').select2();

        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        //to get companies data
        $(function () {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                searching:true,
                ajax: "{{ route('getCompanies') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    // {data: 'phone', name: 'phone'},
                    {data: 'services', name: 'services'},
                    // {data: 'landline_number', name: 'landline_number'},
                    // {data: 'address', name: 'address'},
                    {data: 'status', name: 'status'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action'},
                ]
            });
        });

        $(document).ready(function () {
            $('#services').change(function () {
                var serviceId = $(this).val();

                $.ajax({
                    url: '/get-sub-services/' + serviceId, 
                    type: 'GET',
                    success: function (data) {
                        $('#sub_service').empty();

                        $.each(data, function (key, value) {
                            $('#sub_service').append('<option value="' + value.id + '">' + value.title + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching sub services: ' + error);
                    }
                });
            });

            $(document).on('click', '#editBtn', function () {
                var companyId = $(this).data('id');

                // Make AJAX request to get category details
                $.ajax({
                    type: 'GET',
                    data: { id: companyId },
                    url: '{{ route("vendors.editCompany") }}', 
                    success: function (response) {
                        var company = response.company;
                        $('#editModal #editCategoryId').val(company.id);
                        $('#editModal #editCompanyName').val(company.company_name);
                        $('#editModal #editCompanyEmail').val(company.company_email);
                        $('#editModal #editCompanyAddress').val(company.company_address);
                        $('#editModal #editPhone').val(company.company_phone_number);
                        $('#editModal #editLandline').val(company.company_landline_number);
                        $('#editModal #editAddress').val(company.company_address);
                      
                        var company = response.company;

                        var selectedServices = company.services.map(service => service.id);

                        $('#editModal #editServices').val(selectedServices);

                        $('#editModal #editServices').trigger('change');

                        // Show the modal
                        $('#editModal').modal('show');
                    },
                    error: function (error) {
                        console.log('Error fetching company details: ', error);
                    }
                });
            });

            $(document).on('submit', '#addVendorForm', function (e) {
                e.preventDefault();

                // Show the spinner and disable the button
                const submitButton = $('#addVenderSubmitButton');
                const buttonSpinner = $('#addVenderbuttonSpinner');
                const buttonText = $('#addVenderbuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                var formData = {
                    CompanyName: $('#CompanyName').val(),
                    CompanyEmail: $('#CompanyEmail').val(),
                    phone: $('#phone').val(),
                    landline: $('#landline').val(),
                    services: $('#services').val(),
                    sub_service: $('#sub_service').val(),
                    address: $('#address').val()
                };
                $.ajax({
                    url: "{{ route('vendors.store') }}", 
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: formData,
                    success: function (response) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function (xhr, status, error) {
                        try {
                            var errors = JSON.parse(xhr.responseText).errors;
                            $('#CompanyNameError').text(errors.CompanyName ? errors.CompanyName[0] : '');
                            $('#CompanyEmailError').text(errors.CompanyEmail ? errors.CompanyEmail[0] : '');
                            $('#servicesError').text(errors.services ? errors.services[0] : '');
                            $('#sub_serviceError').text(errors.sub_service ? errors.sub_service[0] : '');
                        } catch (e) {
                            toastr.error('Something went wrong, Please try again');
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

            $(document).on('submit', '#editFormCompany', function (e) {
                e.preventDefault();

                // Show the spinner and disable the button
                const submitButton = $('#editVendorSubmitButton');
                const buttonSpinner = $('#editVendorbuttonSpinner');
                const buttonText = $('#editVendorbuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    url: '{{ route("vendors.updateCompany") }}',
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $(this).serialize(),
                    success: function (response) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        try {
                            var errors = JSON.parse(xhr.responseText).errors;
                            $('#editCompanyNameError').text(errors.editCompanyName ? errors.editCompanyName[0] : '');
                            $('#editCompanyEmailError').text(errors.editCompanyEmail ? errors.editCompanyEmail[0] : '');
                            $('#editPhoneError').text(errors.editPhone ? errors.editPhone[0] : '');
                            $('#editLandlineError').text(errors.editLandline ? errors.editLandline[0] : '');
                            $('#editServicesError').text(errors.editServices ? errors.editServices[0] : '');
                            $('#sub_serviceError').text(errors.sub_service ? errors.sub_service[0] : '');
                            $('#editAddressError').text(errors.editAddress ? errors.editAddress[0] : '');
                        } catch (e) {
                            toastr.error('Something went wrong, Please try again');
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

        function changeStatus(id) {
            $('#statusConfirmationModalCompany').modal('show');

            $('#confirmStatusChange').on('click', function () {
                // Show the spinner and disable the button
                const submitButton = $('#confirmStatusChange');
                const buttonSpinner = $('#confirmStatusbuttonSpinner');
                const buttonText = $('#confirmStatusbuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    url: '{{ url("vendorsChangeStatus") }}',
                    type: 'PUT',
                    data: {
                        id : id,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        if(response.success){
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        }else{
                            toastr.error(response.message);
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    },
                    complete: function () {
                        // Hide the spinner and re-enable the button
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Change Status');
                        submitButton.prop('disabled', false);
                    }
                });
            });
        }

        function deleteCompany(id) {
            // Show confirmation modal
            $('#deleteConfirmationModal').modal('show');

            $('#confirmDelete').on('click', function () {
                // Show the spinner and disable the button
                const submitButton = $('#confirmDelete');
                const buttonSpinner = $('#deletebuttonSpinner');
                const buttonText = $('#deletebuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    url: '{{ route("companyDestroy") }}',
                    data: {id : id},
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function (error) {
                        console.error(error);
                    },
                    complete: function () {
                        // Hide the spinner and re-enable the button
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Delete');
                        submitButton.prop('disabled', false);
                    }
                });
            });
        }
    </script>
@endsection
