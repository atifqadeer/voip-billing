@extends('layouts.app')
<title>{{ getSetting('app_name', env('APP_NAME')) }} | Tax Management</title>

@section('content')
    {{--    <div class="container">--}}


    <!-- Content Header (Page header) -->
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Tax Management</h1>
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
                                {{--                                <h3 class="card-title">DataTable with minimal features & hover style</h3>--}}

                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addTaxModal">
                                  <i class="fa fa-plus"></i>  Add Tax
                                </button>

                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="myTable" class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Sr.</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Rate</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="modal fade" id="addTaxModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Tax</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('taxes.store')}}" method="post" id="taxForm">
                            @csrf
                            <div class="form-group">
                                <label for="name" class="col-form-label">Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter Tax Name">
                                <span id="nameError" class="text-danger"></span>
                            </div>
                            <div class="row form-group">
                                <div class="col-6">
                                    <label for="tax_rate" class="col-form-label">Rate <span class="required">*</span></label>
                                    <input type="number" class="form-control" name="tax_rate" id="tax_rate" placeholder="Enter Rate">
                                    <span id="taxRateError" class="text-danger"></span>
                                </div>
                                <div class="col-6">
                                    <label for="tax_type" class="col-form-label">Tax Type <span class="required">*</span></label>
                                    <select class="form-control" name="tax_type" id="tax_type">
                                        <option value="" selected disabled>Select Type</option>
                                        <option value="fixed">Fixed</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                    <span id="taxTypeError" class="text-danger"></span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" id="addTaxSubmitButton" class="btn btn-primary">
                            <span id="addTaxbuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span id="addTaxbuttonText">Submit</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add this modal at the end of your Blade file -->
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
                        Are you sure you want to delete this tax?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDelete" class="btn btn-danger">
                            <span id="confirmDeletebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span id="confirmDeletebuttonText">Delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add this modal at the end of your Blade file block Or un block code -->
        <div class="modal fade" id="statusConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="statusConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statusConfirmationModalLabel">Confirm Status Change</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to change the status of this tax?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmStatusChange" class="btn btn-danger">
                            <span id="confirmStatusChangebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span id="confirmStatusChangebuttonText">Change Status</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Tax</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Edit form -->
                        <form id="editForm">
                            <input type="hidden" name="id" id="editTaxId">
                            <div class="form-group">
                                <label for="editTaxName" class="col-form-label">Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="editTaxName" name="editTaxName" required>
                                <span id="editTaxNameError" class="text-danger"></span>
                            </div>
                            <div class="row form-group">
                                <div class="col-6">
                                    <label for="recipient-name" class="col-form-label">Rate <span class="required">*</span></label>
                                    <input type="float" class="form-control" name="editRate" id="editRate" placeholder="Enter Rate">
                                    <span id="editRateError" class="text-danger"></span>
                                </div>
                                <div class="col-6">
                                    <label for="editTaxType" class="col-form-label">Tax Type <span class="required">*</span></label>
                                    <select class="form-control" name="editTaxType" id="editTaxType">
                                        <option value="" selected disabled>Select Type</option>
                                        <option value="fixed">Fixed</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                    <span id="editTaxTypeError" class="text-danger"></span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" id="editTaxSubmitButton" class="btn btn-primary">
                            <span id="editTaxbuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span id="editTaxbuttonText">Submit</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

@endsection
@section('scripts')
    <script type="text/javascript">
        $('.select2').select2();
        
        $(function () {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('getTaxes') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'type', name: 'type'},
                    {data: 'rate', name: 'rate'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action'},
                ]
            });
        });

        $(document).ready(function () {
            //start add tax modal
                $("#addTaxSubmitButton").click(function () {
                    $("#taxForm").submit();
                });

                $("#taxForm").submit(function (e) {
                    e.preventDefault();

                    // Show the spinner and disable the button
                    const submitButton = $('#addTaxSubmitButton');
                    const buttonSpinner = $('#addTaxbuttonSpinner');
                    const buttonText = $('#addTaxbuttonText');
                    submitButton.prop('disabled', true);
                    buttonSpinner.removeClass('d-none');
                    buttonText.text('Processing...');

                    $.ajax({
                        url: $(this).attr('action'),
                        type: $(this).attr('method'),
                        data: $(this).serialize(),

                        success: function (response) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function (xhr, status, error) {
                            try {
                                var errors = JSON.parse(xhr.responseText).errors;

                                // Display validation errors in the respective <span> tags
                                $('#nameError').text(errors.name ? errors.name[0] : '');
                                $('#taxRateError').text(errors.rate ? errors.rate[0] : '');
                                $('#taxTypeError').text(errors.tax_type ? errors.tax_type[0] : '');
                            } catch (e) {
                                // Handle the case where the response is not valid JSON
                                toastr.error('Something went wrong. Please try again.');
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
            //end add tax modal

            //start edit tax
                $("#editTaxSubmitButton").click(function () {
                    $("#editForm").submit();
                });

                $('#editForm').on('submit', function (e) {
                    e.preventDefault();

                    // Show the spinner and disable the button
                    const submitButton = $('#editTaxSubmitButton');
                    const buttonSpinner = $('#editTaxbuttonSpinner');
                    const buttonText = $('#editTaxbuttonText');
                    submitButton.prop('disabled', true);
                    buttonSpinner.removeClass('d-none');
                    buttonText.text('Processing...');

                    $.ajax({
                        url: '{{ route("updateTax") }}',
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
                        error: function (error) {
                            try {
                                var errors = JSON.parse(xhr.responseText).errors;
                                $('#editTaxNameError').text(errors.editTaxName ? errors.editTaxName[0] : '');
                                $('#editRateError').text(errors.editRate ? errors.editRate[0] : '');
                                $('#editTaxTypeError').text(errors.editTaxType ? errors.editTaxType[0] : '');
                            } catch (e) {
                                // Handle the case where the response is not valid JSON
                                toastr.error('Something went wrong. Please try again.');
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
            //end edit tax
        });

        function deleteTax(id) {
            // Show confirmation modal
            $('#deleteConfirmationModal').modal('show');

            // Handle deletion on confirmation
            $('#confirmDelete').on('click', function () {
                // Show the spinner and disable the button
                const submitButton = $('#confirmDelete');
                const buttonSpinner = $('#confirmDeletebuttonSpinner');
                const buttonText = $('#confirmDeletebuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    url: '{{ route("destroyTax") }}',
                    type: 'DELETE',
                    data: {
                        id : id,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function (error) {
                        toastr.error(response.message);
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

        function editTax(taxId, name, rate, type, currency, appliesTo) {
            // Populate modal fields with data
            $('#editTaxId').val(taxId);
            $('#editTaxName').val(name);
            $('#editRate').val(rate);
            $('#editTaxType').val(type);
            $('#editCurrency').val(currency);

            // Show the modal
            $('#editModal').modal('show');
        }

        function changeTaxStatus(id) {
            // Show confirmation modal
            $('#statusConfirmationModal').modal('show');

            $('#confirmStatusChange').on('click', function () {
                // Show the spinner and disable the button
                const submitButton = $('#confirmStatusChange');
                const buttonSpinner = $('#confirmStatusChangebuttonSpinner');
                const buttonText = $('#confirmStatusChangebuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    url: '{{ route("taxChangeStatus") }}',
                    type: 'PUT',
                    data: {
                        id : id,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function (error) {
                        toastr.error(response.message);
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
    </script>

@endsection
