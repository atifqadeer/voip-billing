<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ getSetting('app_name', env('APP_NAME')) }}</title>

    <link href="{{ asset('dist/fonts/font.woff2') }}" rel="stylesheet">
    
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
        
    <!-- Scripts -->
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    {{-- <link rel="stylesheet" href="{{asset('dist/css/all.min.css')}}"> --}}

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('css/custom_dashboard.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/js/chart.js') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/select2-bootstrap4.min.css') }}">

    <!-- Toastr CSS -->
    <link href="{{ asset('dist/css/toastr.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/css/dataTables.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet"/>

</head>
<body>
<div class="page-content">
    @include('layouts/sidebar')
    @yield('content')
    @include('layouts/footer')
</div>
        @yield('scripts')
</body>
</html>
