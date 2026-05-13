    @extends('admin.layouts.master')
    @section('page_title', 'Notification Rules')
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
            <a href="{{ route('notification-rules.add') }}" class="btn btn-success btn-sm">
                + Add @yield('page_title')
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <form id="backoffice-form" name="backoffice-form" method="post" novalidate>
        <div class="card">
            <div class="card-body">
                <!-- FILTER -->
                <div class="mb-3 pb-4 border-bottom d-none" id="filterBox">
                    <div class="row align-items-end">
                        <div class="row align-items-end">

                            <div class="col-lg-2 col-md-6">
                                <label for="cronName">
                                    Cron Name
                                </label>

                                <input type="text"
                                    class="form-control form-control-sm clearable"
                                    id="cronName"
                                    name="cronName"
                                    placeholder="Enter Cron Name">
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <label for="channel">
                                    Channel
                                </label>

                                <select class="form-select form-select-sm"
                                    id="channel"
                                    name="channel">

                                    <option value="">
                                        Select Channel
                                    </option>

                                    <option value="1">Email</option>
                                    <option value="2">SMS</option>
                                    <option value="3">Push Notification</option>
                                    <option value="4">WhatsApp</option>

                                </select>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <label for="recipient">
                                    Recipient Type
                                </label>

                                <select class="form-select form-select-sm"
                                    id="recipient"
                                    name="recipient">

                                    <option value="">
                                        Select Recipient Type
                                    </option>

                                    <option value="1">Manual</option>
                                    <option value="2">Role Based</option>
                                    <option value="3">Dynamic Variable</option>

                                </select>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <label for="condition">
                                    Condition Type
                                </label>

                                <select class="form-select form-select-sm"
                                    id="condition"
                                    name="condition">

                                    <option value="">
                                        Select Condition
                                    </option>

                                    <option value="Always">Always</option>
                                    <option value="Success">Success</option>
                                    <option value="Failed">Failed</option>
                                    <option value="Inactive">Inactive</option>

                                </select>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <label for="selStatus">
                                    Status
                                </label>

                                <select class="form-select form-select-sm"
                                    id="selStatus"
                                    name="selStatus">

                                    <option value="">
                                        Select Status
                                    </option>

                                    <option value="1">
                                        Active
                                    </option>

                                    <option value="0">
                                        Inactive
                                    </option>

                                </select>
                            </div>

                            <div class="col-lg-2 col-md-12 d-flex justify-content-end flex-wrap action-btns gap-1">

                                <button class="btn btn-primary btn-sm"
                                    type="button"
                                    onclick="getDataTableView()">

                                    <i class="fa-solid fa-search me-1"></i>
                                    Search
                                </button>

                                <button class="btn btn-secondary btn-sm"
                                    id="btnReset"
                                    type="button">

                                    <i class="fa-solid fa-rotate-left me-1"></i>
                                    Reset
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
                            <button type="button" id="btnDelete" class="btn btn-warning btn-sm d-none"
                                onclick="actionRec('D');">
                                <i class="fa-solid fa-trash me-1"></i>
                                Delete
                            </button>
                            <button type="button" id="btnActive" class="btn btn-success btn-sm text-white"
                                onclick="actionRec('A');">
                                <i class="fa-solid fa-circle-check me-1"></i>
                                Active
                            </button>
                            <button type="button" id="btnInactive" class="btn btn-danger btn-sm"
                                onclick="actionRec('UN');">
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
                    <table class="table table-hover table-bordered align-middle table-sm table-responsive"
                        id="datatable" data-url="{{ route('notification-rules.dataTableView') }}"
                        data-edit-url="{{ route('notification-rules.edit', 'ID') }}">
                        <thead class="table-secondary">
                            <tr>
                                <th class="noPrint no-sort">
                                    <div class="checkbox">
                                        <input id="checkboxall" name="btSelectItem" class="chkAll" type="checkbox">
                                    </div>
                                </th>
                                <th>Sl No</th>
                                <th>Cron Job Name</th>
                                <th>Channel</th>
                                <th>Recipient Type</th>
                                <th>Recipient Value</th>
                                <th>Template</th>
                                <th>Role</th>
                                <th>Condition</th>
                                <th>Last Modefied</th>
                                <th> Status</th>
                                <th class="no-sort">View Cron Details</th>
                                <th class="no-sort" width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="footer-background border-success text-center" id="norecord" style="display:none">No
                    record found.</div>
                {{ csrf_field() }}
                <input name="hdn_ids" id="hdn_ids" type="hidden">
                <input name="hdn_qs" id="hdn_qs" type="hidden">
                <input type="hidden" id="hdn_model" value="NotificationRules">

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div id="customTableInfo"></div>
                    <div id="customPagination"></div>
                </div>
            </div>
        </div>
        </div>
    </form>

    <div class="modal fade"
        id="viewScheduleModal"
        tabindex="-1"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        aria-hidden="true">

        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <!-- HEADER -->
                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="fa fa-bell me-2"></i>
                            Cron Job Details
                        </h5>
                    </div>
                    <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <!-- BODY -->
                <div class="modal-body bg-light">
                    <!-- CRON SUMMARY -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Cron Name</th>
                                            <th>Cron Type</th>
                                            <th>Execution Type</th>
                                            <th>Schedule Type</th>
                                            <th>Scheduled Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="summaryCronNameModal">--</td>
                                            <td id="summaryCronTypeModal">--</td>
                                            <td id="summaryExecutionTypeModal">--</td>
                                            <td id="summaryScheduleTypeModal">--</td>
                                            <td id="summarySchedulerModal">--</td>
                                            <td id="summaryStatusModal">--</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-white">
                    <button type="button"
                        class="btn btn-secondary btn-sm"
                        data-bs-dismiss="modal">
                        Close
                    </button>
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

        $('#btnReset').click(function() {
            $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
            $('.form-select').val(0);
            $('.form-select').val('').trigger('change');
            getDataTableView(true);
        });




        function getDataTableView(reset = true) {
            if (window.dataTableInstance && reset) {

                window.dataTableInstance.state.clear();

                $('#pageSizeDatatable').val(10);

                window.dataTableInstance.page.len(10);

                window.dataTableInstance.page(0);
            }

            $('#pageSizeDatatable').val(10);

            let selStatus = '';
            let txtSearch =
                $('#cronName').val().trim();
            if ($('#selStatus').val() != '') {
                selStatus = $('#selStatus').val();
            }


            let tableId = 'datatable';
            let orderBy = [1, 'asc'];

            let searchParams = {
                cronName: $('#cronName').val() || '',
                selStatus: $('#selStatus').val() || '',
                channel: $('#channel').val() || '',
                recipient: $('#recipient').val() || '',
                condition: $('#condition').val() || ''
            };

            let displayColumns = [1, 2, 3, 4, 5, 6, 7, 8, 9];
            let dataTableColumns = [
            {
                    data: '',
                    render: function(data, type, row) {
                        return `<div class="checkbox">
                            <input class="chkItem"
                                type="checkbox"
                                id="check${row.id}"
                                name="chk${row.id}"
                                value="${row.id}">
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
                    data: 'cron_name',
                    defaultContent: "--"
                },
                {
                    data: 'channel',
                    defaultContent: "--"
                },
                {
                    data: 'recipient_type',
                    defaultContent: "--"
                },
                {
                    data: 'recipient_value',
                    defaultContent: "--"

                },
                {
                    data: 'template_id',
                    defaultContent: "--"

                },
                {
                    data: 'role_type',
                    defaultContent: "--"

                }, {
                    data: 'status_condition',
                    defaultContent: "--"

                },
                {
                    data: null,
                    render: function(data, type, row) {

                        let createdBy = row.created_by_name ?? '--';
                        let createdAt = row.created_date ?? '--';
                        let updatedBy = row.updated_by_name ? row.updated_by_name : '--';
                        let updatedAt = (row.updated_date) ? row.updated_date : '--';
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
                                data-id="${row.id}"
                                data-name="${row.cron_name}">
                                <i class="fa fa-eye"></i> View
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
                                href="${editUrl.replace('ID', row.enc_id)}">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                           <a href="javascript:void(0);"
                            class="btn btn-sm btn-success btn-view-log"
                            data-table="cron_job_notifications"
                            data-id="${row.enc_id}">
                            <i class="fa fa-history"></i> View Log
                           </a>
                        `;
                    },
                    className: "noPrint text-center"
                }
            ]
            if ($.fn.DataTable.isDataTable('#datatable')) {

                $('#datatable')
                    .DataTable()
                    .destroy();
            }

            $('#datatable tbody').html('');

            loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);

        }

        window.getDataTableView = getDataTableView;
        $(document).on('click', '.btnViewSchedule', function() {

            let id = $(this).data('id');

            // open modal
            let modalEl = document.getElementById('viewScheduleModal');

            let modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            $('#summaryCronNameModal').html('--');
            $('#summaryCronTypeModal').html('--');
            $('#summaryExecutionTypeModal').html('--');
            $('#summaryScheduleTypeModal').html('--');
            $('#summarySchedulerModal').html('--');
            $('#summaryStatusModal').html('--');

            modal.show();

            $.ajax({

                type: "POST",

                url: "/admin/get-cron-summary",

                data: {

                    cron_id: id,

                    _token: $('meta[name="csrf-token"]')
                        .attr("content")
                },

                success: function(response) {

                    console.log(response);

                    if (
                        response.status == true &&
                        response.data
                    ) {

                        let row = response.data;

                        $('#summaryCronNameModal').html(
                            row.name ?? '--'
                        );

                        $('#summaryCronTypeModal').html(
                            row.cron_type ?? '--'
                        );

                        $('#summaryExecutionTypeModal').html(
                            row.execution_type ?? '--'
                        );

                        $('#summaryScheduleTypeModal').html(
                            row.schedule_type ?? '--'
                        );

                        $('#summarySchedulerModal').html(
                            row.scheduler ?? '--'
                        );

                        $('#summaryStatusModal').html(
                            row.active_status == 1 ?
                            `<span class="badge bg-success">
                                Active
                            </span>` :
                            `<span class="badge bg-danger">
                                Inactive
                            </span>`
                        );
                    }
                },

                error: function() {
                    $('#summaryCronNameModal').html('--');
                    $('#summaryCronTypeModal').html('--');
                    $('#summaryExecutionTypeModal').html('--');
                    $('#summaryScheduleTypeModal').html('--');
                    $('#summarySchedulerModal').html('--');
                    $('#summaryStatusModal').html(`
                        <span class="badge bg-danger">
                            Failed
                        </span>
                    `);
                }
            });

        });
        $(document).ready(function() {
        commonAjax.initClearableInputs();
        commonAjax.initTableCheckbox('#checkboxall', '.chkItem');
            getDataTableView();

        });
    </script>
    @endpush