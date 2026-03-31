@extends('layouts.app')

<!-- Select2 CSS CDN -->
<title>{{ getSetting('app_name', env('APP_NAME')) }} | Inclusive Services</title>

@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Inclusive Services</h1>
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
                            {{--                            <h3 class="card-title">DataTable with minimal features & hover style</h3>--}}
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#inclusivesModal">
                               <i class="fa fa-plus"></i> Add Inclusive Services
                            </button>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="myTable" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Sr.</th>
                                    <th>Inhouse Service Name</th>
                                    <th>Skip To Descriptives</th>
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

    <div class="modal fade" id="inclusivesModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Inclusive Services</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addInclusiveForm">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="inhouse_service"  class="col-form-label">Inhouse Service <span class="required">*</span></label>
                                <div class="input-group">
                                    <select class="select2" required name="inhouse_service" id="inhouse_service" required data-placeholder="Select Inhouse Service" 
                                    style="width: 100%;">
                                        <option value="" disabled selected></option>
                                        @foreach($inhouseServices as $service)
                                            <option value="{{ $service->id }}">{{ $service->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="inhouse_serviceError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="skipToDescriptives"  class="col-form-label">Skip To Descriptives<span class="required">*</span></label>
                                <div class="input-group select2-blue">
                                    <select class="select2" name="skipToDescriptives[]" id="skipToDescriptives" required multiple data-placeholder="Select Descriptives" data-dropdown-css-class="select2-blue" 
                                    style="width: 100%;">
                                        @foreach($descriptives as $descriptive)
                                            <option value="{{ $descriptive->description_name }}">{{ $descriptive->description_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="skipToDescriptivesError" class="text-danger"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="addInclusiveSubmitButton" class="btn btn-primary">
                        <span id="addInclusivebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="addInclusivebuttonText">Submit</span>
                    </button>
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
                    Are you sure you want to change the status of this inclusive service?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="confirmStatusChange" class="btn btn-danger">
                        <span id="confirmStatusChangebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="confirmStatusChangebuttonText">Change Status</span>
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
                    Are you sure you want to delete this inclusive service?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDelete" class="btn btn-danger">
                        <span id="buttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="buttonText">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Descriptive</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Edit form -->
                    <form id="editDescriptiveForm">
                        <input type="hidden" name="id" id="editInclusiveId">

                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="edit_inhouse_services_id"  class="col-form-label">Inhouse Service <span class="required">*</span></label>
                                <div class="input-group">
                                    <select class="select2" required name="edit_inhouse_services_id" id="edit_inhouse_services_id" required data-placeholder="Select Inhouse Service" 
                                    style="width: 100%;">
                                        <option value="" disabled selected></option>
                                        @foreach($inhouseServices as $service)
                                            <option value="{{ $service->id }}">{{ $service->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="edit_inhouse_services_idError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="edit_skipToDescriptives"  class="col-form-label">Skip To Descriptives<span class="required">*</span></label>
                                <div class="input-group select2-blue">
                                    <select class="select2" name="edit_skipToDescriptives[]" id="edit_skipToDescriptives" required multiple data-placeholder="Select Descriptives" data-dropdown-css-class="select2-blue" 
                                    style="width: 100%;">
                                        @foreach($descriptives as $descriptive)
                                            <option value="{{ $descriptive->description_name }}">{{ $descriptive->description_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="edit_skipToDescriptivesError" class="text-danger"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="editDescriptiveSubmitButton" class="btn btn-primary">
                        <span id="editDescriptivebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="editDescriptivebuttonText">Submit</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
@endsection
@section('scripts')
    <script type="text/javascript">
     $('.select2').select2();
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
                ajax: "{{ route('getInclusives') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'inhouse_service', name: 'inhouse_service'},
                    {data: 'skip_to', name: 'skip_to'},
                    {data: 'status', name: 'status'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action'},
                ]
            });
        });

        $(document).ready(function () {
            //start add new descriptive
                $("#addInclusiveSubmitButton").click(function () {
                    $("#addInclusiveForm").submit();
                });

                $("#addInclusiveForm").submit(function (e) {
                    e.preventDefault(); // Prevent default form submission

                    var formData = {
                        inhouse_services_id: $('#inhouse_service').val(),
                        skipToDescriptives: $('#skipToDescriptives').val()
                    };

                    // Show the spinner and disable the button
                    const submitButton = $('#addInclusiveSubmitButton');
                    const buttonSpinner = $('#addInclusivebuttonSpinner');
                    const buttonText = $('#addInclusivebuttonText');
                    submitButton.prop('disabled', true);
                    buttonSpinner.removeClass('d-none');
                    buttonText.text('Processing...');

                    $.ajax({
                        url: "{{ route('inclusives.store') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: formData,
                        success: function (response) {
                            toastr.success(response.message); // Success notification
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function (xhr, status, error) {
                            try {
                                var errors = JSON.parse(xhr.responseText).errors;
                                $('#inhouse_serviceError').text(errors.inhouse_services_id ? errors.inhouse_services_id[0] : '');
                                $('#skipToDescriptivesError').text(errors.skipToDescriptives ? errors.skipToDescriptives[0] : '');
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

            //end add new descriptive

            //start edit to descriptive
                $(document).on('click', '#editBtn', function () {
                    var inclusiveId = $(this).data('id');

                    // Make AJAX request to get category details
                    $.ajax({
                        type: 'GET',
                        data: { id: inclusiveId },
                        url: '{{ route("editInclusive") }}', 
                        success: function (response) {
                            var inclusiveData = response.data;

                            // Set the value for the inhouse services dropdown
                            $('#editModal #editInclusiveId').val(inclusiveId);
                            $('#editModal #edit_inhouse_services_id').val(inclusiveData.inhouse_service_id).trigger('change');

                            // Set the values for the multiple-select field
                            let skipToArray = inclusiveData.skip_to.split(','); // Assuming `skip_to` is a comma-separated string
                            $('#editModal #edit_skipToDescriptives').val(skipToArray).trigger('change');

                        
                            // Show the modal
                            $('#editModal').modal('show');
                        },
                        error: function (error) {
                            console.log('Error fetching descriptive details: ', error);
                        }
                    });
                });

                $("#editDescriptiveSubmitButton").click(function () {
                    $("#editDescriptiveForm").submit();
                });

                $('#editDescriptiveForm').on('submit', function (e) {
                    e.preventDefault();

                    // Show the spinner and disable the button
                    const submitButton = $('#editDescriptiveSubmitButton');
                    const buttonSpinner = $('#editDescriptivebuttonSpinner');
                    const buttonText = $('#editDescriptivebuttonText');
                    submitButton.prop('disabled', true);
                    buttonSpinner.removeClass('d-none');
                    buttonText.text('Processing...');

                    $.ajax({
                        url: '{{ route("updateInclusive") }}',
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
                                $('#editDescriptiveNameError').text(errors.editDescriptiveName ? errors.editDescriptiveName[0] : '');
                                $('#editReplaceWithError').text(errors.editReplaceWith ? errors.editReplaceWith[0] : '');
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
            //end edit to descriptive
        });

        function changeStatus(id) {
            $('#statusConfirmationModalCompany').modal('show');

            $('#confirmStatusChange').on('click', function () {
                // Show the spinner and disable the button
                const submitButton = $('#confirmStatusChange');
                const buttonSpinner = $('#confirmStatusChangebuttonSpinner');
                const buttonText = $('#confirmStatusChangebuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    url: '{{ url("inclusivesChangeStatus") }}',
                    type: 'PUT',
                    data: {
                        id : id,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        if(response.success){
                            toastr.success(response.message);
                        }else{
                            toastr.error(response.message);
                        }
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
                        buttonText.text('Change Status');
                        submitButton.prop('disabled', false);
                    }
                });

                $('#statusConfirmationModalCompany').modal('hide');
            });
        }

        function deleteCompany(id) {
            // Show confirmation modal
            $('#deleteConfirmationModal').modal('show');

            $('#confirmDelete').on('click', function () {
                // Show the spinner and disable the button
                const submitButton = $('#confirmDelete');
                const buttonSpinner = $('#confirmDeletebuttonSpinner');
                const buttonText = $('#confirmDeletebuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    url: '{{ route("inclusiveDestroy") }}',
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
