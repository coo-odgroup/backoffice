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
    <div class="modal fade"
        id="alertModal"
        tabindex="-1"
         data-bs-backdrop="static"
         data-bs-keyboard="false"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">

                <div class="modal-body py-4">

                    <h5 class="alertMessage mb-4"></h5>

                    <div class="d-flex justify-content-center">

                        <button type="button"
                                class="btn btn-danger btn-sm"
                                id="btnAlertOk"
                                data-bs-dismiss="modal">
                            Ok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


	<div class="modal fade"
        id="confirmModal"
        tabindex="-1"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        aria-labelledby="confirmModalLabel"
        aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">

            <div class="modal-body py-4">

                <h5 class="confirmMessage mb-4"></h5>

                <input type="hidden" id="confirmModalHref">

                <div class="d-flex justify-content-center gap-3">
                    
                    <button type="button"
                            class="btn btn-primary btn-sm"
                            id="btnConfirmOk"
                            data-bs-dismiss="modal">
                        Yes
                    </button>

                    <button type="button"
                            class="btn btn-danger btn-sm"
                            id="btnConfirmCancel"
                            data-bs-dismiss="modal">
                        No
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


	<div class="modal fade"
     id="confirmLogoutModal"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">

            <div class="modal-body py-4">

                <h5 class="confirmMessage mb-4">
                    Are you sure you want to logout?
                </h5>

                <div class="d-flex justify-content-center gap-3">

                    <!-- Logout Button -->
                    <a href="https://www.example.com/admin/logout"
                       class="btn btn-primary btn-sm"
                       style="width:120px;">
                        Logout
                    </a>

                    <!-- Cancel Button -->
                    <button type="button"
                            class="btn btn-danger btn-sm"
                            style="width:120px;"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                </div>

            </div>

        </div>
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
