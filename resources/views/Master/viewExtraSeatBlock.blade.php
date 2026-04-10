@extends('admin.layouts.master')
@section('page_title', 'Extra Seat Block')
@section('content')

<?php
$page_name = 'All ' . trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">@yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <button type="button" id="btnToggleFilter" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-magnifying-glass me-1"></i>
            <span class="btn-text">Filter</span>
        </button>
        <a href="{{ route('extra-seat-block.add') }}" class="btn btn-success btn-sm">
            + Add @yield('page_title')
        </a>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate>
    <div class="card">
        <div class="card-body">
            <!-- FILTER -->
            <div class="mb-1 border-bottom d-none" id="filterBox">
                <div class="card-body">
                    <div class="row align-items-end">

                         <div class="col-lg-3 col-md-3">
                            <label for="operator">Operator</label>
                            <select class="form-select form-select-sm" id="operator" name="operator">
                                <option value="">Select Operator</option>
                            </select>
                         </div>

                        <div class="col-lg-3 col-md-3">
                            <label for="bus">Bus</label>
                            <select class="form-select form-select-sm" id="bus" name="bus">
                                <option value="">Select Bus</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-2">
                            <label for="source">Source</label>
                            <select class="form-select form-select-sm" id="source" name="source">
                                <option value="">Select Source</option>
                            </select>
                        </div>

                         <div class="col-lg-2 col-md-2">
                            <label for="destination">Destination</label>
                            <select class="form-select form-select-sm" id="destination" name="destination">
                                <option value="">Select Destination</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-lg-2 col-md-6">
                            <label for="selStatus">Status</label>
                            <select class="form-select form-select-sm" id="selStatus" name="selStatus">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                         <div class="col-lg-3 col-md-3 mt-1">
                            <label for="fromDate">From Date</label>
                            <input type="date" id="fromDate" name="fromDate" class="form-control form-control-sm" placeholder="From Date">
                         </div>

                        <div class="col-lg-3 col-md-3">
                            <label for="toDate">To Date</label>
                            <input type="date" id="toDate" name="toDate" class="form-control form-control-sm" placeholder="To Date">
                        </div>

                         <div class="col-lg-2 col-md-6">
                            <label for="reason">Reason</label>
                            <select class="form-select form-select-sm" id="reason" name="reason">
                            </select>
                        </div>


                        <!-- Buttons -->
                        <div class="col-lg-3 d-flex flex-wrap action-btns gap-1 mt-1">
                            <button class="btn btn-primary btn-sm" type="button" onclick="getDataTableView()">
                                <i class="fa-solid fa-search me-1"></i>Search
                            </button>
                            <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                <i class="fa-solid fa-rotate-left me-1"></i>Reset
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Table start -->
            <div id="tableActions">
                <div class="d-flex justify-content-between mb-2">
                    <select id="pageSizeDatatable" class="form-select form-select-sm page-size">
                        <option value="10" selected="selected">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">All</option>
                    </select>
                    <div>
                        <button type="button" id="btnDelete" class="btn btn-warning btn-sm d-none" onclick="actionRec('D');">
                            <i class="fa-solid fa-trash me-1"></i>
                            Delete
                        </button>
                        <button type="button" id="btnActive" class="btn btn-success btn-sm text-white d-none" onclick="actionRec('A');">
                            <i class="fa-solid fa-circle-check me-1"></i>
                            Active
                        </button>
                        <button type="button" id="btnInactive" class="btn btn-danger btn-sm d-none" onclick="actionRec('UN');">
                            <i class="fa-solid fa-times me-1"></i>
                            Inactive
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div id="utilitiesTop">
                    <button type="button" id="btnExcel" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-file-excel me-1"></i>
                    </button>
                    <button type="button" id="btnPdf" class="btn btn-warning btn-sm text-white">
                        <i class="fa-solid fa-file-pdf me-1"></i>
                    </button>
                    <button type="button" id="btnPrint" class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-print me-1"></i>
                    </button>
                </div>
                <div id="customPaginationTop"></div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle table-sm table-responsive" id="datatable"
                    data-url="{{ route('extra-seat-block.dataTableView') }}"
                    data-edit-url="{{ route('extra-seat-block.edit', 'ID') }}">
                    <thead class="table-secondary">
                        <tr>
                            <!-- <th class="noPrint no-sort">
                                <div class="checkbox">
                                    <input id="checkboxall" name="btSelectItem" class="chkAll" type="checkbox">
                                </div>
                            </th> -->
                            <th>Sl No</th>
                            <th>Opeator</th>
                            <th>Bus Name/No</th>
                            <th>Route</th>
                            <th class="no-sort">Extra Seat Block Info</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="footer-background border-success text-center" id="norecord" style="display:none">No record found.</div>
            {{csrf_field()}}
            <input name="hdn_ids" id="hdn_ids" type="hidden">
            <input name="hdn_qs" id="hdn_qs" type="hidden">
            <input type="hidden" id="hdn_model" value="Brand">

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div id="customTableInfo"></div>
                <div id="customPagination"></div>
            </div>
        </div>
    </div>
    </div>
