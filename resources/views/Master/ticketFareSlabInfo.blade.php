@extends('admin.layouts.master')
@section('page_title', 'Ticket Fare Slab Info')
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
        <a href="{{ route('ticketfareslab-info.add') }}" class="btn btn-success btn-sm">
            + Add @yield('page_title')
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
                    <div class="row align-items-end">
                        <div class="col-lg-5 col-md-5 mb-2">
                            <label for="txtSearch">Search By Slab Info</label>
                            <input type="text" class="form-control clearable form-control-sm" id="txtSearch" name="txtSearch"
                                placeholder="Slab Info">
                        </div>
                        <div class="col-lg-2 col-md-2 mb-2">
                            <label for="amenityCategory">Category</label>
                            <select class="form-select form-select-sm" id="amenityCategory" name="amenityCategory">
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-2 mb-2">
                            <label for="selStatus">Status</label>
                            <select class="form-select form-select-sm" id="selStatus" name="selStatus">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- BUTTONS -->
                        <div class="col-lg-3 col-md-3 d-flex justify-content-end flex-wrap action-btns gap-1">
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

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle table-sm table-responsive" id="datatable"
                    data-url="{{ route('ticketfareslab-info.dataTableView') }}"
                    data-edit-url="{{ route('ticketfareslab-info.edit', 'ID') }}">
                    <thead class="table-secondary">
                        <tr>
                            <th class="noPrint no-sort">
                                <div class="checkbox">
                                    <input id="checkboxall" name="btSelectItem" class="chkAll" type="checkbox">
                                </div>
                            </th>
                            <th>Sl No</th>
                            <th>Slab Name</th>
                            <th>Operator Name</th>
                            <th>Slab Info</th>
                            <th>Last Modified</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-secondary"></tbody>
                </table>
            </div>
            <div class="footer-background border-success text-center" id="norecord" style="display:none">No record found.</div>
            {{csrf_field()}}
            <input name="hdn_ids" id="hdn_ids" type="hidden">
            <input name="hdn_qs" id="hdn_qs" type="hidden">
            <input type="hidden" id="hdn_model" value="TicketFareSlabInfo">

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div id="customTableInfo"></div>
                <div id="customPagination"></div>
            </div>
        </div>
    </div>
    </div>
</form>

<style>
    .selected-tag {
        display: inline-flex;
        align-items: center;
        background: #ffc107;
        color: #000;
        padding: 5px 10px;
        border-radius: 20px;
        margin: 3px;
        font-size: 13px;
    }

    .selected-tag .remove {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: 6px;
        width: 16px;
        height: 16px;
        font-size: 11px;
        color: #555;
        background: rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        cursor: pointer;
    }

    .selected-tag .remove:hover {
        background: rgba(0, 0, 0, 0.2);
    }

    /* ✅ ADD THIS HERE */
    .table-sm td {
        vertical-align: middle;
    }
</style>

@endsection
@push('scripts')
<script type="module">
    window.bulkActionUrl = "{{ route('admin.bulkAction') }}";

    $('#backoffice-form').on('submit', function(e) {
        e.preventDefault();
    });

    $(document).ready(function() {
        commonAjax.initClearableInputs();
        commonAjax.initTableCheckbox('#checkboxall', '.chkItem');
        getDataTableView();
    });


    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
        getDataTableView(true);
    });


    function formatDate(dateStr) {
        if (!dateStr) return '--';

        let date = new Date(dateStr);

        let options = {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        };

        return date.toLocaleDateString('en-GB', options).replace(/ /g, '-');
    }


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
        let dataTableColumns = [

            {
                data: '',
                className: "noPrint text-center",
                render: (d, t, r) =>
                    `<input class="chkItem" type="checkbox" value="${r.id}">`
            },

            {
                data: 'slNo',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center"
            },

            // Slab Name
            {
                data: 'slab_name',
                defaultContent: "--"
            },
            {
                data: 'operators',
                render: function(data) {

                    if (!data || data.length === 0) return "--";

                    return data.join('<br>');
                }
            },

            //  Operator Name (FIXED)
            {
                data: 'slab_info',
                render: function(data) {

                    if (!data || data.length === 0) return "--";

                    let table = `
            <div class="slab-info-box">
                <table class="table table-sm mb-0 slab-inner-table">
                    <thead>
                        <tr>
                            <th>Fare Range</th>
                            <th>Commission</th>
                            <th>Validity</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

                    data.forEach(row => {
                        table += `
                <tr>
                    <td>
                            ${row.starting_fare} - ${row.upto_fare}
                    </td>
                    <td>
                            ${row.commision}
                    </td>
                    <td>
                        ${formatDate(row.from_date)} → ${formatDate(row.to_date)}
                    </td>
                </tr>
            `;
                    });

                    table += `
                    </tbody>
                </table>
            </div>
        `;

                    return table;
                }
            },

            // Last Modified
            {
                data: null,
                render: function(data, type, row) {

                    let createdBy = row.created_by_name ?? '--';
                    let createdAt = row.created_date ?? '--';

                    let updatedBy = row.updated_by_name ? row.updated_by_name : '--';
                    let updatedAt = (row.updated_date) ? row.updated_date : '--';

                    // Show updated date if exists, else created date
                    let displayDate = (updatedAt != '--') ? updatedAt : createdAt;

                    return `
                        <span
                            class="text-decoration-underline fw-semibold"
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
                        </span>
                    `;
                }
            },

            // Status
            {
                data: 'is_active',
                render: function(data) {
                    let cls = data == 'Active' ? 'badge bg-success' : 'badge bg-danger';
                    return `<span class="${cls}">${data}</span>`;
                },
                className: "text-center"
            },

            // Action
            {
                data: '',
                className: "text-center",
                render: function(data, type, row) {

                    let editUrl = $('#datatable').data('edit-url');

                    return `
            <a class="btn btn-sm btn-info"
               href="${editUrl.replace('ID', row.enc_id)}">
                Edit
            </a>
            <a class="btn btn-sm btn-success btn-view-log"
               data-table="mst_ticket_fare_slab_info"
               data-id="${row.enc_id}">
                Log
            </a>
        `;
                }
            }

        ];

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }

    $(document).ready(function() {

        commonAjax.initSelect2('#amenityCategory', 'Select Amenity Category');

        commonAjax.loadAmenityCategory(0);
    });
</script>
@endpush