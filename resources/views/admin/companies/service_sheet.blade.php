@extends('layouts.app')

<!-- Select2 CSS CDN -->
<title>{{ getSetting('app_name', env('APP_NAME')) }} | Services Sheet</title>

@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 d-flex align-items-center">
                        <a href="{{ url('vendors') }}" class="mr-2"><button class=" btn btn-primary btn-sm"><i class="fas fa-arrow-left"></i></button></a>
                        <h1><strong>{{$company->company_name}}</strong> - Services Sheet</h1>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="card pt-3">
                        <div class="card-body">
                            @foreach($groupedServices as $serviceId => $servicesGroup)
                                @php
                                    $service = $servicesGroup->first();
                                @endphp
                                <div class="accordion">
                                    <div class="accordion-item">
                                        <button class="accordion-button">
                                            <div class="row align-items-center">
                                                &nbsp; &nbsp; 
                                                @if(Str::contains(strtolower($service->title), 'voip') || Str::contains(strtolower($service->title), 'call')
                                                || Str::contains(strtolower($service->title), 'mobile'))
                                                    <i class="fa-solid fa-phone-volume"></i>
                                                @else
                                                <i class="fa-solid fa-envelope"></i>
                                                @endif
                                                &nbsp; <strong>{{ $service->title }}</strong>
                                            </div>
                                            <i class="fas fa-chevron-down icon"></i>
                                        </button>
                                        <div class="accordion-content">
                                            <form id="service-form-{{ $serviceId }}" class="service-form">
                                                @csrf
                                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                                                <input type="hidden" name="company_id" value="{{ $company->id }}">
                                                <div class="row service-row mb-2">
                                                    @php $count = 1; @endphp
                                                    @foreach($descriptive as $desc)
                                                        @php
                                                            $matchedService = $servicesGroup->firstWhere('pivot.descriptive_id', $desc->id);
                                                            $buyRate = $matchedService ? $matchedService->pivot->buy_rate : 0;
                                                            $increaseRate = $matchedService ? $matchedService->pivot->sell_rate : 0;
                                                        @endphp
                                                       <div class="col-md-4 col-lg-4 col-sm-12">
                                                        <div class="row align-items-center service-cards">
                                                            <div class="col-4">
                                                                <label for="desc">{{ $count++ }}) {{ $desc->description_name }}</label>
                                                            </div>
                                                            <div class="col-4 px-1">
                                                                <label for="buy_rate">Buy</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">{{ $currency }}</span> <!-- Add your buy icon here -->
                                                                    </div>
                                                                    <input type="number" step="0.01" name="buy_rate[{{ $desc->id }}]" id="buy_rate_{{ $desc->id }}" value="{{ $buyRate }}" placeholder="Buy Rate" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-4 px-1">
                                                                <label for="sell_rate">Sell</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">{{ $currency }}</span> <!-- Add your sell icon here -->
                                                                    </div>
                                                                    <input type="number" step="0.01" name="sell_rate[{{ $desc->id }}]" id="sell_rate{{ $desc->id }}" value="{{ $increaseRate }}" placeholder="Sell Rate" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    @endforeach
                                                </div>
                                                <div class="row justify-content-end px-2">
                                                    <button type="submit" id="service-form-{{ $serviceId }}" class="btn btn-primary service-form">
                                                        <span id="buttonSpinner" class="buttonSpinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                        <span id="buttonText" class="buttonText">Submit</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            const buttons = document.querySelectorAll(".accordion-button");
            buttons.forEach(button => {
                button.addEventListener("click", function() {
                    const content = this.nextElementSibling;
                    const icon = this.querySelector(".icon");

                    // Toggle the current panel
                    if (content.style.display === "block") {
                        content.style.display = "none";
                        icon.classList.remove("rotate");
                    } else {
                        // Close all other panels
                        const allContents = document.querySelectorAll(".accordion-content");
                        const allIcons = document.querySelectorAll(".icon");
                        allContents.forEach(item => item.style.display = "none");
                        allIcons.forEach(icn => icn.classList.remove("rotate"));

                        content.style.display = "block";
                        icon.classList.add("rotate");
                    }
                });
            });
        });

        $(document).ready(function() {
            // Handle form submission
            $('.service-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Show the spinner and disable the button
                const submitButton = $('.service-form');
                const buttonSpinner = $('.buttonSpinner');
                const buttonText = $('.buttonText');
                submitButton.prop('disabled', true);
                buttonSpinner.removeClass('d-none');
                buttonText.text('Processing...');

                var form = $(this);
                var formData = form.serialize(); // Serialize the form data

                $.ajax({
                    url: '{{ route("vendors.updateCompanyServicesRates") }}', // Replace with your endpoint URL
                    method: 'put',
                    data: formData,
                    success: function(response) {
                        // Handle successful response
                        toastr.success('Form submitted successfully');
                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                        console.error('Error submitting form:', error);
                        toastr.error('Error submitting form. Please try again.');
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


    </script>
@endsection
