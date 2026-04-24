@extends('admin.layouts.master')
@section('page_title', 'Seat Block')
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
        <a href="{{ route('seat-block.add') }}" class="btn btn-success btn-sm">
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
                            <select class="form-select form-select-sm selCity" id="source" name="source">
                                <option value="">Select Source</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-2">
                            <label for="destination">Destination</label>
                            <select class="form-select form-select-sm selCity " id="destination" name="destination">
                                <option value="">Select Destination</option>
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
                    data-url="{{ route('seat-block.dataTableView') }}"
                    data-edit-url="{{ route('seat-block.edit', 'ID') }}">
                    <thead class="table-secondary">
                        <tr>

                            <th>Sl No</th>
                            <th>Opeator</th>
                            <th>Bus Name / No</th>
                            <th>Route</th>
                            <th class="no-sort">Seat Block Info</th>
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

    <div class="modal fade" id="deleteReasonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content seat-delete-modal">

                <div class="modal-header seat-delete-header">
                    <h5 class="modal-title">
                        Select The Reason For Delete
                    </h5>

                    <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="delete_enc_id">

                    <label class="fw-bold mb-2 text-primary d-block">
                        Reason
                    </label>

                    <select id="delete_reason" class="form-select">
                        <option value="">Select Reason</option>
                    </select>

                    <small class="text-muted mt-2 d-block">
                        Select a valid reason before deleting.
                    </small>

                </div>

                <div class="modal-footer bg-light">

                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="button"
                        class="btn seat-delete-btn"
                        onclick="confirmDeleteSeat()">
                        <i class="fa fa-trash me-1"></i>
                        Delete
                    </button>

                </div>

            </div>
        </div>
    </div>
</form>
<style>
    /* ===== Delete Reason Modal (Matches Admin Page Style) ===== */

    .seat-delete-modal {
        border: 1px solid #d8dde6;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        background: #ffffff;
    }

    /* Header */
    .seat-delete-header {
        background: #0d6efd;
        color: #ffffff;
        border-bottom: 1px solid #0b5ed7;
        padding: 12px 16px;
    }

    .seat-delete-header .modal-title {
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }

    .seat-delete-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 1;
    }

    /* Body */
    .seat-delete-modal .modal-body {
        padding: 18px 16px;
        background: #ffffff;
    }

    .seat-delete-modal label {
        font-size: 13px;
        font-weight: 600;
        color: #001a57;
        margin-bottom: 6px;
    }

    .seat-delete-modal .form-select {
        height: 38px;
        border: 1px solid #cfd6df;
        border-radius: 4px;
        font-size: 13px;
        box-shadow: none;
    }

    .seat-delete-modal .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.12rem rgba(13, 110, 253, 0.12);
    }

    .seat-delete-modal small {
        font-size: 12px;
        color: #6c757d;
        margin-top: 6px;
    }

    /* Footer */
    .seat-delete-modal .modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #e5e8ec;
        padding: 10px 16px;
    }

    /* Buttons */
    .seat-delete-modal .btn-secondary {
        font-size: 13px;
        border-radius: 4px;
        padding: 6px 14px;
    }

    .seat-delete-btn {
        background: #dc3545;
        border: 1px solid #dc3545;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        border-radius: 4px;
        padding: 6px 14px;
    }

    .seat-delete-btn:hover {
        background: #bb2d3b;
        border-color: #b02a37;
        color: #ffffff;
    }

    /* Modal backdrop look */
    .modal-content {
        font-family: inherit;
    }

    /* Mobile */
    @media (max-width: 576px) {
        .seat-delete-modal .modal-footer {
            flex-direction: column-reverse;
            gap: 8px;
        }

        .seat-delete-modal .btn {
            width: 100%;
        }
    }








    .bus-seat,
    .bus-sleeper,
    .bus-vertical-sleeper {
        display: inline-block;
        background-size: 100% 100%;
        background-repeat: no-repeat;
        background-position: center;
        cursor: pointer;
    }

    /* sizes */
    .bus-seat {
        width: 42px;
        height: 24px;
    }

    .bus-sleeper {
        width: 72px;
        height: 28px;
    }

    .bus-vertical-sleeper {
        width: 34px;
        height: 72px;
    }

    /* BLUE */
    .selected-seat.bus-seat {
        background-image: url('/assets/seats/Seat_layout_blue.png') !important;
    }

    .selected-seat.bus-sleeper {
        background-image: url('/assets/seats/Sleeper_layout_blue.png') !important;
    }

    .selected-seat.bus-vertical-sleeper {
        background-image: url('/assets/seats/Sleeper_layout_blue_vertical.png') !important;
    }

    /* RED */
    .blocked.bus-seat {
        background-image: url('/assets/seats/Seat_layout_red.png') !important;
    }

    .blocked.bus-sleeper {
        background-image: url('/assets/seats/sleeper_layout_red.png') !important;
    }

    .blocked.bus-vertical-sleeper {
        background-image: url('/assets/seats/sleeper_layout_red_vertical.png') !important;
    }

    /* GREY */
    .disabled.bus-seat {
        background-image: url('/assets/seats/Seat_layout_grey.png') !important;
    }

    .disabled.bus-sleeper {
        background-image: url('/assets/seats/sleeper_layout_grey.png') !important;
    }

    .disabled.bus-vertical-sleeper {
        background-image: url('/assets/seats/sleeper_layout_grey_vertical.png') !important;
        cursor: not-allowed;
    }
