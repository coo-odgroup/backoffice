<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
     <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/css/beyond.min.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap.css') }}">

      @stack('styles')

    @vite([
        'resources/adminlte/css/adminlte.min.css',
        'resources/adminlte/js/adminlte.min.js',
        'resources/js/admin.js'
    ])
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

    {{-- CONTENT --}}
    <div class="content-wrapper p-4">
        @yield('content')
    </div>

</div>
 <script src="{{ asset('assets/js/jquery-2.0.3.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/beyond.min.js') }}"></script>

<script src="{{ asset('assets/js/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatable/dataTables.bootstrap.min.js') }}"></script>

@stack('scripts')
</body>
</html>
