@extends('admin.layouts.master')
@section('content')

<?php
$page_name = 'All Seat Types';
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">Seat Types</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">Seat Types</h5>
    <div>
        <button type="button" id="btnToggleFilter" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-magnifying-glass me-1"></i>
            <span class="btn-text">Filter</span>
        </button>
        <a href="{{ route('seatingtype.add') }}" class="btn btn-success btn-sm">
            + Add Seat Type
        </a>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate>
    <div class="card">
        <div class="card-body">
            <!-- FILTER -->
            <div class="mb-3 border-bottom d-none" id="filterBox">
                <div class="card-body">
                    <div class="row">
                        <!-- FILTER FIELDS -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6 col-sm-6 col-md-6  col-lg-2 mb-2">
                                    <label for="txtSearch">Search By Bus Type</label>
                                    <input type="text" class="form-control" id="txtSearch" name="txtSearch"
                                        placeholder="Bus Type">
                                </div>
                                <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                                    <label for="selStatus">Status</label>
                                    <select class="form-select" id="selStatus" name="selStatus">
                                        <option value="">Select Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- BUTTONS -->
                        <div class="col-12 mt-3 d-flex justify-content-end flex-wrap action-btns">
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
                        <button type="button" id="btnDelete" class="btn btn-warning btn-sm" onclick="actionRec('D');">
                            <i class="fa-solid fa-trash me-1"></i>
                            Delete
                        </button>
                        <button type="button" id="btnActive" class="btn btn-success btn-sm text-white" onclick="actionRec('A');">
                            <i class="fa-solid fa-circle-check me-1"></i>
                            Active
                        </button>
                        <button type="button" id="btnInactive" class="btn btn-danger btn-sm" onclick="actionRec('UN');">
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

            <table class="table table-hover table-bordered align-middle table-sm" id="datatable"
                data-url="{{ route('seatingtype.dataTableView') }}"
                data-edit-url="{{ route('seatingtype.edit', 'ID') }}">
                <thead class="thead-light">
                    <tr>
                        <th class="noPrint no-sort">
                            <input id="checkboxall" name="btSelectItem" class="form-check-input chkAll" type="checkbox">
                        </th>
                        <th>Sl No</th>
                        <th>Seat Type</th>
                        <th>Last Modified</th>
                        <th>Status</th>
                        <th class="no-sort">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="footer-background border-success text-center" id="norecord" style="display:none">No record found.</div>
            {{csrf_field()}}
            <input name="hdn_ids" id="hdn_ids" type="hidden">
            <input name="hdn_qs" id="hdn_qs" type="hidden">
            <input type="hidden" id="hdn_model" value="SeatType">

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

        if ($('#txtSearch').val() != '') {
            txtSearch = $('#txtSearch').val();
        }
        if ($('#selStatus').val() != '') {
            selStatus = $('#selStatus').val();
        }

        let tableId = 'datatable';
        let orderBy = [2, 'asc'];
        let searchParams = {
            txtsearch: txtSearch,
            selstatus: selStatus
        };
        let displayColumns = [1, 2, 3, 4, 5, 6];
        let dataTableColumns = [{
                data: '',
                render: function(data, type, row) {
                    return '<input class="form-check-input chkItem" type="checkbox" id="check' + row.seat_type_id +
                        '" name="chkStd' + row.seat_type_id + '" value="' + row.seat_type_id +
                        '" >';
                },
                className: "noPrint text-center"
            },
            {
                data: 'slNo',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center"
            },
            {
                data: 'seat_type',
                defaultContent: "--"
            },
            {
                data: null,
                render: function (data, type, row) {

                    let createdBy  = row.created_by_name ?? '--';
                    let createdAt  = row.created_date ?? '--';

                    let updatedBy  = row.updated_by_name ? row.updated_by_name : '--';
                    let updatedAt  = (row.updated_date) ? row.updated_date : '--';

                    // Show updated date if exists, else created date
                    let displayDate = (updatedAt!='--') ? updatedAt : createdAt;

                    return `
                        <small
                            class="text-primary fw-semibold"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            data-bs-html="true"
                            title="
                                <div class='audit-box'>
                                    <div><strong>Created By:</strong> ${createdBy}</div>
                                    <div><strong>Created At:</strong> ${createdAt}</div>
                                    <hr class='my-1'>
                                    <div><strong>Updated By:</strong> ${updatedBy}</div>
                                    <div><strong>Updated At:</strong> ${updatedAt}</div>
                                </div>
                            ">
                            ${displayDate}
                        </small>
                    `;
                }
            },
            {
                data: 'is_active',
                render: function(data, type, row) {
                    var cls = ((row.is_active == 'Active') ? 'badge bg-success' : 'badge bg-danger');
                    return '<span class="' + cls + '">' + row.is_active + '</span>';
                },
                className: "text-center"
            },
            {
                data: '',
                render: function(data, type, row) {

                    let editUrl = $('#' + tableId).data('edit-url');

                    if (!editUrl) return '';

                    return `
                        <a class="btn btn-sm btn-info"
                        href="${editUrl.replace('ID', row.enc_seat_type_id)}">
                        <i class="fa fa-edit"></i> Edit
                        </a>

                        <a href="javascript:void(0);"
                            class="btn btn-sm btn-success btn-view-log"
                            data-table="mst_bus_type"
                            data-id="${row.enc_seat_type_id}">
                                <i class="fa fa-history"></i> View Log
                        </a>
                    `;
                },
                className: "noPrint text-center"
            }
        ]

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }
</script>
@endpush
