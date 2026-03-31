@extends('layouts.app')
<title>{{ getSetting('app_name', env('APP_NAME')) }} | Bills Management</title>
@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Bills Management</h1>
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
                            <button class="btn btn-primary float-right" data-toggle="modal" data-target="#GenerateBillModal">
                                <i class="fa fa-plus"></i> Generate Bill
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Filter Section -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="date-range">Select Month for Bill</label>
                                    <input type="month" name="month_year" id="month_year" class="form-control" value="" placeholder="Select a month" />
                                </div>
                                <div class="col-md-2">
                                    <label for="payment-status">Payment Status</label>
                                    <select id="payment-status" class="form-control">
                                        <option value="all" selected>All</option>
                                        <option value="paid">Paid</option>
                                        <option value="unpaid">Unpaid</option>
                                    </select>
                                </div>
                                <div class="col-md-6"></div>
                                <div class="col-md-2 d-flex align-items-end justify-content-end mb-1">
                                    <!-- Bulk Action Button aligned at the bottom -->
                                    <button id="bulk-action-btn" class="btn btn-danger"><i class="fas fa-trash"></i> Bulk Delete</button>
                                </div>
                            </div>


                            <!-- Billing Table -->
                            <table id="billingTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>Bill ID</th>
                                        <th>Client Name</th>
                                        <th>Billing Month</th>
                                        <th>Amount</th>
                                        <th>Payment Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be populated here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
   
    <!-- Import CSV Modal -->
    <div class="modal fade" id="GenerateBillModal" tabindex="-1" role="dialog" aria-labelledby="GenerateBillModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Generate Bill</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="generateBillForm">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label for="landline">Client <span class="required">*</span></label>
                                <div class="select2-blue">
                                    <select class="select2" multiple="multiple" name="client[]" id="client" data-placeholder="Select Clients" data-dropdown-css-class="select2-blue" style="width: 100%;">
                                        <option value="all">Select All</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ ucwords($client->client_name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="servicesError" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="monthYear">Choose the CDR for the month <span class="required">*</span></label>
                            <input type="text" id="monthYear" name="monthYear" class="form-control" placeholder="Select Month">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="generateBillBtn" class="btn btn-primary">
                        <span id="generateBillbuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="generateBillbuttonText">Generate</span>
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
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                Are you sure you want to mark the payment status as paid?
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" id="confirmToggle" class="btn btn-danger">
                <span id="confirmTogglebuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <span id="confirmTogglebuttonText">Confirm</span>
            </button>
            </div>
        </div>
        </div>
    </div>
  
@endsection
@section('scripts')

    <script type="text/javascript">
        $('#client').select2();

        $('#monthYear').datepicker({
            format: "yyyy-mm",
            startView: "months",
            minViewMode: "months",
            autoclose: true
        });
            
        // Set default filter to current month
        var currentMonth = new Date();
        currentMonth.setMonth(currentMonth.getMonth() - 1); // Subtract one month
        var formattedMonth = currentMonth.getFullYear() + '-' + ('0' + (currentMonth.getMonth() + 1)).slice(-2);

        // Set the date in the datepicker
        $('#month_year').val(formattedMonth);


        $(function () {
            // Set up the DataTable
            var table = $('#billingTable').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                paging: true,  // Pagination enabled
                pageLength: 10,  // Default number of rows per page
                ajax: {
                    url: "{{ route('getBillingList') }}",  // URL for the DataTables server-side processing
                    data: function(d) {
                       // Get the selected date from the datepicker
                        var month_year = $('#month_year').val();  // Get selected date from the datepicker
                        // Get the payment status filter value
                        var paymentStatus = $('#payment-status').val();

                        // Add filters to the DataTables request
                        d.date = month_year;  // Add selected date (or current date)
                        d.paymentStatus = paymentStatus;  // Add paymentStatus filter
                    },
                    dataSrc: function (response) {
                        // Return the correct data structure for DataTables
                        return response.data;  // This is where the paginated data is sent back
                    }
                },
                columns: [
                    {
                        data: 'checkbox',  // The checkbox column
                        name: 'checkbox',
                        orderable: false,  // Don't allow sorting for checkboxes
                        searchable: false,  // Don't allow searching for checkboxes
                        render: function (data, type, row) {
                            // Add the checkbox HTML
                            return '<input type="checkbox" class="billing-checkbox" data-id="' + row.id + '">';
                        }
                    },
                    {data: 'uuid', name: 'uuid'},
                    {data: 'client_name', name: 'client_name'},
                    {data: 'billing_month', name: 'billing_month'},
                    {data: 'total_payment', name: 'total_payment'},
                    {data: 'payment_status', name: 'payment_status'},
                    {data: 'generated_at', name: 'generated_at'},
                    {data: 'action', name: 'action'},
                ]
            });

           // Datepicker Event: When a date is selected
            $('#month_year').on('change', function() {
                table.ajax.reload();  // Reload table data based on the selected date
            });

            // Payment Status Filter Change Event
            $('#payment-status').on('change', function() {
                table.ajax.reload();  // Reload table data based on the selected payment status
            });
            
             // Handle "Select All" checkbox click event
            $('#select-all').on('click', function () {
                var isChecked = this.checked;  // Get the state of the "Select All" checkbox
                
                // Select or deselect checkboxes for all rows in the table (not just the current page)
                table.$('input.billing-checkbox').prop('checked', isChecked);
            
                // Enable/Disable the "Bulk Delete" button based on checkbox selection
                if (isChecked) {
                    $('#bulk-action-btn').prop('disabled', false);  // Enable the button
                } else {
                    $('#bulk-action-btn').prop('disabled', true);  // Disable the button
                }
            });
            
             // Handle individual row checkbox click event
            $('#billingTable').on('change', '.billing-checkbox', function () {
                var totalCheckboxes = $('.billing-checkbox').length;
                var checkedCheckboxes = $('.billing-checkbox:checked').length;
            
                // Enable/Disable the "Bulk Delete" button based on checkbox selection
                if (checkedCheckboxes > 0) {
                    $('#bulk-action-btn').prop('disabled', false);  // Enable the button
                } else {
                    $('#bulk-action-btn').prop('disabled', true);  // Disable the button
                }
            
                // If all checkboxes are selected, check the "Select All" checkbox
                if (totalCheckboxes === checkedCheckboxes) {
                    $('#select-all').prop('checked', true);
                } else {
                    $('#select-all').prop('checked', false);
                }
            });
            
            // Bulk Action Button
            $('#bulk-action-btn').on('click', function () {
                var selectedIds = [];
                
                // Collect the IDs of selected checkboxes
                $('.billing-checkbox:checked').each(function () {
                    selectedIds.push($(this).data('id'));  // Collect the IDs of selected rows
                });
            
                // Check if any checkboxes are selected
                if (selectedIds.length > 0) {
                    // Confirm with the user before performing the bulk delete
                    if (confirm('Are you sure you want to delete these bills?')) {
                        // Show a loading spinner or message to indicate the deletion process is happening
                        var submitButton = $(this);
                        submitButton.prop('disabled', true);  // Disable the button during the request
            
                        // Send the request to delete the selected bills via AJAX
                        $.ajax({
                            url: "{{ url('/billing/destroy') }}",  // Replace with your route to handle bulk deletion
                            method: 'Delete',
                            data: {
                                _token: '{{ csrf_token() }}',  // CSRF token for security
                                ids: selectedIds  // Send the selected bill IDs
                            },
                           success: function (response) {
                                if (response.success) {
                                    // Show success message using Toastr
                                    toastr.success(response.message);
                            
                                    // Reload the DataTable to reflect the changes
                                    table.ajax.reload();
                                } else {
                                    // Show error message if the delete operation failed
                                    toastr.error(response.message || 'An error occurred while deleting the bills.');
                                }
                            },
                            error: function (xhr, status, error) {
                                // Handle AJAX errors
                                var errorMessage = 'Error: ' + error;
                            
                                // If the response contains a specific message, use it
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                            
                                // Show error message in case of AJAX failure
                                toastr.error(errorMessage);
                            },
                            complete: function () {
                                // Re-enable the button after the request is completed
                                submitButton.prop('disabled', false);
                            }

                        });
                    }
                } else {
                    // If no bills are selected, show an alert
                    alert('No bills selected.');
                }
            });

        });

        $(document).ready(function() {
           // Initially disable the Bulk Delete button
            $('#bulk-action-btn').prop('disabled', true);
    
            // Get today's date in the format YYYY-MM-DD
            var today = moment().format('YYYY-MM-DD');

            // Set the input value as today's date by default
            $('#date-range').val(today);  // Display today's date in the input field

            $("#generateBillBtn").click(function () {
                $("#generateBillForm").submit();
            });

            // Attach the submit event handler to the form
            $('#generateBillForm').on('submit', function(e) {
                e.preventDefault();

                // Show the spinner and disable the button
                const submitButton = $('#generateBillBtn');
                const buttonSpinner = $('#generateBillbuttonSpinner');
                const buttonText = $('#generateBillbuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                // Serialize the form data
                let formData = $(this).serialize();

                $.ajax({
                    url: '{{ route("generate_bill") }}',
                    method: 'GET', 
                    data: formData,
                    success: function(response) {
                        toastr.success(response.message);
                         setTimeout(function() {
                              window.location.reload();
                           }, 1000);
                    },
                    error: function (xhr, status, error) {
                        try {
                            let errorMessage = 'An unexpected error occurred. Please try again.';

                            if (xhr.status === 422) {
                                errorMessage = xhr.responseJSON && xhr.responseJSON.error
                                    ? xhr.responseJSON.error
                                    : 'Validation error: Please check your inputs.';
                            } else if (xhr.status === 500) {
                                errorMessage = xhr.responseJSON && xhr.responseJSON.message
                                    ? xhr.responseJSON.message
                                    : 'Internal server error. Please try again later.';
                            } else {
                                errorMessage = xhr.responseText || error || 'Something went wrong.';
                            }

                            toastr.error(errorMessage);
                        } catch (e) {
                            toastr.error('An unexpected error occurred. Please try again.');
                        }
                    },
                    complete: function () {
                        // Hide the spinner and re-enable the button
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Generate');
                        submitButton.prop('disabled', false);
                    },
                });
            });
        });

        function deleteBill(id) {
            // Show confirmation modal
            $('#deleteConfirmationModal').modal('show');

            // Handle deletion on confirmation
            $('#confirmDelete').on('click', function () {
                var ids = Array.isArray(id) ? id : [id];

                $.ajax({
                    url: "{{ url('/billing/destroy') }}",
                    url: "{{ url('/billing/destroy') }}",
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',  // CSRF token for security
                        ids: ids  // Send the selected bill IDs
                    },
                    success: function (response) {
                        if (response.success) {
                            // Show success message using Toastr
                            toastr.success(response.message);
                            var table = $('#billingTable').DataTable();
                            setTimeout(function() {
                                table.ajax.reload();
                            }, 1000);
                            // Reload the DataTable to reflect the changes
                            
                        } else {
                            // Show error message if the delete operation failed
                            toastr.error(response.message || 'An error occurred while deleting the bills.');
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle AJAX errors
                        var errorMessage = 'Error: ' + error;
                    
                        // If the response contains a specific message, use it
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                    
                        // Show error message in case of AJAX failure
                        toastr.error(errorMessage);
                    },
                    complete: function () {
                        // Re-enable the button after the request is completed
                        submitButton.prop('disabled', false);
                    }

                });

                // Close the modal
                $('#deleteConfirmationModal').modal('hide');
            });
        }

        function togglePaymentStatus(billingId) {
            // Show the confirmation modal
            $('#confirmationModal').modal('show');

            // When the "Confirm" button is clicked
            $('#confirmToggle').off('click').on('click', function() {
                // Show the spinner and disable the button
                const submitButton = $('#confirmToggle');
                const buttonSpinner = $('#confirmTogglebuttonSpinner');
                const buttonText = $('#confirmTogglebuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                // Close the modal
                $('#confirmationModal').modal('hide');

                // Proceed with the AJAX request
                $.ajax({
                    url: '{{ route('billing.togglePaymentStatus') }}', // Your route to toggle payment status
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', 
                        id: billingId // Pass the billing ID
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);

                        } else {
                            toastr.error('Error toggling payment status');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Error: ' + error);
                    },
                    complete: function () {
                        // Hide the spinner and re-enable the button
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Confirm');
                        submitButton.prop('disabled', false);
                    }
                });
            });
        }

    </script>
    
@endsection
