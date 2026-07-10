@extends('admin.layouts.master')
@section('page_title', 'Popular Routes Management')
@section('content')

<?php
$page_name = 'All ' . trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Manage Route SEO</li>
        <li class="breadcrumb-item active">@yield('page_title')</li>
    </ol>
</nav>

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <button type="button" id="btnToggleFilter" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-magnifying-glass me-1"></i>
            <span class="btn-text">Filter</span>
        </button>
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

                        <div class="col-lg-3 col-md-6 mb-2">
                            <label class="form-label">Route</label>
                            <select class="form-select form-select-sm selRoute" id="route_id" name="route_id">
                                <option value="">Select Route</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-2">
                            <label class="form-label">Route Type</label>
                            <select class="form-select form-select-sm" id="route_type" name="route_type">
                                <option value="">Select Route Type</option>
                                <option value="popular">Popular Route</option>
                                <option value="top">Top Route</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-2">
                            <label class="form-label">Order By</label>
                            <select class="form-select form-select-sm" id="order_by" name="order_by">
                                <option value="">Select Order</option>
                                <option value="ASC">Ascending</option>
                                <option value="DESC">Descending</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-12 d-flex justify-content-end flex-wrap action-btns gap-1 mb-2">
                            <button class="btn btn-primary btn-sm" type="button" onclick="getDataTableView(true)">
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
                    data-url="{{ route('manage-popular-routes.dataTableView') }}">
                    <thead class="table-secondary">
                        <tr>
                            <th>Sl No</th>
                            <th>Routes</th>
                            <th>Is Popular Routes</th>
                            <th>Is Top Routes</th>
                            <th>Sequence</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- <div class="d-flex justify-content-end mt-2 mb-2">
                <button type="button" class="btn btn-primary btn-sm" id="btnUpdatePopularRoutes">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update
                </button>
            </div> -->

            <div class="footer-background border-success text-center" id="norecord" style="display:none">No record found.</div>

            {{ csrf_field() }}
            <input name="hdn_ids" id="hdn_ids" type="hidden">
            <input name="hdn_qs" id="hdn_qs" type="hidden">
            <input type="hidden" id="hdn_model" value="ManagePopularRoutes">

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div id="customTableInfo"></div>
                <div id="customPagination"></div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script type="module">
    $('#backoffice-form').on('submit', function(e) {
        e.preventDefault();
    });

    $(document).ready(function() {
        commonAjax.loadRouteList();
        commonAjax.initSelect2('#route_id', 'Select Route');
        commonAjax.initSelect2('#route_type', 'Select Route Type');
        commonAjax.initSelect2('#order_by', 'Select Order');

        getDataTableView();
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val('').trigger('change');
        getDataTableView(true);
    });

    window.getDataTableView = function(reset = true) {

        if (window.dataTableInstance && reset) {
            window.dataTableInstance.state.clear();
            $('#pageSizeDatatable').val(10);
            window.dataTableInstance.page.len(10);
            window.dataTableInstance.page(0);
        }

        $('#pageSizeDatatable').val(10);

        let tableId = 'datatable';
        let orderBy = [1, 'asc'];

        let searchParams = {
            route_id: $('#route_id').val(),
            route_type: $('#route_type').val(),
            order_by: $('#order_by').val()
        };

        let displayColumns = [0, 1, 2, 3, 4];

        let dataTableColumns = [{
                data: 'slNo',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center"
            },
            {
                data: 'route_name',
                defaultContent: '--'
            },
            {
                data: 'popular',
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function(data, type, row) {
                    let isPopular = parseInt(row.popular) === 1;
                    let isTop = parseInt(row.top) === 1;

                    // for excel/pdf/print/export
                    if (type === 'export' || type === 'print' || type === 'filter' || type === 'sort') {
                        return isPopular ? 'Yes' : 'No';
                    }

                    // for table display
                    return `
                    <input type="checkbox"
                        class="form-check-input popular-route-checkbox"
                        style="width:12px;height:12px;border:1px solid #000;cursor:pointer;"
                        data-route-id="${row.route_id}"
                        ${isPopular ? 'checked' : ''}
                        ${isTop ? 'disabled' : ''}>
                `;
                }
            }, {
                data: 'top',
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function(data, type, row) {
                    let isPopular = parseInt(row.popular) === 1;
                    let isTop = parseInt(row.top) === 1;

                    // for excel/pdf/print/export
                    if (type === 'export' || type === 'print' || type === 'filter' || type === 'sort') {
                        return isTop ? 'Yes' : 'No';
                    }

                    // for table display
                    return `
                        <input type="checkbox"
                            class="form-check-input top-route-checkbox"
                            style="width:12px;height:12px;border:1px solid #000;cursor:pointer;"
                            data-route-id="${row.route_id}"
                            ${isTop ? 'checked' : ''}
                            ${isPopular ? 'disabled' : ''}>
                    `;
                }
            },
            {
                data: 'sequence',
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function(data, type, row) {
                    let seq = row.sequence ?? 0;

                    // for excel/pdf/print/export
                    if (type === 'export' || type === 'print' || type === 'filter' || type === 'sort') {
                        return seq;
                    }

                    // for table display
                    return `
                    <input type="number"
                        min="0"
                        class="form-control form-control-sm route-sequence-input"
                        style="width:90px;margin:auto;"
                        data-route-id="${row.route_id}"
                        data-old-value="${seq}"
                        value="${seq}">
                `;
                }
            },
        ];

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    };

    function saveRouteRow(row, changedInput = null) {
        let routeId = row.find('.route-sequence-input').data('route-id');
        let sequenceNo = parseInt(row.find('.route-sequence-input').val()) || 0;
        let isPopular = row.find('.popular-route-checkbox').is(':checked') ? 1 : 0;
        let isTop = row.find('.top-route-checkbox').is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('manage-popular-routes.update-sequence') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                route_id: routeId,
                sequence_no: sequenceNo,
                is_popular_routes: isPopular,
                is_top_routes: isTop
            },
            beforeSend: function() {
                if (changedInput) {
                    changedInput.prop('disabled', true);
                }
            },
            success: function(res) {
                if (res.status) {
                    // keep latest sequence as old-value after successful save
                    row.find('.route-sequence-input').data('old-value', sequenceNo);

                    // common success alert for BOTH checkbox + sequence
                    if (typeof viewAlert === 'function') {
                        viewAlert('success', res.message);
                    } else if (typeof commonAjax !== 'undefined' && typeof commonAjax.viewAlert === 'function') {
                        commonAjax.viewAlert('Successfully updated', res.message);
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message
                        });
                    }
                } else {
                    getDataTableView(false);

                    if (typeof viewAlert === 'function') {
                        viewAlert('error', res.message || 'Something went wrong');
                    } else if (typeof commonAjax !== 'undefined' && typeof commonAjax.viewAlert === 'function') {
                        commonAjax.viewAlert('Error', res.message || 'Something went wrong');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'Something went wrong'
                        });
                    }
                }
            },
            error: function() {
                getDataTableView(false);

                if (typeof viewAlert === 'function') {
                    viewAlert('error', 'Something went wrong');
                } else if (typeof commonAjax !== 'undefined' && typeof commonAjax.viewAlert === 'function') {
                    commonAjax.viewAlert('Error', 'Something went wrong');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong'
                    });
                }
            },
            complete: function() {
                if (changedInput) {
                    changedInput.prop('disabled', false);
                }
            }
        });
    }

    $(document).on('change', '.popular-route-checkbox', function() {
        let row = $(this).closest('tr');
        let topCheckbox = row.find('.top-route-checkbox');

        if ($(this).is(':checked')) {
            topCheckbox.prop('checked', false).prop('disabled', true);
        } else {
            topCheckbox.prop('disabled', false);
        }

        saveRouteRow(row, $(this));
    });

    $(document).on('change', '.top-route-checkbox', function() {
        let row = $(this).closest('tr');
        let popularCheckbox = row.find('.popular-route-checkbox');

        if ($(this).is(':checked')) {
            popularCheckbox.prop('checked', false).prop('disabled', true);
        } else {
            popularCheckbox.prop('disabled', false);
        }

        saveRouteRow(row, $(this));
    });

    $(document).on('blur', '.route-sequence-input', function() {
        let input = $(this);
        let row = input.closest('tr');
        let newValue = $.trim(input.val());
        let oldValue = input.data('old-value');

        if (newValue === '') {
            newValue = 0;
            input.val(0);
        }

        newValue = parseInt(newValue) || 0;
        oldValue = parseInt(oldValue) || 0;

        if (newValue === oldValue) {
            return;
        }

        saveRouteRow(row, input);
    });

    $(document).on('focus', '.route-sequence-input', function() {
        $(this).data('old-value', $(this).val());
    });

    $(document).on('click', '#btnUpdatePopularRoutes', function() {
        let rows = [];

        $('#datatable tbody tr').each(function() {
            let popularCheckbox = $(this).find('.popular-route-checkbox');
            let topCheckbox = $(this).find('.top-route-checkbox');
            let sequenceInput = $(this).find('.route-sequence-input');

            if (popularCheckbox.length || topCheckbox.length || sequenceInput.length) {
                rows.push({
                    route_id: popularCheckbox.data('route-id') || topCheckbox.data('route-id') || sequenceInput.data('route-id'),
                    is_popular_routes: popularCheckbox.is(':checked') ? 1 : 0,
                    is_top_routes: topCheckbox.is(':checked') ? 1 : 0,
                    sequence_no: sequenceInput.val() || 0
                });
            }
        });

        $.ajax({
            url: "{{ route('manage-popular-routes.add') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                routes: rows
            },
            beforeSend: function() {
                $('#btnUpdatePopularRoutes').prop('disabled', true);
            },
            success: function(res) {
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.message
                    });
                    getDataTableView(false);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message || 'Something went wrong'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong'
                });
            },
            complete: function() {
                $('#btnUpdatePopularRoutes').prop('disabled', false);
            }
        });
    });
</script>
@endpush