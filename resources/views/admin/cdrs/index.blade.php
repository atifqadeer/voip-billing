@extends('layouts.app')

<title>{{ getSetting('app_name', env('APP_NAME')) }} | CDRs Management</title>

@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>CDRs Management</h1>
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
                            <div class="row align-items-center d-flex">
                                <div class="col-md-3 d-flex align-items-center ">
                                    <div class="col-md-5 px-1">
                                        <input type="text" id="month" class="form-control" placeholder="Select Month">
                                    </div>
                                    <div class="col-md-2 px-1">
                                        <button id="filter" class="btn btn-primary py-2">
                                            <i class="fa-solid fa-filter"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select id="providerFilter" class="form-control">
                                        <option value="">All Providers</option>
                                        @foreach($cdrProviders as $provider)
                                            <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="statusFilter" class="form-control">
                                        <option value="all">All</option>
                                        <option value="assigned">Assigned</option>
                                        <option value="un_assigned">Un-Assigned</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#importModal">
                                    <i class="fa fa-plus"></i>   Import CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="cdrTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Sr.</th>
                                        <th>Date</th>
                                        <th>Provider</th>
                                        <th>Reference No.</th>
                                        <th>Trunk</th>
                                        <th>From (Cli)</th>
                                        <th>To (Cli)</th>
                                        <th>Descriptive</th>
                                        <th>Cal. Duration</th>
                                        <th>Currency</th>
                                        <th>Status</th>
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
    <!-- Import CSV Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import CDR CSV</h5>
                    <button type="button" class="close" data-dismiss="modal" id="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data" id="importCSV">
                        @csrf
                        <div class="form-group">
                            <select name="provider_id" id="cdr_provider" class="form-control" required>
                                <option value="">Select CDR Provider</option>
                                @foreach($cdrProviders as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option> 
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <div id="drop_zone" class="drop-zone">
                                <i class="fa-solid fa-file"></i>
                                <p><b>csv/xlsx file</b></p><br>
                                <p>Drag & drop a file here or click to select a file</p>
                                <input type="file" name="csv_file" id="csv_file" accept=".csv,.xlsx" class="form-control">
                            </div>
                            <span id="file_name" class="file-name"></span>
                            <span id="fileError" class="text-danger"></span>
                            <span id="uploadSuccess" class="text-success"></span>
                        </div>
                    
                        <!-- Progress bar container -->
                        <div id="progressContainer" style="display:none; margin-top:10px;">
                            <div id="progressBar" style="width: 0%; height: 20px;" class="bg-primary"></div>
                            <div id="progressPercentage" style="text-align:center;">0%</div>
                        </div>             
                    
                        <div class="form-group float-right">
                            <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal" aria-label="Close">Cancel
                            </button>
                            <button type="submit" id="submitBtn" class="btn btn-primary">
                                <span id="submitbuttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span id="submitbuttonText">Upload</span>
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
            // Initialize the DataTable
            var table = $('#cdrTable').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: {
                    url: "{{ route('getCdrs') }}",  // Adjust to your route
                    data: function (d) {
                        d.month = $('#month').val();  // Pass the selected month to the server
                        d.provider = $('#providerFilter').val(); // Pass the selected provider to the server
                        d.status = $('#statusFilter').val(); // Pass the selected provider to the server
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'date', name: 'date', searchable: true },
                    { data: 'provider_name', name: 'cdr_providers.name', searchable: true },
                    { data: 'reference', name: 'reference', searchable: true },
                    { data: 'trunk', name: 'trunk', searchable: true },
                    { data: 'from_cli', name: 'from_cli', searchable: true },
                    { data: 'to_number', name: 'to_number', searchable: true },
                    { data: 'simplified_to_descriptive', name: 'simplified_to_descriptive', searchable: true },
                    { data: 'calculated_duration', name: 'calculated_duration' },
                    { data: 'currency', name: 'currency' },
                    { data: 'status', name: 'status', searchable: true, orderable: false},
                ]
            });

            // Initialize the datepicker with the format 'yyyy-mm' (month view)
            $('#month').datepicker({
                format: "yyyy-mm",      // Set the format as 'yyyy-mm'
                startView: "months",    // Start the view at the month level
                minViewMode: "months",  // Ensure user only selects months (no days)
                autoclose: true         // Automatically close the datepicker when a date is selected
            });

            // Set default filter to the previous month
            var currentDate = new Date();
            currentDate.setMonth(currentDate.getMonth() - 1); // Subtract 1 month to get the previous month

            // Format the previous month as 'yyyy-mm'
            var previousMonth = currentDate.getFullYear() + '-' + ('0' + (currentDate.getMonth() + 1)).slice(-2);

            // Set the datepicker's default date to the previous month
            $('#month').datepicker('setDate', previousMonth);

            // Trigger the table to be drawn initially with the previous month's data
            table.draw();

            // When the filter button is clicked, redraw the DataTable with the selected month
            $('#filter').click(function () {
                table.draw();
            });

            // When the provider filter changes, redraw the DataTable with the selected provider
            $('#providerFilter').on('change', function () {
                table.draw();
            });
           
            // When the provider filter changes, redraw the DataTable with the selected provider
            $('#statusFilter').on('change', function () {
                table.draw();
            });
        });

        $(document).ready(function () {
            var dropZone = document.getElementById('drop_zone');
            var fileInput = document.getElementById('csv_file');
            var fileNameElement = document.getElementById('file_name');
            var fileError = document.getElementById('fileError');
            var progressContainer = $('#progressContainer');
            var progressBar = $('#progressBar');
            var progressPercentage = $('#progressPercentage');
            var progressPolling;

            // Handle drag events
            dropZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                dropZone.classList.add('dragover');
            });

            dropZone.addEventListener('dragleave', function () {
                dropZone.classList.remove('dragover');
            });

            dropZone.addEventListener('drop', function (e) {
                e.preventDefault();
                dropZone.classList.remove('dragover');

                if (e.dataTransfer.files.length) {
                    var file = e.dataTransfer.files[0];
                    fileInput.files = e.dataTransfer.files;
                    displayFileInfo(file);
                }
            });

            dropZone.addEventListener('click', function () {
                fileInput.click();
            });

            fileInput.addEventListener('change', function () {
                if (fileInput.files.length) {
                    var file = fileInput.files[0];
                    displayFileInfo(file);
                }
            });

            function displayFileInfo(file) {
                fileNameElement.textContent = file.name;
            }

            $('#importCSV').submit(function (event) {
                event.preventDefault();
                
                if (!fileInput.files.length) {
                    fileError.textContent = 'File is required';
                    return;
                }

                var formData = new FormData(this);
                progressContainer.show(); // Show the progress container

                // Show the spinner and disable the button
                const submitButton = $('#submitBtn');
                const buttonSpinner = $('#submitbuttonSpinner');
                const buttonText = $('#submitbuttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Saving...');

                $.ajax({
                    url: '{{ route("importCDR") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function (e) {
                            if (e.lengthComputable) {
                                var percentComplete = (e.loaded / e.total) * 100;
                                progressBar.css('width', percentComplete + '%');
                                progressPercentage.text(Math.round(percentComplete) + '%');
                                
                                // Hide the progress container once the upload is 100%
                                if (percentComplete === 100) {
                                    progressContainer.hide();  // Hide the progress bar container
                                    buttonSpinner.removeClass('d-none'); // Show the button spinner once the upload is complete
                                    buttonText.text('Saving...');
                                }
                            }
                        }, false);
                        return xhr;
                    },
                    success: function (response) {
                        // $('#uploadSuccess').text(response.message);
                        toastr.success(response.message);
                        $('#importModal').modal('hide');
                    },
                    error: function (xhr) {
                        toastr.error(xhr);
                    },
                    complete: function () {
                        // Hide the spinner and re-enable the button once the process is complete
                        buttonSpinner.addClass('d-none');
                        buttonText.text('Saved');
                        submitButton.prop('disabled', false);
                    }
                });
            });

        });

    </script>
@endsection
