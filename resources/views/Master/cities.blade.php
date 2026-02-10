@extends('admin.layouts.master')

@section('title', 'Users')

@section('content')
<!-- Loading Container -->
<!-- @include('admin.inc.loader') -->

<!--  /Loading Container -->
<!-- Navbar -->
@include('admin.inc.navbar')
<!-- /Navbar -->
<!-- Main Container -->
<div class="main-container container-fluid">
    <!-- Page Container -->
    <div class="page-container">
        <!-- Page Sidebar -->
        @include('admin.inc.sidebar')
        <!-- /Page Sidebar -->
        <!-- Page Content -->
        <div class="page-content">
            <!-- Page Breadcrumb -->
            <div class="page-breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <a href="#">Home</a>
                    </li>
                    <li>
                        <a href="#">Tables</a>
                    </li>
                    <li class="active">Data Tables</li>
                </ul>
            </div>
            <!-- /Page Breadcrumb -->

            <!-- PAGE BODY -->
            <div class="page-body">

                <div class="row">
                    <div class="col-xs-12">

                        <!-- BOOKING REPORT CARD -->
                        <div class="widget">
                            <div class="widget-header">
                                <span class="widget-caption">Add Cities</span>
                                <div class="widget-buttons">
                                    <a href="javascript:void(0)"
                                        id="toggleFilterBtn"
                                        class="btn btn-primary"
                                        onclick="toggleFilter()">
                                        <i class="fa fa-search"></i> Search
                                    </a>

                                    <a href="javascript:void(0)"
                                        id="toggleAddLocationBtn"
                                        class="btn btn-success">
                                        <i class="fa fa-plus"></i> Add Location
                                    </a>
                                </div>
                            </div>


                            <!-- FILTER -->
                            <div class="widget-body" id="filterBox" style="display:none;">
                                <div class="row">

                                    <div class="col-md-3">
                                        <label>Select Operator</label>
                                        <select class="form-control">
                                            <option>Select Operator</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label>From Date</label>
                                        <input type="date" class="form-control">
                                    </div>

                                    <div class="col-md-2">
                                        <label>To Date</label>
                                        <input type="date" class="form-control">
                                    </div>

                                    <div class="col-md-2">
                                        <label>Please Select</label>
                                        <select class="form-control">
                                            <option>Journey Date</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Select Booking</label>
                                        <select class="form-control">
                                            <option>Select Booking</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Enter PNR</label>
                                        <input type="text" placeholder="Enter PNR" class="form-control">
                                    </div>
                                </div>


                                <!-- FILTER BUTTONS -->
                                <div class="row">
                                    <div class="col-md-12 text-right filter-btns">
                                        <button class="btn btn-primary">
                                            <i class="fa fa-check"></i> Submit
                                        </button>
                                        <button class="btn btn-default">
                                            <i class="fa fa-refresh"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- TABLE AREA -->
                            <div class="widget-body table_body">

                                <!-- TOP ACTION BAR -->
                                <div class="row mb-2 align-items-center">

                                    <div class="col-md-1">
                                        <select class="form-control count-size" id="rowLimit">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                        </select>
                                    </div>

                                    <div class="col-md-11 text-right">
                                        <button class="btn btn-success btn-sm">Export</button>
                                        <button class="btn btn-danger btn-sm">Print</button>
                                        <button class="btn btn-warning btn-sm">PDF</button>
                                    </div>

                                </div>

                                <!-- INFO ROW -->
                                <div class="row booking-info-row">

                                </div>

                                <!-- TABLE -->
                                <div class="table table-hover table-striped table-bordered table-condensed">
                                    <table  id="bookingTable" class="table table-bordered table-striped">
                                        <thead class="bordered-blueberry">
                                            <tr>
                                                <th>Sl No.</th>
                                                <th>City Name</th>
                                                <th>Allias</th>
                                                <th>Synnonyms</th>
                                                <th>Created_at</th>
                                                <th>Created_by</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Delhi</td>
                                                <td>DEL</td>
                                                <td>Delhi, New Delhi, Dilli</td>
                                                <td>2024-01-01 12:00:00</td>
                                                <td>Admin</td>
                                                <td><span class="label label-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-primary btn-xs">Edit</button>
                                                    <button class="btn btn-danger btn-xs">Delete</button>
                                                </td>
                                            </tr>

                                            <!-- More rows here -->
                                    </table>
                                </div>

                                <!-- TABLE FOOTER -->
                                <div class="table-footer text-right">

                                    <!-- <div class="total-text">
                                            Total : 78
                                        </div> -->

                                    <ul class="pagination pagination-sm">
                                        <li class="disabled"><a href="#">Prev</a></li>
                                        <li class="active"><a href="#">1</a></li>
                                        <li><a href="#">2</a></li>
                                        <li><a href="#">Next</a></li>
                                    </ul>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <!-- /Page Content -->
    </div>
    <!-- /Page Container -->
    <!-- Main Container -->

</div>
@endsection

@push('scripts')
<script>
    // $(function () {
    //     $('#usersTable').DataTable();
    // });
</script>
<script src="assets/js/jquery-2.0.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<!--Beyond Scripts-->
<script src="assets/js/beyond.min.js"></script>

<!--Page Related Scripts-->
<script src="assets/js/datatable/jquery.dataTables.min.js"></script>
<script src="assets/js/datatable/ZeroClipboard.js"></script>
<script src="assets/js/datatable/dataTables.tableTools.min.js"></script>
<script src="assets/js/datatable/dataTables.bootstrap.min.js"></script>
<script src="assets/js/datatable/datatables-init.js"></script>
<script>
    InitiateSimpleDataTable.init();
    InitiateEditableDataTable.init();
    InitiateExpandableDataTable.init();
    InitiateSearchableDataTable.init();
</script>
<!--Google Analytics::Demo Only-->
<script>
    (function(i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function() {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'http://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-52103994-1', 'auto');
    ga('send', 'pageview');
</script>

<script>
    function toggleFilter() {
        const filterBox = $("#filterBox");
        const searchBtn = $("#toggleFilterBtn");

        if (filterBox.hasClass("d-none")) {

            // show
            filterBox
                .removeClass("d-none")
                .hide()
                .slideDown(300);

            searchBtn.html('<i class="fa fa-times-circle"></i> Close');

        } else {

            // hide
            filterBox.slideUp(300, function () {
                filterBox.addClass("d-none");
            });

            searchBtn.html('<i class="fa fa-search"></i> Search');
        }
    }
</script>
   b

@endpush