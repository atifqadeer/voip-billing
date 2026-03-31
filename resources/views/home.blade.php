@extends('layouts.app')
<title>{{ getSetting('app_name', env('APP_NAME')) }} | Dashboard</title>

@section('content')
<div class="content-wrapper">
    <div class="container-fluid p-4">
        <!-- First Section: Counts -->
        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                    <div class="row d-flex justify-content-between align-items-center px-3">
                        <div>
                            <h5 class="mb-0">Today's Summary</h5>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-primary export-btn">
                                <i class="fa-solid fa-upload"></i> &nbsp;Export
                            </button>
                        </div>
                    </div><hr>
                    <div class="row d-flex justify-content-between px-3">
                        <!-- Total Clients -->
                        <div class="col-3">
                            <div class="card custom-card p-4">
                                <div class="card-icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div>
                                    <h4>{{ $clientsCount }}</h4>
                                    <p>Total Clients</p>
                                </div>
                            </div>
                        </div>
        
                        <!-- Total Vendors -->
                        <div class="col-3">
                            <div class="card custom-card p-4">
                                <div class="card-icon">
                                    <i class="fa fa-industry"></i> <!-- Vendor relevant icon -->
                                </div>
                                <div>
                                    <h4>{{ $vendorsCount }}</h4>
                                    <p>Total Vendors</p>
                                </div>
                            </div>
                        </div>
        
                        <!-- Total Users -->
                        <div class="col-3">
                            <div class="card custom-card p-4">
                                <div class="card-icon">
                                    <i class="fa fa-users-cog"></i> <!-- User relevant icon -->
                                </div>
                                <div>
                                    <h4>{{ $usersCount }}</h4>
                                    <p>Total Users</p>
                                </div>
                            </div>
                        </div>
        
                        <!-- Total Services -->
                        <div class="col-3">
                            <div class="card custom-card p-4">
                                <div class="card-icon">
                                    <i class="fa fa-layer-group"></i> <!-- Services relevant icon -->
                                </div>
                                <div>
                                    <h4>{{ $servicesCount }}</h4>
                                    <p>Total Services</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Second Section: Graphs -->
        <div class="row">
            <!-- Graph for Services Usage -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Services Usage Overview</h5>
                    </div>
                    <div class="card-body">
                        <!-- Placeholder for Graph (e.g., using Chart.js or any other library) -->
                        <canvas id="servicesUsageGraph"></canvas>
                    </div>
                </div>
            </div>
           
            <!-- Graph for Services Usage -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>In-House Services Usage Overview</h5>
                    </div>
                    <div class="card-body">
                        <!-- Placeholder for Graph (e.g., using Chart.js or any other library) -->
                        <canvas id="inhouseServicesUsageGraph"></canvas>
                    </div>
                </div>
            </div>

            <!-- Graph for Clients/Vendors or Any Other Stats -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Client & Vendor Activity</h5>
                    </div>
                    <div class="card-body">
                        <!-- Placeholder for Graph (e.g., using Chart.js or any other library) -->
                        <canvas id="clientVendorActivityGraph"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Example Data passed from backend (Laravel)
    var servicesUsageData = @json($servicesUsageData);
    var inhouseServicesUsageData = @json($inhouseServicesUsageData);
    var clientsActivityData = @json($clientsActivityData);

    // Services Usage Graph (Line Chart)
    var ctx1 = document.getElementById('servicesUsageGraph').getContext('2d');
    var servicesUsageGraph = new Chart(ctx1, {
        type: 'line', // Line chart for services usage
        data: {
            labels: servicesUsageData.labels, // Service names
            datasets: [{
                label: 'Service Usage',
                data: servicesUsageData.data, // Data values for usage
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
   
    // Services Usage Graph (Line Chart)
    var ctx2 = document.getElementById('inhouseServicesUsageGraph').getContext('2d');
    var inhouseServicesUsageGraph = new Chart(ctx2, {
        type: 'line', // Line chart for services usage
        data: {
            labels: inhouseServicesUsageData.labels, // Service names
            datasets: [{
                label: 'In-House Service Usage',
                data: inhouseServicesUsageData.data, // Data values for usage
                fill: false,
                borderColor: 'rgb(75, 132, 102)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Client and Vendor Activity Graph (Bar Chart)
    var ctx2 = document.getElementById('clientVendorActivityGraph').getContext('2d');
    var clientVendorActivityGraph = new Chart(ctx2, {
        type: 'bar', // Bar chart for activity
        data: {
            labels: clientsActivityData.labels, // Client and Vendor names
            datasets: [{
                label: 'Activity Count',
                data: clientsActivityData.data, // Activity data
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>


@endsection
