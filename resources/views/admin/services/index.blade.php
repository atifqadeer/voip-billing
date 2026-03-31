@extends('layouts.app')
<title>{{ getSetting('app_name', env('APP_NAME')) }} | Services Management</title>

@section('content')
    {{--    <div class="container">--}}


    <!-- Content Header (Page header) -->
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Services Management</h1>
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
                                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addServiceModal">
                                   <i class="fa fa-plus"></i> Add Service
                                </button>
                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="myTable" class="table  table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            {{-- <th>Sub Service</th>--}}
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
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

    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('services.store')}}" method="post" id="addServiceForm">
                        @csrf
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Enter Service Name">
                            <span id="titleError" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Enter Description"></textarea>
                            <span id="descriptionError" class="text-danger"></span>
                        </div>
                        <div class="form-group float-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" id="addServiceSubmitButton" class="btn btn-primary">
                                <span id="addServicebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span id="addServicebuttonText">Submit</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding Sub-Services -->
    {{-- <div class="modal fade" id="addSubServiceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Sub-Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Your form for adding Sub-Services goes here -->
                    <!-- For example: -->
                    <form id="addSubServiceForm">
                        <!-- Form fields go here -->
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Title:</label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="ENTER TITLE sub category..">
                            <span id="titleError" class="text-danger"></span>
                        </div>
                        <button type="submit"  style="float: right" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

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
                    Are you sure you want to delete this service?
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
                    <button type="button" id="confirmStatusChange" class="btn btn-primary">
                        <span id="confirmStatusbuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="confirmStatusbuttonText">Change Status</span>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" name="id" id="editServiceId">
                        <div class="form-group">
                            <label for="editServiceName">Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="editServiceName" name="editServiceName" required>
                            <span id="editServiceNameError" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="editDescription" class="col-form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="editDescription"></textarea>
                            <span id="editDescriptionError" class="text-danger"></span>
                        </div>
                        <div class="form-group float-right">
                            <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Cancel</button>
                            <button type="submit" id="editServiceSubmitButton" class="btn btn-primary">
                                <span id="editServicebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span id="editServicebuttonText">Submit</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script type="text/javascript">
        $(function () {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('getServices') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'description', name: 'description'},
                    // {data: 'sub_service', name: 'sub_service'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action'},
                ]
            });
        });

        $(document).ready(function () {
            $(document).on('submit', '#addServiceForm', function (e) {
                e.preventDefault();

                // Show the spinner and disable the button
                const submitButton = $('#addServiceSubmitButton');
                const buttonSpinner = $('#addServicebuttonSpinner');
                const buttonText = $('#addServicebuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: $(this).serialize(),
                    success: function (response) {
                        toastr.success(response.message);
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function (xhr, status, error) {
                        try {
                            const errors = JSON.parse(xhr.responseText).errors;
                            $('#titleError').text(errors.title ? errors.title[0] : '');
                            $('#descriptionError').text(errors.description ? errors.description[0] : '');
                        } catch (e) {
                            alert('Something went wrong. Please try again.');
                        }
                    },
                    complete: function () {
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Submit');
                        submitButton.prop('disabled', false);
                    }
                });
            });

            $(document).on('submit', '#editForm', function (e) {
                e.preventDefault();

                // Show the spinner and disable the button
                const submitButton = $('#editServiceSubmitButton');
                const buttonSpinner = $('#editServicebuttonSpinner');
                const buttonText = $('#editServicebuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                $.ajax({
                    url: '{{ route("updateService") }}',
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $(this).serialize(),
                    success: function (response) {
                        toastr.success(response.success);
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
                            alert('Something went wrong. Please try again.');
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
            // $('#myTable').on('click', '.add-sub-service', function () {
            //     var serviceId = $(this).data('service-id');

            //     // You can update the form action, fields, etc. based on your needs
            //     $('#addSubServiceForm').attr('action', '/sub-services/add/' + serviceId);

            //     // Show the modal
            //     $('#addSubServiceModal').modal('show');
            // });

            // Handle form submission
            // $('#addSubServiceForm').submit(function (e)
            //  {
            //     e.preventDefault();
            //     // Your AJAX submission logic goes here
            //     // For example:
            //     $.ajax({
            //         type: 'POST',
            //         url: $(this).attr('action'),
            //         data: $(this).serialize()+ "&_token={{ csrf_token() }}",
            //         success: function (response) {
            //             // Handle success
            //             $('#addSubServiceModal').modal('hide');
            //             $('#myTable').DataTable().ajax.reload();
            //         },
            //         error: function (xhr, status, error) {
            //             // Handle errors
            //         }
            //     });
            // });
        });

        function deleteService(id) {
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
                    url: '{{ url('serviceDestroy') }}/' + id,
                    data: {id : id},
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        console.log(response);
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

        // Define the editCategory function
        function editService(serviceId, title,description) {
            // Populate modal fields with data
            $('#editServiceId').val(serviceId);
            $('#editServiceName').val(title);
            $('#editDescription').val(description);

            // Show the modal
            $('#editModal').modal('show');
        }

        // Handle button click
        // $('body').on('click', '.btn-edit', function () {
        //     var categoryId = $(this).data('id');
        //     var title = $(this).data('name');

        //     // Call the editCategory function
        //     editCategory(categoryId, title);
        // });

        // Handle form submission


        function changeStatus(id) {
            // Show confirmation modal
            $('#statusConfirmationModal').modal('show');

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
                    url: '{{ url('services') }}/' + id + '/change-status',
                    type: 'PUT', // Use 'PUT' or 'PATCH' based on your Laravel setup
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
                $('#statusConfirmationModal').modal('hide');
            });
        }
    </script>

@endsection
