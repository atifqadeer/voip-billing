@extends('layouts.app')
<title>{{ getSetting('app_name', env('APP_NAME')) }} | Settings</title>

@section('content')
<style>
    #table_div{
        height: 60vh !important;
        overflow: scroll;
    }
</style>
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Settings</h1>
                    </div>
                </div>
            </div>
        </section>

        <!-- Settings Form -->
        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Add New</h3>
                            </div>
                            <div class="card-body">
                                <!-- Form to add new setting -->
                                <form id="settings-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-6 form-group">
                                            <label for="param">Parameter</label>
                                            <input type="text" class="form-control" id="param" name="param" placeholder="Enter Parameter" required>
                                        </div>
                                        <div class="col-6 form-group">
                                            <label for="value">Value</label>
                                            <input type="text" class="form-control" id="value" name="value" placeholder="Enter Value" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary float-right">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saved Settings Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Saved Settings</h3>
                            </div>
                            <div class="card-body" id="table_div">
                                <table class="table table-bordered" id="settings-table">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Settings will be populated here dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>


@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        fetchSettings();  // Fetch settings data on page load

        // Handle form submission for new settings
        $('#settings-form').on('submit', function(e) {
            e.preventDefault();
            var param = $('#param').val();
            var value = $('#value').val();

            createSetting(param, value);
        });
    });

    // Fetch the settings from the server
    function fetchSettings() {
        $.ajax({
            url: '{{ route('settings.getSettings') }}',  // Use your route to fetch settings
            method: 'GET',
            success: function(response) {
                var settingsTable = $('#settings-table tbody');
                settingsTable.empty();  // Clear the table before adding new data

                // Loop through settings and append to the table
                response.data.forEach(function(setting) {
                    var value = setting.value == null ? '' : setting.value;
                    settingsTable.append(`
                        <tr data-id="${setting.id}">
                            <td><input type="text" class="form-control" value="${setting.param}" disabled></td>
                            <td><input type="text" class="form-control setting-value" value="${value}"></td>
                            <td>
                                <button class="btn btn-success btn-sm save-btn">
                                    Update 
                                    <span class="spinner-border spinner-border-sm text-light d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                // Attach event listener for save buttons
                $('.save-btn').click(function() {
                    var row = $(this).closest('tr');
                    var settingId = row.data('id');
                    var updatedValue = row.find('.setting-value').val();
                    var saveButton = $(this);  // Save button element
                    var spinner = saveButton.find('.spinner-border');  // Spinner inside the button

                    // Show spinner and disable the button
                    saveButton.prop('disabled', true);
                    spinner.removeClass('d-none');

                    updateSetting(settingId, updatedValue, saveButton, spinner);
                });
            },
            error: function(xhr, status, error) {
                alert('Error fetching settings: ' + error);
            }
        });
    }

    // Function to send the updated value to the server
    function updateSetting(id, updatedValue, saveButton, spinner) {
        $.ajax({
            url: '{{ route('settingsUpdate', ':id') }}'.replace(':id', id),  // Use your route to update settings
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                value: updatedValue
            },
            success: function(response) {
                toastr.success('Settings updated successfully');
                fetchSettings();  // Refresh the settings list after adding

                // Hide spinner and enable button
                spinner.addClass('d-none');
                saveButton.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                toastr.error('Error updating setting: ' + error);

                // Hide spinner and enable button
                spinner.addClass('d-none');
                saveButton.prop('disabled', false);
            }
        });
    }

    // Function to create a new setting
    function createSetting(param, value) {
        $.ajax({
            url: '{{ route('settings.store') }}',  // Route to store the new setting
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                param: param,
                value: value
            },
            success: function(response) {
                toastr.success('New setting added successfully!');
                fetchSettings();  // Refresh the settings list after adding
                $('#settings-form')[0].reset();  // Reset the form
            },
            error: function(xhr, status, error) {
                toastr.error('Error adding new setting: ' + error);
            }
        });
    }
</script>

@endsection