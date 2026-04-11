    @extends('admin.layouts.master')
    @section('page_title', 'Bus Schedule')
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
            <a href="{{ route('bus-schedule.add') }}" class="btn btn-success btn-sm">
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

                            <div class="col-lg-3 col-md-6">
                                <label for="operator">Operator:</label>
                                <select class="form-select form-select-sm" id="operator" name="operator">
                                    <option value="">Select Operator</option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label for="bus">Bus:</label>
                                <select class="form-select form-select-sm" id="bus" name="bus">
                                    <option value="">Select Bus:</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-3 col-md-6">
                                <label for="selStatus">Status:</label>
                                <select class="form-select form-select-sm" id="selStatus" name="selStatus">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-lg-3 d-flex justify-content-end flex-wrap action-btns gap-1 mt-1">
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
                        data-url="{{ route('bus-schedule.dataTableView') }}"
                        data-edit-url="{{ route('bus-schedule.edit', 'ID') }}">
                        <thead class="table-secondary">
                            <tr>
                                <th class="noPrint no-sort">
                                    <div class="checkbox">
                                        <input id="checkboxall" name="btSelectItem" class="chkAll" type="checkbox">
                                    </div>
                                </th>
                                <th>Sl No</th>
                                <th>Opeator</th>
                                <!-- <th>Route</th> -->
                                <th>Bus Name/No</th>
                                <th>Last Modified</th>
                                <th>Status</th>
                                <th class="no-sort">View Schedule</th>
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
                <input type="hidden" id="hdn_model" value="BusSchedule">

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div id="customTableInfo"></div>
                    <div id="customPagination"></div>
                </div>
            </div>
        </div>
        </div>
    </form>

    <div class="modal fade" id="viewScheduleModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bus Schedule Dates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="overflow-y:auto">
                    <div id="viewScheduleContainer"></div>
                </div>
            </div>
        </div>
    </div>

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
            commonAjax.initClearableInputs();
            commonAjax.loadBusOperatorDropdown();

            $('#operator').on('change', function() {
                let operator_id = $(this).val();
                if (!operator_id) return;

                commonAjax.loadBusListByOperator('#bus', operator_id);
            });

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
                operator: operator,
                bus: bus,
            };
            let displayColumns = [1, 2, 3, 4, 5, 6, 7];
            let dataTableColumns = [{
                    data: '',
                    render: function(data, type, row) {
                        return `<div class="checkbox">
                                            <input class="chkItem" type="checkbox" value="${row.bus_schedule_id}">
                                        </div>`;
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
                    data: 'operator_name',
                    defaultContent: "--"
                },
                {
                    data: 'bus_name',
                    defaultContent: "--"
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
                                class="fw-semibold text-decoration-underline cursor-pointer"
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

                        return `
                            <span class="btn btn-sm btn-primary btnViewSchedule"
                                data-id="${row.bus_schedule_id}"
                                data-id="${row.enc_bustype_id}"
                                data-name="${row.layout_name}">
                                <i class="fa fa-calendar"></i> View
                            </span>
                        `;
                    },
                    className: "noPrint text-center"
                },
                {
                    data: '',
                    render: function(data, type, row) {

                        let editUrl = $('#' + tableId).data('edit-url');

                        if (!editUrl) return '';

                        return `
                            <a class="btn btn-sm btn-info"
                                href="${editUrl.replace('ID', row.enc_bus_schedule_id)}">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="javascript:void(0);"
                                class="btn btn-sm btn-success btn-view-log"
                                data-table="bus_schedule"
                                data-id="${row.enc_bus_schedule_id}">
                                <i class="fa fa-history"></i> View Log
                            </a>
                        `;
                    },
                    className: "noPrint text-center"
                }
            ]

            loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
        }

        $(document).on('click', '.btnViewSchedule', function() {

            let id = $(this).data('id');

            // show loader
            $('#viewScheduleContainer').html(`
        <div class="text-center p-4">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2">Loading schedule...</p>
        </div>
     `);

            // open modal
            let modal = new bootstrap.Modal(document.getElementById('viewScheduleModal'));
            modal.show();

            // ajax call
            $.ajax({
                type: "POST",
                url: "/admin/get-schedule-dates",
                data: {
                    bus_schedule_id: id,
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    $('#viewScheduleContainer').html(response);
                },
                error: function() {
                    $('#viewScheduleContainer').html(`
                <div class="text-danger text-center p-4">
                    Failed to load schedule
                </div>
            `);
                }
            });
        });
    </script>
    @endpush