<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="description" content="simple and responsive tables" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <link rel="shortcut icon"
          href="{{ asset('assets/img/favicon.png') }}"
          type="image/x-icon">
     <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/css/beyond.min.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/css/skins/custom_style.css') }}">


      @stack('styles')

    @vite([
        'resources/js/admin.js'
    ])
</head>

<body>
<div class="wrapper">

    {{-- CONTENT --}}
    <div class="content-wrapper p-4">
        @yield('content')
    </div>

</div>
 <script src="{{ asset('assets/js/jquery-2.0.3.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/helpers.js') }}"></script>
<script src="{{ asset('assets/js/beyond.min.js') }}"></script>

<script src="{{ asset('assets/js/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatable/dataTables.bootstrap.min.js') }}"></script>

@stack('scripts')
</body>
</html>
