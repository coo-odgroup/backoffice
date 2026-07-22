@extends('admin.layouts.master')
@section('page_title', 'City Content Management')
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
        <li class="breadcrumb-item">City Content Management</li>
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
            <div class="mb-3 border-bottom d-none" id="filterBox">
                <div class="card-body">
                    <div class="row align-items-end g-2">

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Search</label>
                            <input type="text"
                                class="form-control form-control-sm clearable"
                                id="search"
                                name="search"
                                placeholder="Enter City / Content ">
                        </div>
                        <div class="col-lg-2 d-flex justify-content-end flex-wrap action-btns gap-1 mt-2">
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
                    data-url="{{ route('manage-city-content.dataTableView') }}">
                    <thead class="table-secondary">
                        <tr>
                            <th>Sl No</th>
                            <th>City</th>
                            <th>Content</th>
                            <th>Last Modified</th>
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
            <input type="hidden" id="hdn_model" value="ManageCityContent">

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div id="customTableInfo"></div>
                <div id="customPagination"></div>
            </div>
        </div>
    </div>
    </div>
</form>

<!-- <style>
    #datatable {
        table-layout: fixed;
        width: 100%;
    }

    #datatable th,
    #datatable td {
        vertical-align: top;
    }

    .content-edit-box {
        width: 100%;
    }

    .content-view {
        min-height: 58px;
        max-height: 90px;
        overflow-y: auto;
        white-space: normal;
        word-break: break-word;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
    }

    .content-view:hover {
        background: #f8f9fa;
    }

    .edit-content-textarea {
        min-height: 100px;
        resize: vertical;
    }

    #datatable thead th {
        text-align: center !important;
        vertical-align: middle !important;
    }
</style> -->

@endsection
@push('scripts')
<script type="module">
    $(document).ready(function() {


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


        let tableId = 'datatable';
        let orderBy = [0, 'asc'];
        let searchParams = {
            search: $('#search').val(),
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
                data: 'city_name',
                defaultContent: "--"
            },
            {
                data: 'content',
                width: '63%',
                render: function(data, type, row) {
                    if (!data) data = "";

                    let safeText = $('<div>').text(data).html();

                    return `
                            <div class="content-edit-box" data-id="${row.enc_id}">
                                <div class="content-view content-cell" title="Click to edit content">
                                    ${safeText}
                                </div>

                                <div class="content-edit d-none">
                                    <textarea class="form-control form-control-sm edit-content-textarea" rows="4">${data}</textarea>
                                    <div class="mt-2 d-flex gap-2">
                                        <button type="button" class="btn btn-success btn-sm btn-save-content" data-id="${row.enc_id}">
                                            Update
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm btn-cancel-content">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                }
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
                    data-table="mst_city_content"
                    data-id="${row.enc_id}">
                        <i class="fa fa-history"></i>
                    </a>
                `;
                },
                className: "noPrint text-center"
            }
        ];

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }

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

    // save content
    $(document).on('click', '.btn-save-content', function() {
        let btn = $(this);
        let parent = btn.closest('.content-edit-box');
        let encId = btn.data('id');
        let content = parent.find('.edit-content-textarea').val().trim();

        if (content === '') {
            commonAjax.viewAlert('Content is required.');
            return;
        }

        btn.prop('disabled', true).text('Updating...');

        $.ajax({
            url: "{{ route('manage-city-content.updateContent') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                enc_id: encId,
                content: content
            },
            success: function(response) {
                if (response.status) {
                    parent.find('.content-view').html($('<div>').text(content).html());
                    parent.find('.content-edit').addClass('d-none');
                    parent.find('.content-view').removeClass('d-none');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        commonAjax.viewAlert(response.message);
                    }

                    getDataTableView(false);
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', response.message, 'error');
                    } else {
                        commonAjax.viewAlert(response.message);
                    }
                }
            },
            error: function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Failed to update content.', 'error');
                } else {
                    commonAjax.viewAlert('Failed to update content.');
                }
            },
            complete: function() {
                btn.prop('disabled', false).text('Update');
            }
        });
    });

    $(document).on('click', '.content-cell', function() {
        let preview = $(this).find('.content-preview');
        let full = $(this).find('.content-full-scroll');

        if (full.hasClass('d-none')) {
            preview.addClass('d-none');
            full.removeClass('d-none');
        } else {
            full.addClass('d-none');
            preview.removeClass('d-none');
        }
    });
</script>
@endpush