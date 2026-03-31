@extends('layouts.app')

<!-- Select2 CSS CDN -->
<title>{{ getSetting('app_name', env('APP_NAME')) }} | Descriptives</title>

@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Descriptives</h1>
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
                            <button type="button" style="background: linear-gradient(to right, #007bff, #0056b3); float: right" class="btn btn-primary" data-toggle="modal" data-target="#descriptiveModal">
                               <i class="fa fa-plus"></i> Add Descriptive
                            </button>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="myTable" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Sr.</th>
                                    <th>Description Name</th>
                                    <th>Replace With</th>
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

    <div class="modal fade" id="descriptiveModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Descriptive</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addDescriptiveForm">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="DescriptiveName"  class="col-form-label">Descriptive Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="DescriptiveName" placeholder="Enter Descriptive Name">
                                <span id="DescriptiveNameError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="replaceWith"  class="col-form-label">Replace With <span class="required">*</span></label>
                                <textarea class="form-control" rows="5" id="replaceWith" placeholder="Enter name which you want to replace with this name"></textarea>
                                <span id="replaceWithError" class="text-danger"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="addDescriptiveSubmitButton" class="btn btn-primary">
                        <span id="addDescriptivebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="addDescriptivebuttonText">Submit</span>
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
                    Are you sure you want to change the status of this descriptive?
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
                    Are you sure you want to delete this descriptive?
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
                        <input type="hidden" name="id" id="editDescriptiveId">

                        <div class="form-group">
                            <label for="editDescriptiveName"  class="col-form-label">Descriptive Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="editDescriptiveName" name="editDescriptiveName" placeholder="Enter descriptive name">
                            <span id="editDescriptiveNameError" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="editReplaceWith" class="col-form-label">Replace With </label>
                            <textarea class="form-control" rows="5" id="editReplaceWith" name="editReplaceWith" placeholder="Enter name which you want to replace with this name"></textarea>
                            <span id="editReplaceWithError" class="text-danger"></span>
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
                ajax: "{{ route('getDescriptives') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'description_name', name: 'description_name'},
                    {data: 'replace_with', name: 'replace_with'},
                    {data: 'status', name: 'status'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action'},
                ]
            });
        });

        $(document).ready(function () {
            //start add new descriptive
                $("#addDescriptiveSubmitButton").click(function () {
                    $("#addDescriptiveForm").submit();
                });

                $("#addDescriptiveForm").submit(function (e) {
                    var formData = {
                        DescriptiveName: $('#DescriptiveName').val(),
                        replaceWith: $('#replaceWith').val()
                    };

                    // Show the spinner and disable the button
                    const submitButton = $('#addDescriptiveSubmitButton');
                    const buttonSpinner = $('#addDescriptivebuttonSpinner');
                    const buttonText = $('#addDescriptivebuttonText');
                    submitButton.prop('disabled', true);
                    buttonSpinner.removeClass('d-none');
                    buttonText.text('Processing...');

                    $.ajax({
                        url: "{{ route('descriptives.store') }}", 
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
                                $('#DescriptiveNameError').text(errors.CompanyName ? errors.CompanyName[0] : '');
                                $('#replaceWithError').text(errors.CompanyEmail ? errors.CompanyEmail[0] : '');
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
                    var descriptiveId = $(this).data('id');

                    // Make AJAX request to get category details
                    $.ajax({
                        type: 'GET',
                        data: { id: descriptiveId },
                        url: '{{ route("editDescriptive") }}', 
                        success: function (response) {
                            var descriptive = response.data;
                            $('#editModal #editDescriptiveId').val(descriptive.id);
                            $('#editModal #editDescriptiveName').val(descriptive.description_name);
                            $('#editModal #editReplaceWith').val(descriptive.replace_with);
                        
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
                        url: '{{ route("updateDescriptive") }}',
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
                    url: '{{ url("descriptiveChangeStatus") }}',
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
                    url: '{{ route("descriptiveDestroy") }}',
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
