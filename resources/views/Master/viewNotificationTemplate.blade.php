    @extends('admin.layouts.master')
    @section('page_title', 'Notification Template')
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
            <a href="{{ route('notification-template.add') }}" class="btn btn-success btn-sm">
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

                        <!-- Notification Name -->
                        <div class="col-lg-3 col-md-6">
                            <label for="txtSearch">Notification Name</label>
                            <input type="text" class="form-control form-control-sm clearable"
                                id="txtSearch" placeholder="Search Notification" name="txtSearch">
                        </div>

                        <!-- Type -->
                        <div class="col-lg-2 col-md-6">
                            <label for="type">Type</label>
                            <select class="form-select form-select-sm " id="type" name="type">
                                <option value="0">Select Type</option>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="col-lg-2 col-md-6">
                            <label for="category">Category</label>
                            <select class="form-select form-select-sm " id="category" name="category">
                                <option value="0">Select Category</option>
                            </select>
                        </div>

                        <!-- Trigger -->
                        <div class="col-lg-2 col-md-6">
                            <label for="trigger">Event Trigger</label>
                            <select class="form-select form-select-sm " id="trigger" name="trigger">
                                <option value="0">Select Trigger</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-lg-1 col-md-6">
                            <label for="selStatus">Status</label>
                            <select class="form-select form-select-sm" id="selStatus" name="selStatus">
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-lg-2 col-md-12 d-flex justify-content-end gap-1">
                            <button class="btn btn-primary btn-sm" type="button" onclick="getDataTableView()">
                                <i class="fa-solid fa-search me-1"></i>Search
                            </button>

                            <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                <i class="fa-solid fa-rotate-left me-1"></i>Reset
                            </button>
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
                            <button type="button" id="btnDelete" class="btn btn-warning btn-sm " onclick="actionRec('D');">
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
                        data-url="{{ route('notification-template.dataTableView') }}"
                        data-edit-url="{{ route('notification-template.edit', 'ID') }}">
                        <thead class="table-secondary">
                            <tr>
                                <th class="noPrint no-sort">
                                    <div class="checkbox">
                                        <input id="checkboxall" name="btSelectItem" class="chkAll" type="checkbox">
                                    </div>
                                </th>
                                <th>Sl No</th>
                                <th>Notification Name</th>
                                <th>Notification Type</th>
                                <th>Notification Category</th>
                                <th>Notification Trigger</th>
                                <th>Last Modefied</th>
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
                <input type="hidden" id="hdn_model" value="NotificationTemplate">

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div id="customTableInfo"></div>
                    <div id="customPagination"></div>
                </div>
            </div>
        </div>
        </div>
    </form>


    <div class="modal fade" id="viewDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg  modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Notification Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="modalContent">
                    <div class="text-center p-3">Loading...</div>
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

        $(document).ready(function() {


            commonAjax.initClearableInputs();
            commonAjax.initTableCheckbox('#checkboxall', '.chkItem');
            commonAjax.initSelect2('#type', 'Select Type');
            commonAjax.initSelect2('#category', 'Select Category');
            commonAjax.initSelect2('#trigger', 'Select Event Trigger');

            commonAjax.loadAnnextureList([
                'NOTIFICATION_TYPE',
                'NOTIFICATION_TEMPLATE_CATEGORY',
                'NOTIFICATION_EVENT_TRIGGER'
            ], function(data) {

                renderDropdown(
                    '#type',
                    data.NOTIFICATION_TYPE || []
                );

                renderDropdown(
                    '#category',
                    data.NOTIFICATION_TEMPLATE_CATEGORY || []
                );

                renderDropdown(
                    '#trigger',
                    data.NOTIFICATION_EVENT_TRIGGER || []
                );

                getDataTableView();
            });

        });



        function renderDropdown(selector, items = [], selected = 0) {

            let options = '<option value="0">Select Option</option>';

            $.each(items, function(index, item) {

                let isSelected =
                    selected == item.annexture_value ?
                    'selected' :
                    '';

                options += `
                <option value="${item.annexture_value}" ${isSelected}>
                    ${item.annexture_name}
                </option>
            `;
            });

            $(selector).html(options).trigger('change');
        }


        window.getDataTableView = function(reset = true) {

            let tableSelector = '#datatable';

            if ($.fn.DataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().clear().destroy();
                $(tableSelector + ' tbody').empty();
            }

            if (window.dataTableInstance && reset) {
                window.dataTableInstance.state.clear();
                $('#pageSizeDatatable').val(10);
                window.dataTableInstance.page.len(10);
                window.dataTableInstance.page(0);
            }

            $('#pageSizeDatatable').val(10);
            let tableId = 'datatable';
            let orderBy = [2, 'asc'];

            let searchParams = {
                txtSearch: $('#txtSearch').val() || '',
                selStatus: $('#selStatus').val() || '',
                type: $('#type').val() || 0,
                category: $('#category').val() || 0,
                trigger: $('#trigger').val() || 0
            };

            let displayColumns = [1, 2, 3, 4, 5, 6, 7];
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
                    data: 'notification_name',
                    defaultContent: "--"
                },
                {
                    data: 'notification_type',
                    defaultContent: "--"
                },
                {
                    data: 'notification_category',
                    defaultContent: "--"
                },
                {
                    data: 'notification_trigger',
                    defaultContent: "--"
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
                            <span class="fw-semibold text-decoration-underline cursor-pointer"
                                data-bs-toggle="tooltip"
                                data-bs-html="true"
                                title="
                                    <div>
                                        <div><strong>Created By:</strong> ${createdBy}</div>
                                        <div><strong>Created At:</strong> ${createdAt}</div>
                                        <hr>
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
                        let cls = row.is_active === 'Active' ? 'badge bg-success' : 'badge bg-danger';
                        return `<span class="${cls}">${row.is_active}</span>`;
                    },
                    className: "text-center"
                },
                {
                    data: '',
                    render: function(data, type, row) {

                        let editUrl = $('#datatable').data('edit-url');

                        return `
                          <button class="btn btn-sm btn-primary viewDetailsBtn"
                                data-id="${row.id}">
                                <i class="fa fa-eye"></i>
                            </button>

                            <a class="btn btn-sm btn-info"
                                href="${editUrl.replace('ID', row.enc_id)}">
                                <i class="fa fa-edit"></i>
                            </a>

                            <a href="javascript:void(0);"
                            class="btn btn-sm btn-success btn-view-log"
                            data-table="mst_notification_templates"
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


        $(document).on('click', '.btnViewSchedule', function() {

            let id = $(this).data('id');
            $('#viewScheduleContainer').html(`
                <div class="text-center p-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Loading schedule...</p>
                </div>
            `);

            let modal = new bootstrap.Modal(document.getElementById('viewScheduleModal'));
            modal.show();

            $.ajax({
                type: "POST",
                url: "/admin/get-schedule-dates",
                data: {
                    bus_schedule_id: id,
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {

                    $('#viewScheduleContainer').html(response);

                    let busTitle = $('#viewScheduleContainer').find('#modalBusTitle').text();

                    if (busTitle !== '') {
                        $('#modalBusHeading').text(busTitle);
                    }
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

        $(document).on('click', '.viewDetailsBtn', function() {

            let id = $(this).data('id');

            $('#modalContent').html('Loading...');

            let modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
            modal.show();

            $.ajax({
                url: "{{ url('admin/get-notification-details') }}",
                type: 'POST',
                data: {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#modalContent').html(response);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    $('#modalContent').html(`
                <div class="text-danger text-center">
                    Failed to load
                </div>
            `);
                }
            });

        });
    </script>
    @endpush