@extends('admin.layouts.master')
@section('page_title', 'Route Distance Management')
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
        <li class="breadcrumb-item">Route Distance Management</li>
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
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate>
    <div class="card">
        <div class="card-body">

            <!-- FILTER -->
            <div class="mb-3 border-bottom " id="filterBox">
                <div class="card-body">
                    <div class="row align-items-end g-2">

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Route</label>
                            <select class="form-select form-select-sm selRoute" id="route_id" name="route_id">
                                <option value="">Select Route</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Location</label>
                            <select class="form-select form-select-sm selCity" id="selCity" name="selCity">
                                <option value="">Select Location</option>
                            </select>
                        </div>


                        <div class="col-lg-2 d-flex justify-content-end flex-wrap action-btns gap-1 mt-2">
                            <button class="btn btn-primary btn-sm" type="button" onclick="getDataTableView()">
                                <i class="fa-solid fa-search me-1"></i>Search
                            </button>
                            <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                <i class="fa-solid fa-rotate-left me-1"></i>Reset
                            </button>
                        </div>

                        <div class="col-lg-4 col-md-12 d-flex justify-content-end align-items-center mt-2">
                            <div id="csvUtilitiesTop" class=" gap-2" style="display:none;">
                                <button type="button" id="btnExportExcel" class="btn btn-success btn-sm btn-mob">
                                    <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                                </button>

                                <button type="button" id="btnUploadExcel" class="btn btn-primary btn-sm btn-mob">
                                    <i class="fa-solid fa-upload me-1"></i> Upload Excel
                                </button>

                                <input type="file" id="excelFileInput" accept=".csv" style="display:none;">
                            </div>
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
                    <div class="d-none">
                        <button type="button" id="btnDelete" class="btn btn-warning btn-sm btn-mob" onclick="actionRec('D');">
                            <i class="fa-solid fa-trash me-1"></i>
                            Delete
                        </button>
                        <button type="button" id="btnActive" class="btn btn-success btn-sm text-white btn-mob" onclick="actionRec('A');">
                            <i class="fa-solid fa-circle-check me-1"></i>
                            Active
                        </button>
                        <button type="button" id="btnInactive" class="btn btn-danger btn-sm btn-mob" onclick="actionRec('UN');">
                            <i class="fa-solid fa-times me-1"></i>
                            Inactive
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div id="utilitiesTop">
                    <button type="button" id="btnExcel" class="btn btn-success btn-sm btn-mob">
                        <i class="fa-solid fa-file-excel me-1"></i>
                    </button>
                    <button type="button" id="btnPdf" class="btn btn-warning btn-sm text-white btn-mob">
                        <i class="fa-solid fa-file-pdf me-1"></i>
                    </button>
                    <button type="button" id="btnPrint" class="btn btn-danger btn-sm btn-mob">
                        <i class="fa-solid fa-print me-1"></i>
                    </button>
                </div>
                <div id="customPaginationTop"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle table-sm table_mob" id="datatable"
                    data-url="{{ route('manage-route-distance.dataTableView') }}">
                    <thead class="table-secondary">
                        <tr>
                            <th>Sl No</th>
                            <th>Location</th>
                            <th>Distance</th>
                            <th>Location</th>
                            <th>Distance</th>
                            <th>Last Modified</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="footer-background border-success text-center" id="norecord" style="display:none">Select a route or location to view distances.</div>
            {{csrf_field()}}
            <input name="hdn_ids" id="hdn_ids" type="hidden">
            <input name="hdn_qs" id="hdn_qs" type="hidden">
            <input type="hidden" id="hdn_model" value="ManageCityContent">

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div id="customTableInfo"></div>
            </div>
        </div>
    </div>
    </div>
