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
                                <span class="widget-caption">Booking Report</span>
                                <div class="widget-buttons">
                                    <a href="javascript:void(0)" class="btn btn-primary" onclick="toggleFilter()">
                                        <i class="fa fa-search"></i> Search
                                    </a>
                                    <a href="#" class="btn btn-success">+ Add Bus</a>
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
                            <!-- FILTER -->
                            <!-- TABLE AREA -->
                            <div class="widget-header bordered-bottom bordered-blueberry table_body">
                                <span class="widget-caption">Horizontal Form</span>
                            </div>
                            <div class="widget-body">
                                <div id="horizontal-form">
                                    <form class="form-horizontal" role="form">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-2 control-label no-padding-right">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
                                                <p class="help-block">Example block-level help text here.</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputPassword3" class="col-sm-2 control-label no-padding-right">Password</label>
                                            <div class="col-sm-10">
                                                <input type="password" class="form-control" id="inputPassword3" placeholder="Password">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox">
                                                        <span class="text">Remember me next time.</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" class="btn btn-default">Sign in</button>
                                            </div>
                                        </div>
                                    </form>
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
@endpush
<!--Basic Scripts-->
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
        $("#filterBox").slideToggle();
    }
</script>