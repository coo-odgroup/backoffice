@extends('admin.layouts.master')
@section('page_title', 'Support Ticket')
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
        <a href="{{ route('supportTicket.add') }}" class="btn btn-success btn-sm">
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
                        <!-- Search -->
                        <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                            <label for="txtSearch">Search By Ticket Code</label>
                            <input type="text" class="form-control clearable form-control-sm" id="txtSearch" name="txtSearch"
                                placeholder="Ticket Code">
                        </div>

                        <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                            <label for="module">Module<span class="text-danger important">*</span></label>
                            <select class="form-select form-select-sm selOrg" id="module" name="module">
                                <option value="">Select Module</option>
                            </select>
                        </div>

                        <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                            <label for="priority">Priority<span class="text-danger important">*</span></label>
                            <select class="form-select form-select-sm selOrg" id="priority" name="priority">
                                <option value="">Select Priority</option>
                            </select>
                        </div>

                        <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                            <label for="environment">Environment</label>
                            <select class="form-select form-select-sm environment" id="environment" name="environment">
                                <option value="">Select Environment</option>
                                <option value="Staging">Staging</option>
                                <option value="Production">Production</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                            <label for="selStatus">Status</label>
                            <select class="form-select form-select-sm" id="selStatus" name="selStatus">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 mb-2 d-flex justify-content-end flex-wrap action-btns gap-1">
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
                    data-url="{{ route('supportTicket.dataTableView') }}"
                    data-edit-url="{{ route('supportTicket.edit', 'ID') }}">
                    <thead class="table-secondary">
                        <tr>
                            <th class="noPrint no-sort">
                                <div class="checkbox">
                                    <input id="checkboxall" name="btSelectItem" class="chkAll" type="checkbox">
                                </div>
                            </th>
                            <th>Sl No</th>
                            <th>Code</th>
                            <th>Module</th>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Last Modified</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="footer-background border-success text-center" id="norecord" style="display:none">No record found.</div>
                {{csrf_field()}}
                <input name="hdn_ids" id="hdn_ids" type="hidden">
                <input name="hdn_qs" id="hdn_qs" type="hidden">
                <input type="hidden" id="hdn_model" value="SupportTicket">

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div id="customTableInfo"></div>
                    <div id="customPagination"></div>
                </div>
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
        commonAjax.initClearableInputs();
        commonAjax.initTableCheckbox('#checkboxall', '.chkItem');
        commonAjax.initSelect2('#module', 'Select Module');
        commonAjax.initSelect2('#priority', 'Select Priority');
        commonAjax.initSelect2('#environment', 'Select Environment');

        commonAjax.loadAnnextureList([
            'SUPPORT_TICKET_MODULE',
            'SUPPORT_TICKET_PRIORITY'
        ], function(data) {

            renderDropdown('#module', data.SUPPORT_TICKET_MODULE || []);
            renderDropdown('#priority', data.SUPPORT_TICKET_PRIORITY || []);

        });

        // Static Environment Options
        $('#environment').html(`
                <option value="">Select Environment</option>
                <option value="Staging">Staging</option>
                <option value="Production">Production</option>
            `).trigger('change');
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
        let module = '';
        let priority = '';
        let environment = '';

        if ($('#txtSearch').val() != '') {
            txtSearch = $('#txtSearch').val();
        }
        if ($('#selStatus').val() != '') {
            selStatus = $('#selStatus').val();
        }
        if ($('#module').val() != '') {
            module = $('#module').val();
        }

        if ($('#priority').val() != '') {
            priority = $('#priority').val();
        }

        if ($('#environment').val() != '') {
            environment = $('#environment').val();
        }

        let tableId = 'datatable';
        let orderBy = [2, 'asc'];
        let searchParams = {
            txtSearch,
            selStatus,
            module,
            priority,
            environment
        };
        let displayColumns = [1, 2, 3, 4, 5, 6];
        let dataTableColumns = [

            {
                data: '',
                render: function(data, type, row) {
                    return '<div class="checkbox"><input class="chkItem" type="checkbox" id="check' + row.id + '" name="chkStd' + row.id + '" value="' + row.id + '"></div>';
                },
                className: "text-center"
            },

            {
                data: 'slNo',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center"
            },

            {
                data: 'ticket_code',
                defaultContent: '--'
            },

            {
                data: 'module',
                defaultContent: '--'
            },

            {
                data: 'title',
                defaultContent: '--'
            },

            {
                data: 'priority',
                defaultContent: '--'
            },

            {
                data: 'category',
                defaultContent: '--'
            },

            {
                data: 'status',
                defaultContent: '--'
            },

            {
                data: null,
                render: function(data, type, row) {

                    let createdBy = row.created_by_name ?? '--';
                    let createdAt = row.created_date ?? '--';

                    let updatedBy = row.updated_by_name ?? '--';
                    let updatedAt = row.updated_date ?? '--';

                    let displayDate = (updatedAt != '--') ? updatedAt : createdAt;

                    return displayDate;
                }
            },
            {
                data: '',
                render: function(data, type, row) {

                    let editUrl = $('#datatable').data('edit-url');

                    return `
            <a class="btn btn-sm btn-info"
               href="${editUrl.replace('ID',row.enc_id)}">
                <i class="fa fa-edit"></i>
            </a>

            <a href="javascript:void(0)"
               class="btn btn-sm btn-success btn-view-log"
               data-table="support_tickets"
               data-id="${row.enc_id}">
               <i class="fa fa-history"></i>
            </a>
        `;
                },
                className: "text-center"
            }

        ];

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }

    function renderDropdown(selector, data = [], selected = '') {

        let options = '<option value="">Select Option</option>';

        $.each(data, function(index, item) {

            let value = item.id ?? item.value;
            let text = item.name ?? item.annexture_value ?? item.label;

            let isSelected = (selected == value) ? 'selected' : '';

            options += `<option value="${value}" ${isSelected}>${text}</option>`;
        });

        $(selector).html(options).trigger('change');
    }
</script>
@endpush