</form>
@endsection
@push('scripts')
<script type="module">
    $(document).ready(function() {
        commonAjax.loadRouteList();
        commonAjax.loadCityList();

        initSelect2('#route_id', 'Select Route');
        initSelect2('#selCity', 'Select Location');

        $('#route_id, #selCity').on('select2:select select2:clear', function() {
            toggleCsvUtilities();
        });

        toggleCsvUtilities();
        getDataTableView();
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
        toggleCsvUtilities();
        getDataTableView(true);
    });

    window.getDataTableView = function(reset = true) {

        // //  If table already initialized
        // if (window.dataTableInstance && reset) {

        //     // Clear saved state
        //     window.dataTableInstance.state.clear();

        //     // Reset length dropdown UI
        //     $('#pageSizeDatatable').val(10);

        //     // Reset page length internally
        //     window.dataTableInstance.page.len(10);

        //     // Force first page
        //     window.dataTableInstance.page(0);
        // }

        // $('#pageSizeDatatable').val(10);

        if (window.dataTableInstance && reset) {
            window.dataTableInstance.state.clear();
            $('#pageSizeDatatable').val(10);
            window.dataTableInstance.page.len(10);
            window.dataTableInstance.page(0);
        }
        let txtSearch = '';


        let tableId = 'datatable';
        let orderBy = [0, 'asc'];
        let searchParams = {
            route_id: $('#route_id').val(),
            selCity: $('#selCity').val()
        };
        let displayColumns = [1, 2, 3, 4, 5];
        let dataTableColumns = [{
                data: 'slNo',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center"
            },
            {
                data: 'route_name_1',
                defaultContent: "--"
            },
            {
                data: 'distance_1',
                className: "text-center",
                render: function(data, type, row) {
                    let val = data ?? '';
                    return `
            <input type="text"
                   class="form-control form-control-sm route-distance-input text-center"
                   value="${val}"
                   data-id="${row.route_id_1 ? row.route_id_1 : ''}"
                   data-type="distance_1"
                   style="min-width:90px;">
        `;
                }
            },
            {
                data: 'route_name_2',
                defaultContent: "--"
            },
            {
                data: 'distance_2',
                className: "text-center",
                render: function(data, type, row) {
                    let val = data ?? '';
                    return `
                    <input type="text"
                        class="form-control form-control-sm route-distance-input text-center"
                        value="${val}"
                        data-id="${row.route_id_2 ? row.route_id_2 : ''}"
                        data-type="distance_2"
                        style="min-width:90px;">
                    `;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    let createdBy = row.created_by_name ?? '--';
                    let createdAt = row.created_date ?? '--';
                    let updatedBy = row.updated_by_name ?? '--';
                    let updatedAt = row.updated_date ?? '--';

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
                data: '',
                width: '10%',
                render: function(data, type, row) {
                    return `
                <a href="javascript:void(0);"
                   class="btn btn-sm btn-success btn-view-log"
                   data-table="mst_routes_details"
                   data-id="${row.enc_id}">
                    <i class="fa fa-history"></i> View Log
                </a>
            `;
                },
                className: "noPrint text-center"
            }
        ];

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }
    $(document).on('focus', '.route-distance-input', function() {
        $(this).data('old-value', $(this).val());
    });

    $(document).on('blur', '.route-distance-input', function() {
        let input = $(this);
        let oldValue = input.data('old-value');
        let newValue = input.val().trim();
        let routeId = input.data('id');

        if (!routeId) return;
        if (newValue === oldValue) return;

        if (newValue === '') {
            commonAjax.viewAlert('Distance is required.');
            input.val(oldValue);
            return;
        }

        input.prop('disabled', true);

        $.ajax({
            url: "{{ route('manage-route-distance.updateDistance') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                enc_id: routeId,
                distance: newValue,
                type: input.data('type')
            },
            success: function(response) {
                if (response.status) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated',
                            text: response.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                    }
                    getDataTableView(false);
                } else {
                    input.val(oldValue);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', response.message, 'error');
                    } else {
                        commonAjax.viewAlert(response.message);
                    }
                }
            },
            error: function() {
                input.val(oldValue);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Failed to update distance.', 'error');
                } else {
                    commonAjax.viewAlert('Failed to update distance.');
                }
            },
            complete: function() {
                input.prop('disabled', false);
            }
        });
    });

    $(document).on('change', '#route_id, #selCity', function() {
        toggleCsvUtilities();
    });

    $('#route_id, #selCity').on('select2:select select2:clear', function() {
        toggleCsvUtilities();
    });
    // click content to edit
    $(document).on('click', '.content-view', function() {
        $('.content-edit').addClass('d-none');
        $('.content-view').removeClass('d-none');

        let parent = $(this).closest('.content-edit-box');
        parent.find('.content-view').addClass('d-none');
        parent.find('.content-edit').removeClass('d-none');
    });

    // cancel edit
    $(document).on('click', '.btn-cancel-content', function() {
        let parent = $(this).closest('.content-edit-box');
        parent.find('.content-edit').addClass('d-none');
        parent.find('.content-view').removeClass('d-none');
    });



    $(document).on('click', '#btnExportExcel', function(e) {
        e.preventDefault();
        e.stopPropagation();

        let route_id = $('#route_id').val() || '';
        let selCity = $('#selCity').val() || '';

        let form = $('<form>', {
            method: 'POST',
            action: "{{ route('manage-route-distance.exportCsv') }}",
            style: 'display:none;'
        });

        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content')
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'route_id',
            value: route_id
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'selCity',
            value: selCity
        }));

        $('body').append(form);
        form.trigger('submit');

        setTimeout(function() {
            form.remove();
        }, 500);
    });


    $('#btnUploadExcel').on('click', function() {
        $('#excelFileInput').click();
    });

    $('#excelFileInput').on('change', function() {
        let file = this.files[0];
        if (!file) return;

        let formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('csv_file', file);
        formData.append('route_id', $('#route_id').val() || '');
        formData.append('selCity', $('#selCity').val() || '');

        $.ajax({
            url: "{{ route('manage-route-distance.importCsv') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            beforeSend: function() {
                if (typeof viewLoader === 'function') {
                    viewLoader(true);
                }
                $('#btnUploadExcel').prop('disabled', true);
            },

            success: function(response) {
                if (response.status) {
                    $('#excelFileInput').val('');

                    // reload current filtered table after DB update
                    getDataTableView(false);

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                    } else {
                        commonAjax.viewAlert(response.message || 'CSV uploaded successfully.');
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', response.message || 'CSV import failed', 'error');
                    } else {
                        commonAjax.viewAlert(response.message || 'CSV import failed');
                    }
                }
            },


            error: function(xhr) {
                let msg = 'CSV import failed';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', msg, 'error');
                } else {
                    commonAjax.viewAlert(msg);
                }
            },

            complete: function() {
                if (typeof viewLoader === 'function') {
                    viewLoader(false);
                }
                $('#btnUploadExcel').prop('disabled', false);
            }
        });
    });

    function toggleCsvUtilities() {
        let routeId = ($('#route_id').val() || '').toString().trim();
        let selCity = ($('#selCity').val() || '').toString().trim();

        let hasRoute = routeId !== '' && routeId !== '0' && routeId !== 'null' && routeId !== 'undefined';
        let hasCity = selCity !== '' && selCity !== '0' && selCity !== 'null' && selCity !== 'undefined';

        if (hasRoute || hasCity) {
            $('#csvUtilitiesTop').css('display', 'flex');
        } else {
            $('#csvUtilitiesTop').css('display', 'none');
        }
    }
</script>
@endpush