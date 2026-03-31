@extends('layouts.app')
<title>{{ getSetting('app_name', env('APP_NAME')) }} | Inhouse Services</title>

@section('content')
    <!-- Content Header (Page header) -->
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Inhouse Services</h1>
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
                                {{-- <h3 class="card-title">DataTable with minimal features & hover style</h3>--}}
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addNewInhouseServiceModal">
                                  <i class="fa fa-plus"></i>  Add Inhouse Service
                                </button>
                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="myTable" class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Sr.</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Frequency</th>
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
    </div>

    <div class="modal fade" id="addNewInhouseServiceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Inhouse Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('additional_services.store')}}" method="post" id="addNewInhouseServiceForm">
                        @csrf
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Enter Service Name">
                            <span id="titleError" class="text-danger"></span>
                        </div>
                        <div class="row form-group">
                            <div class="col-6">
                                <label for="recipient-name" class="col-form-label">Rate <span class="required">*</span></label>
                                <input type="text" class="form-control rateInput" name="rate" id="rate" placeholder="Enter Rate">
                                <span id="rateError" class="text-danger"></span>
                            </div>
                            <div class="col-6">
                                <label for="frequency" class="col-form-label">Frequency <span class="required">*</span></label>
                                <select class="form-control" name="frequency" id="frequency" required>
                                    <option value="" disabled selected>Select Frequency</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="annually">Annually</option>
                                </select>
                                <span id="frequencyError" class="text-danger"></span> <!-- Error message for frequency -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Enter Description"></textarea>
                            <span id="descriptionError" class="text-danger"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="addInhouseSubmitButton" class="btn btn-primary">
                        <span id="addInhousebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="addInhousebuttonText">Submit</span>
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
                    Are you sure you want to delete this record?
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
                    Are you sure you want to change the status of this category?
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Inhouse Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Edit form -->
                    <form id="editForm">
                        <input type="hidden" name="id" id="editServiceId">
                        <div class="form-group">
                            <label for="editServiceName">Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="editServiceName" name="editServiceName" required>
                            <span id="editServiceNameError" class="text-danger"></span>
                        </div>
                        <div class="row form-group">
                            <div class="col-6">
                                <label for="recipient-name" class="col-form-label">Rate <span class="required">*</span></label>
                                <input type="text" class="form-control rateInput" name="editRate" id="editRate" placeholder="Enter Rate">
                                <span id="editRateError" class="text-danger"></span>
                            </div>
                            <div class="col-6">
                                <label for="editFrequency" class="col-form-label">Frequency <span class="required">*</span></label>
                                <select class="form-control" name="editFrequency" id="editFrequency" required data-placeholder="Select Frequency" style="width: 100%;">
                                    <option value="" disabled selected>Select Frequency</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="annually">Annually</option>
                                </select>
                                <span id="editFrequencyError" class="text-danger"></span> <!-- Error message for frequency -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editDescription" class="col-form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="editDescription"></textarea>
                            <span id="editDescriptionError" class="text-danger"></span>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="editInhouseSubmitButton" class="btn btn-primary">
                        <span id="editInhousebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="editInhousebuttonText">Submit</span>
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
                ajax: "{{ route('getAdditionalServices') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'description', name: 'description'},
                    {data: 'frequency', name: 'frequency'},
                    {data: 'rate', name: 'rate'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action'},
                ]
            });
        });

        $(document).ready(function () {
            $("#addInhouseSubmitButton").click(function () {
                $("#addNewInhouseServiceForm").submit();
            });

            $("#editInhouseSubmitButton").click(function () {
                $("#editForm").submit();
            });

            $("#addNewInhouseServiceForm").submit(function (e) {
                e.preventDefault();
                // Show the spinner and disable the button
                const submitButton = $('#addInhouseSubmitButton');
                const buttonSpinner = $('#addInhousebuttonSpinner');
                const buttonText = $('#addInhousebuttonText');
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
                            $('#titleError').text(errors.title ? errors.title[0] : '');
                            $('#rateError').text(errors.rate ? errors.rate[0] : '');
                            $('#frequencyError').text(errors.frequency ? errors.frequency[0] : '');
                            $('#descriptionError').text(errors.description ? errors.description[0] : '');
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

            $('#editForm').submit(function (e) {
                e.preventDefault();

                // Show the spinner and disable the button
                const submitButton = $('#editInhouseSubmitButton');
                const buttonSpinner = $('#editInhousebuttonSpinner');
                const buttonText = $('#editInhousebuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    url: '{{ route("updateAdditionalService") }}',
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
                            $('#editServiceNameError').text(errors.title ? errors.title[0] : '');
                            $('#editDescriptionError').text(errors.description ? errors.description[0] : '');
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

            // Handle button click to open the modal
            $('#myTable').on('click', '.add-sub-service', function () {
                var serviceId = $(this).data('service-id');

                // You can update the form action, fields, etc. based on your needs
                $('#addSubServiceForm').attr('action', '/sub-services/add/' + serviceId);

                // Show the modal
                $('#addSubServiceModal').modal('show');
            });

            // Handle form submission
            $('#addSubServiceForm').submit(function (e) {
                e.preventDefault();
                // Your AJAX submission logic goes here
                // For example:
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize()+ "&_token={{ csrf_token() }}",
                    success: function (response) {
                        // Handle success
                        $('#addSubServiceModal').modal('hide');
                        $('#myTable').DataTable().ajax.reload();
                    },
                    error: function (xhr, status, error) {
                        // Handle errors
                    }
                });
            });
        });

        function deleteAdditionalService(id) {
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
                    url: '{{ url('additional-service-destroy') }}/' + id,
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
            });
        }

        function editAdditionalService(serviceId, title, description, rate, currency, frequency) {
            // Populate modal fields with data
            $('#editServiceId').val(serviceId);
            $('#editServiceName').val(title);
            $('#editRate').val(rate);
            // $('#editFrequency').val(frequency);
            $('#editCurrency').val(currency);
            $('#editDescription').val(description);

            // Set the Frequency field based on response.frequency
            var frequencyValue = frequency;  // Get frequency from response

            // Check if the frequency is one of the valid options
            if (['monthly', 'quarterly', 'annually'].includes(frequencyValue)) {
                $('#editModal #editFrequency').val(frequencyValue).trigger('change.select2');  // This updates the dropdown
            } else {
                $('#editModal #editFrequency').val('').trigger('change.select2');  // If frequency is not valid, clear the selection
            }

            // Show the modal
            $('#editModal').modal('show');
        }

        // Handle button click
        $('body').on('click', '.btn-edit', function () {
            var categoryId = $(this).data('id');
            var title = $(this).data('name');

            // Call the editCategory function
            editCategory(categoryId, title);
        });

        function changeAdditionalServiceStatus(id) {
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
                    url: '{{ url('additional-services') }}/' + id + '/change-status',
                    type: 'PUT',
                    data: {
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
                        buttonText.text('Change Status');
                        submitButton.prop('disabled', false);
                    }

                });
            });
        }
    </script>

@endsection
