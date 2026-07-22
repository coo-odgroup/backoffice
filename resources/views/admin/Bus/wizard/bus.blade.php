@extends('admin.layouts.master')
@section('page_title', 'Bus')
@section('content')

<?php
// $page_name = 'All ' . trim($__env->yieldContent('page_title'));
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
        <a href="{{ route('bus.step1') }}" class="btn btn-success btn-sm">
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
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6 mb-2">
                            <label for="txtSearch">Search</label>
                            <input type="text" class="form-control clearable form-control-sm" id="txtSearch" name="txtSearch"
                                placeholder="Bus Name / Bus No / Via">
                        </div>

                        <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-2">
                            <label for="operator">Operator</label>
                            <select class="form-select form-select-sm users" id="operator" name="operator">
                                <option value="">Select Operator</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-2">
                            <label for="selStatus">Status</label>
                            <select class="form-select form-select-sm" id="selStatus" name="selStatus">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-2">
                            <label for="source">Source</label>
                            <select class="form-select form-select-sm selCity" id="source" name="source">
                                <option value="">Select Source</option>
                            </select>
                        </div>

                        <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-2">
                            <label for="destination">Destination</label>
                            <select class="form-select form-select-sm selCity" id="destination" name="destination">
                                <option value="">Select Destination</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6 mb-2 d-flex justify-content-end flex-wrap action-btns gap-1">
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
                    data-url="{{ route('bus.dataTableView') }}"
                    data-copy-url="{{ route('bus.copy', 'ID') }}"
                    data-clone-url="{{ route('bus.clone', 'ID') }}"
                    data-businfo-edit-url="{{ route('bus.step1', 'ID') }}"
                    data-seats-routes-edit-url="{{ route('bus.step2', 'ID') }}"
                    data-contact-edit-url="{{ route('bus.step6', 'ID') }}"
                    data-moreinfo-edit-url="{{ route('bus.step7', 'ID') }}">
                    <thead class="table-secondary">
                        <tr>
                            <th class="noPrint no-sort">
                                <div class="checkbox">
                                    <input id="checkboxall" name="btSelectItem" class="chkAll" type="checkbox">
                                </div>
                            </th>
                            <th>Sl No</th>
                            <th>Bus Operator</th>
                            <th>Bus Name</th>
                            <th>Bus Number</th>
                            <th>Source >> Destination</th>
                            <th>Last Modified</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="footer-background border-success text-center" id="norecord" style="display:none">No record found.</div>
            {{csrf_field()}}
            <input name="hdn_ids" id="hdn_ids" type="hidden">
            <input name="hdn_qs" id="hdn_qs" type="hidden">
            <input type="hidden" id="hdn_model" value="Bus">

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
        commonAjax.initClearableInputs();
        commonAjax.initTableCheckbox('#checkboxall', '.chkItem');
        getDataTableView();

        commonAjax.initSelect2('.selCity', 'Select City');
        commonAjax.loadCityList(0);
        commonAjax.loadCityList(0);

        commonAjax.initSelect2('.users', 'Select Operator');
        commonAjax.loadUsersList('OPERATOR', 0);
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
        let operator = '';
        let source = '';
        let destination = '';

        if ($('#txtSearch').val() != '') {
            txtSearch = $('#txtSearch').val();
        }

        if ($('#selStatus').val() != '') {
            selStatus = $('#selStatus').val();
        }

        if ($('#operator').val() != '') {
            operator = $('#operator').val();
        }

        if ($('#source').val() != '') {
            source = $('#source').val();
        }

        if ($('#destination').val() != '') {
            destination = $('#destination').val();
        }

        let tableId = 'datatable';
        let orderBy = [2, 'desc'];
        let searchParams = {
            txtsearch: txtSearch,
            selstatus: selStatus,
            operator: operator,
            source: source,
            destination: destination
        };
        let displayColumns = [1, 2, 3, 4, 5, 6];
        let dataTableColumns = [{
                data: '',
                render: function(data, type, row) {
                    return '<div class="checkbox"><input class="chkItem" type="checkbox" id="check' + row.bus_id +
                        '" name="chkStd' + row.bus_id + '" value="' + row.bus_id +
                        '" ></div>';
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
                data: null,
                render: function(data, type, row) {
                    return `
                        ${row.operator?.organization_name ?? ''}<br>
                        [${row.operator?.name ?? ''}]
                    `;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        ${row.bus_name}<br>
                        (Bus ID : ${row.bus_id})
                    `;
                }
            },
            {
                data: 'bus_number',
                defaultContent: "--"
            },
            {
                data: null,
                defaultContent: "--",
                render: function(data, type, row) {

                    if (row.routemap && row.routemap.length > 0) {

                        let routes = row.routemap.map(function(item) {

                            if (item.route && item.route.boardingcity && item.route.droppingcity) {
                                return item.route.boardingcity.city_name +
                                    ' >> ' +
                                    item.route.droppingcity.city_name;
                            }

                            return null;

                        }).filter(Boolean);

                        return routes.length ? routes.join('<br>') : "--";
                    }

                    return "--";
                }
            },
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
                    let copyUrl = $('#' + tableId).data('copy-url');
                    let cloneUrl = $('#' + tableId).data('clone-url');
                    let businfoEditUrl = $('#' + tableId).data('businfo-edit-url');
                    let seatsRoutesEditUrl = $('#' + tableId).data('seats-routes-edit-url');
                    let contactEditUrl = $('#' + tableId).data('contact-edit-url');
                    let moreinfoEditUrl = $('#' + tableId).data('moreinfo-edit-url');

                    if (!copyUrl) return '';
                    if (!cloneUrl) return '';
                    if (!businfoEditUrl) return '';
                    if (!seatsRoutesEditUrl) return '';
                    if (!contactEditUrl) return '';
                    if (!moreinfoEditUrl) return '';

                    let copy_url = copyUrl.replace('ID', row.enc_bus_id);
                    let clone_url = cloneUrl.replace('ID', row.enc_bus_id);
                    let businfo_url = businfoEditUrl.replace('ID', row.enc_bus_id) + '/save/edit';
                    let seatsroutes_url = seatsRoutesEditUrl.replace('ID', row.enc_bus_id) + '/save/edit';
                    let contact_url = contactEditUrl.replace('ID', row.enc_bus_id) + '/save/edit';
                    let moreinfo_url = moreinfoEditUrl.replace('ID', row.enc_bus_id) + '/save/edit';

                    return `
                        <div class="d-inline-flex gap-1">

                            <div class="dropdown">
                              <a href="javascript:void(0);"
                            class="btn btn-sm btn-primary btn-view-bus"
                            data-table="bus"
                            data-id="${row.enc_bus_id}">
                                <i class="fa fa-eye"></i>
                            </a>
                                <button class="btn btn-sm btn-info text-white dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                    <i class="fa fa-edit"></i> 
                                </button>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item"
                                        href="${copy_url}">
                                        <i class="fa fa-clone"></i> Copy
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                        href="${clone_url}">
                                        <i class="fa fa-clone"></i> Clone
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                        href="${businfo_url}">
                                        <i class="fa fa-pen"></i> Edit Bus Info
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                        href="${seatsroutes_url}">
                                        <i class="fa fa-pencil-square"></i> Edit Seats & Routes
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                        href="${contact_url}">
                                        <i class="fa fa-phone"></i> Edit Contact
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                        href="${moreinfo_url}">
                                        <i class="fa fa-pencil-square"></i> Edit More Info
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <a href="javascript:void(0);"
                            class="btn btn-sm btn-success btn-view-log"
                            data-table="bus"
                            data-id="${row.enc_bus_id}">
                                <i class="fa fa-history"></i>
                            </a>

                          

                        </div>
                    `;
                },
                className: "noPrint text-center"
            }
        ]

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }
</script>
@endpush