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

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/v5/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/beyond.min.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/skins/custom_style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    
    @stack('styles')


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

    <!-- SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->



    <!-- DataTables Core -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Bootstrap 3 Integration -->
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap.min.js"></script>

    <!-- Buttons Core -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

    <!-- Buttons Bootstrap 3 Integration -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap.min.js"></script>

    <!-- Dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- Buttons Extensions -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- Beyond Theme -->
    <!-- <script src="{{ asset('assets/js/beyond.min.js') }}"></script> -->

    <!-- Common (ALWAYS LAST) -->
    <script src="{{ asset('assets/js/common-datatable.js') }}"></script>
    <script src="{{ asset('assets/js/commonAjax.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    const menuToggle = document.getElementById("menu-toggle");
    const sidebar = document.getElementById("sidebar-wrapper");
    const closeSidebar = document.getElementById("close-sidebar");

    menuToggle.addEventListener("click", function() {

        if (window.innerWidth <= 768) {
            sidebar.classList.toggle("active");
        } else {
            sidebar.classList.toggle("collapsed");
        }

    });

    if (closeSidebar) {
        closeSidebar.addEventListener("click", function() {
            sidebar.classList.remove("active");
        });
    }
    </script>

    @stack('scripts')

</body>

</html>