</form>


@endsection
@push('scripts')

<script type="module">
    window.bulkActionUrl = "{{ route('admin.bulkAction') }}";

    $('#backoffice-form').on('submit', function(e) {
        e.preventDefault();
    });

    $(document).ready(function() {
        commonAjax.initTableCheckbox('#checkboxall', '.chkItem');
        commonAjax.initSelect2('#operator', 'Select Operator');
        commonAjax.initSelect2('#bus', 'Select Bus');
        commonAjax.initSelect2('#source', 'Select Source');
        commonAjax.initSelect2('#destination', 'Select Destination');
        commonAjax.initSelect2('#reason', 'Select Reason');
        commonAjax.loadCountryList();
        commonAjax.initClearableInputs();
        getDataTableView();
    });


    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
        getDataTableView(true);
    });

    window.getDataTableView = function(reset = true) {

        //  If table already initialized
        if (window.dataTableInstance && reset) {

            // Clear saved state
            window.dataTableInstance.state.clear();

            // Reset length dropdown UI
            $('#pageSizeDatatable').val(10);

            // Reset page length internally
            window.dataTableInstance.page.len(10);

            // Force first page
            window.dataTableInstance.page(0);
        }

        $('#pageSizeDatatable').val(10);
        let txtSearch = '';
        let selStatus = '';
        let countrySearch = '';

        if ($('#txtSearch').val() != '') {
            txtSearch = $('#txtSearch').val();
        }

        if ($('#selStatus').val() != '') {
            selStatus = $('#selStatus').val();
        }
        if ($('#countrySearch').val() != '') {
            countrySearch = $('#countrySearch').val();
        }

        let tableId = 'datatable';
        let orderBy = [2, 'asc'];
        let searchParams = {
            txtSearch: txtSearch,
            selStatus: selStatus,
            countrySearch: countrySearch
        };
        let displayColumns = [1, 2, 3, 4, 5, 6, 7];
        let dataTableColumns = [
            {
                data: 'slNo',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center"
            },
            {
                data: 'brand_name',
                defaultContent: "--"
            },
            {
                data: 'brand_name',
                defaultContent: "--"
            },
            {
                data: 'brand_name',
                defaultContent: "--"
            },
            {
                data: 'brand_name',
                render: function(data, type, row) {

                     let editUrl = $('#' + tableId).data('edit-url');

                    // if (!data || data.length === 0) return "--";

                    let table = `<div class="inner-table-hdr">
                                    <table class="table mb-0 table-hover table-sm">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Date</th>
                                                <th>Seats/Sleeper</th>
                                                <th>Reason</th>
                                                <th>Created By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                    // data.forEach(row => {
                        table += `<tr>
                                        <td class="align-middle">26-Apr-2026</td>
                                        <td class="align-middle">LB5, LB6, U5UB, U6UB, US10UB, US12UB, US9UB, US11UB, 16SS, 18SS, 20SS, 22SS, 24SS, 15SS, 17SS, 19SS, 21SS, 23SS</td>
                                        <td class="align-middle">Blocked By Owner</td>
                                        <td>John Doe<br>03-Apr-2026 12:25:12</td>
                                        <td class="align-middle"> <a class="btn btn-sm btn-info"
                                                href="${editUrl.replace('ID', row.enc_brand_id)}">
                                                <i class="fa fa-edit"></i>
                                             </a>
                                             <a class="btn btn-sm btn-danger"
                                                href="${editUrl.replace('ID', row.enc_brand_id)}">
                                                <i class="fa fa-trash"></i>
                                             </a>
                                        </td>
                                    </tr>
                                     <tr>
                                       <td class="align-middle">26-Apr-2026</td>
                                        <td class="align-middle">LB5, LB6, U5UB, U6UB, US10UB, US12UB, US9UB, US11UB, 16SS, 18SS, 20SS, 22SS, 24SS, 15SS, 17SS, 19SS, 21SS, 23SS</td>
                                        <td class="align-middle">Blocked By Owner</td>
                                        <td>John Doe<br>03-Apr-2026 12:25:12</td>
                                        <td class="align-middle">
                                             <a class="btn btn-sm btn-info"
                                                href="${editUrl.replace('ID', row.enc_brand_id)}">
                                                <i class="fa fa-edit"></i>
                                             </a>
                                             <a class="btn btn-sm btn-danger"
                                                href="${editUrl.replace('ID', row.enc_brand_id)}">
                                                <i class="fa fa-trash"></i>
                                             </a>
                                        </td>
                                    </tr>`;
                    // });

                                    table += `</tbody>
                                             </table>
                                            </div> `;

                    return table;
                },
                className: ""
            }
        ]

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }
</script>
@endpush