</style>


@endsection
@push('scripts')

<script type="module">
    window.bulkActionUrl = "{{ route('admin.bulkAction') }}";

    $(document).ready(function() {

        commonAjax.initSelect2('#operator', 'Select Operator');
        commonAjax.initSelect2('#bus', 'Select Bus');
        commonAjax.initSelect2('#reason', 'Select Reason');
        commonAjax.initSelect2('#source', 'Select Source');
        commonAjax.initSelect2('#destination', 'Select Destination');


        commonAjax.loadBusOperatorDropdown('');
        commonAjax.loadAnnextureList('REASON', '', '#reason');

        setTimeout(function() {
            commonAjax.loadCityList('#source');
            commonAjax.loadCityList('#destination');
        }, 300);
        
        console.log(commonAjax.loadCityList.toString())

        commonAjax.initClearableInputs();

        getDataTableView();
    });

    $('#operator').on('change', function() {

        let operator_id = $(this).val();

        $('#bus').html('');
        $('#scheduleContainer').html(`
                <div class="text-center text-muted">
                    Please select bus
                </div>
            `);

        commonAjax.loadBusListByOperator('#bus', operator_id);

    });

    $('#btnReset').click(function() {

        $('#backoffice-form')[0].reset();
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

        let tableId = 'datatable';

        let todayDate = new Date().toISOString().split('T')[0];

        let searchParams = {
            txtSearch: $('#txtSearch').length ? $('#txtSearch').val() : '',
            operator: $('#operator').val(),
            bus: $('#bus').val(),

            source: $('#source').val(),
            destination: $('#destination').val(),

            fromDate: $('#fromDate').val() || todayDate,
            toDate: $('#toDate').val() || '',

            reason: $('#reason').val()
        };

        let orderBy = [4, 'asc'];

        let displayColumns = [0, 1, 2, 3, 4];

        let dataTableColumns = [

            {
                data: null,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center"
            },

            {
                data: 'operator_name',
                defaultContent: '--'
            },

            {
                data: 'bus_name',
                defaultContent: '--'
            },

            {
                data: 'route_name',
                defaultContent: '--'
            },

            {
                data: 'block_info',
                orderable: false,
                searchable: false,

                render: function(data, type, row) {

                    if (!data || data.length === 0) {
                        return '--';
                    }

                    let editUrl = $('#' + tableId).data('edit-url');

                    let html = `
                        <div class="inner-table-hdr table-responsive">
                            <table class="table table-bordered table-hover table-sm mb-0 fixed-inner-table">
                                <thead class="table-secondary">
                                    <tr>
                                        <th style="width:120px; min-width:120px;">Date</th>
                                        <th style="width:500px; min-width:500px;">Seats / Sleeper</th>
                                        <th style="width:180px; min-width:180px;">Reason</th>
                                        <th style="width:220px; min-width:220px;">Created By</th>
                                        <th style="width:90px; min-width:90px;" class="text-center noPrint">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                    data.forEach(function(item) {

                        html += `
                <tr>
                    <td style="width:120px;">${item.date}</td>

                    <td style="width:500px; white-space:normal; word-break:break-word;">
                        ${item.seat_code}
                    </td>

                    <td style="width:180px;">
                        ${item.reason}
                    </td>

                    <td style="width:220px;">
                        ${item.created_by}<br>
                        <small>${item.created_at}</small>
                    </td>

                    <td style="width:90px;" class="text-center noPrint">
                        ${renderSeatAction(item, editUrl)}
                    </td>

                    
                </tr>
                `;
                    });

                    html += `
                    </tbody>
                </table>
            </div>
            `;

                    return html;
                }
            }
        ];

        window.renderSeatAction = function(item, editUrl) {
            let today = new Date();
            today.setHours(0, 0, 0, 0);

            let rowDate = parseSeatDate(item.date);

            if (rowDate < today) {
                return `
                    <a class="btn btn-sm btn-secondary disabled me-1">
                        <i class="fa fa-edit"></i>
                    </a>

                    <a class="btn btn-sm btn-secondary disabled">
                        <i class="fa fa-trash"></i>
                    </a>
                `;
            }

            return `
                    <a class="btn btn-sm btn-info me-1"
                    href="${editUrl.replace('ID', item.enc_id)}">
                    <i class="fa fa-edit"></i>
                    </a>

                    <a class="btn btn-sm btn-danger"
                    href="javascript:void(0)"
                    onclick="deleteSingleRecord('${item.enc_id}')">
                    <i class="fa fa-trash"></i>
                    </a>
                `;
        }

        window.parseSeatDate = function(str) {
            if (!str) return new Date();

            let parts = str.split('-');

            let months = {
                Jan: 0,
                Feb: 1,
                Mar: 2,
                Apr: 3,
                May: 4,
                Jun: 5,
                Jul: 6,
                Aug: 7,
                Sep: 8,
                Oct: 9,
                Nov: 10,
                Dec: 11
            };

            return new Date(parts[2], months[parts[1]], parts[0]);
        };

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    };

    window.deleteSingleRecord = function(encId) {

        $('#delete_enc_id').val(encId);

        $('#delete_reason').html('<option value="">Loading...</option>');

        let modal = new bootstrap.Modal(
            document.getElementById('deleteReasonModal')
        );

        modal.show();

        commonAjax.loadAnnextureList('REASON', '', '#delete_reason');

        setTimeout(function() {
            $('#delete_reason').trigger('change');
        }, 300);
    };

    window.confirmDeleteSeat = function() {

        let encId = $('#delete_enc_id').val();
        let reason = $('#delete_reason').val();

        if (reason == '') {

            commonAjax.viewAlert(
                'Please select reason');

            return;
        }



        $.ajax({
            url: "{{ route('seat-block.delete') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: encId,
                reason: reason
            },
            success: function(res) {

                $('#deleteReasonModal').modal('hide');

                getDataTableView(false);
            },
            error: function() {

                commonAjax.viewAlert(

                    'Something went wrong'
                );
            }
        });
    };

    window.confirmDeleteSeat = function() {

        let encId = $('#delete_enc_id').val();
        let reason = $('#delete_reason').val();

        if (reason == '') {

            commonAjax.viewAlert('Please select reason');
            return;
        }

        $.ajax({
            url: "{{ route('seat-block.delete') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: encId,
                reason: reason
            },

            beforeSend: function() {

                $('.seat-delete-btn').prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin me-1"></i>Deleting...');
            },

            success: function(res) {

                let modalEl = document.getElementById('deleteReasonModal');
                let modal = bootstrap.Modal.getInstance(modalEl);

                if (modal) {
                    modal.hide();
                }

                $('#delete_reason').val('');
                $('#delete_enc_id').val('');


                getDataTableView(false);
            },

            error: function(xhr) {

                commonAjax.viewAlert('Delete failed');
                console.log(xhr.responseText);
            },

            complete: function() {

                $('.seat-delete-btn').prop('disabled', false)
                    .html('<i class="fa fa-trash me-1"></i>Delete');
            }
        });
    };

    window.confirmDeleteSeat = function() {

        let encId = $('#delete_enc_id').val();
        let reason = $('#delete_reason').val();

        if (reason == '') {
            commonAjax.viewAlert('Please select reason');
            return;
        }

        $.ajax({
            url: "{{ route('seat-block.delete') }}",
            type: "POST",
            dataType: "json",
            data: {
                _token: "{{ csrf_token() }}",
                id: encId,
                reason: reason
            },

            beforeSend: function() {

                $('.seat-delete-btn')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin me-1"></i>Deleting...');
            },

            success: function(res) {

                let modalEl = document.getElementById('deleteReasonModal');
                let modal = bootstrap.Modal.getInstance(modalEl);

                if (modal) {
                    modal.hide();
                }

                $('#delete_reason').val('');
                $('#delete_enc_id').val('');

                getDataTableView(false);
            },

            error: function(xhr) {

                console.log(xhr.responseText);
                commonAjax.viewAlert('Delete failed');
            },

            complete: function() {

                $('.seat-delete-btn')
                    .prop('disabled', false)
                    .html('<i class="fa fa-trash me-1"></i>Delete');
            }
        });
    };
</script>
@endpush