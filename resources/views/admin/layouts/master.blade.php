<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="description" content="simple and responsive tables" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css',
          'resources/js/admin.js'])
   
</head>

<body>
     <!-- Navbar -->
    @include('admin.inc.loader')
    <!-- Navbar -->
    @include('admin.inc.navbar')
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        @include('admin.inc.sidebar')
        <!-- Page Content -->
        <div id="page-content-wrapper" class="w-100 p-4">
            @yield('content')
        </div>
    </div>

@push('scripts')

<script type="module">
        document.addEventListener('DOMContentLoaded', function () {

            const menuToggle = document.getElementById("menu-toggle");
            const sidebar = document.getElementById("sidebar-wrapper");
            const closeSidebar = document.getElementById("close-sidebar");

            if (menuToggle) {
                menuToggle.addEventListener("click", function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.toggle("active");
                    } else {
                        sidebar.classList.toggle("collapsed");
                    }
                });
            }

            if (closeSidebar) {
                closeSidebar.addEventListener("click", function() {
                    sidebar.classList.remove("active");
                });
            }

        });
</script>

@stack('scripts')
</body>
</html>
