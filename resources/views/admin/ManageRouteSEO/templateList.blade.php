@extends('admin.layouts.master')
@section('page_title', 'Template List')
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
                    data-url="{{ route('template-list.dataTableView') }}">
                    <thead class="table-secondary">
                        <tr>
                            <th>Sl No</th>
                            <th>Routes</th>
                            <th>Action</th>
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
            <input type="hidden" id="hdn_model" value="TemplateList">

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div id="customTableInfo"></div>
                <div id="customPagination"></div>
            </div>
        </div>
    </div>
</form>


<div class="modal fade"
    id="viewTemplateModal"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa fa-eye me-2"></i>
                    Route SEO Details
                </h5>
                <button class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body bg-light">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header">
                                <strong>Route Content</strong>
                            </div>
                            <div class="card-body">
                                <div id="v_content"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header">
                                Meta Title
                            </div>
                            <div class="card-body">
                                <div id="v_meta_title"></div>
                            </div>
                        </div>
                        <div class="card shadow-sm mb-3">
                            <div class="card-header">
                                Meta Description
                            </div>
                            <div class="card-body">
                                <div id="v_meta_description"></div>
                            </div>
                        </div>
                        <div class="card shadow-sm mb-3">
                            <div class="card-header">
                                <strong>Breadcrumb Schema</strong>
                            </div>
                            <div class="card-body">
                                <div id="v_breadcrumb_schema" class="json-viewer"></div>
                            </div>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-header">
                                <strong>FAQ Schema</strong>
                            </div>
                            <div class="card-body">
                                <div id="v_faq_schema" class="json-viewer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    /* ===========================
   Route SEO View Modal
=========================== */

    #viewTemplateModal .modal-content {
        border: 0;
        border-radius: 16px;
        overflow: hidden;
        background: #f5f7fb;
    }

    #viewTemplateModal .modal-header {
        background: linear-gradient(90deg, #0f2b6d, #2f80ed);
        padding: 16px 24px;
        border-bottom: none;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    #viewTemplateModal .modal-title {
        font-size: 20px;
        font-weight: 700;
    }

    #viewTemplateModal .modal-body {
        padding: 25px;
    }

    /* Cards */

    #viewTemplateModal .card {
        border: none;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
    }

    #viewTemplateModal .card-header {
        background: #ffffff;
        border-bottom: 1px solid #e9ecef;
        font-size: 15px;
        font-weight: 700;
        color: #0f2b6d;
        padding: 14px 18px;
    }

    #viewTemplateModal .card-body {
        padding: 18px;
    }

    /* Route Content */

    #v_content {
        min-height: 600px;
        max-height: 75vh;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 20px;
        line-height: 1.8;
        color: #444;
    }

    #v_content h1,
    #v_content h2,
    #v_content h3,
    #v_content h4 {
        color: #0f2b6d;
        margin-bottom: 12px;
        font-weight: 700;
    }

    #v_content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    #v_content table {
        width: 100%;
        border-collapse: collapse;
    }

    #v_content table th,
    #v_content table td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    /* Meta Data */

    #v_meta_title {
        font-size: 16px;
        font-weight: 600;
        color: #0f2b6d;
        word-break: break-word;
    }

    #v_meta_description {
        color: #555;
        line-height: 1.8;
        white-space: pre-wrap;
        word-break: break-word;
    }

    /* Schema Blocks */

    #viewTemplateModal pre {
        margin: 0;
        background: #1e293b;
        color: #e2e8f0;
        border-radius: 10px;
        padding: 16px;
        max-height: 350px;
        overflow: auto;
        font-size: 13px;
        line-height: 1.6;
    }

    #viewTemplateModal code {
        color: inherit;
        font-family: Consolas, Monaco, monospace;
    }

    /* Scrollbar */

    #viewTemplateModal ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    #viewTemplateModal ::-webkit-scrollbar-thumb {
        background: #b5b5b5;
        border-radius: 20px;
    }

    #viewTemplateModal ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* Responsive */

    @media(max-width:992px) {

        #viewTemplateModal .modal-body {
            padding: 15px;
        }

        #v_content {
            min-height: 350px;
        }

    }

    .json-viewer {
        height: 550px;
        overflow: auto;
        border-radius: 10px;
        background: #1e293b;
        border: 1px solid #334155;
        padding: 18px;
        font-size: 14px;
        line-height: 1.7;
    }

    .json-viewer::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .json-viewer::-webkit-scrollbar-thumb {
        background: #64748b;
        border-radius: 20px;
    }
</style>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsoneditor@10.1.3/dist/jsoneditor.min.css">
<script src="https://cdn.jsdelivr.net/npm/jsoneditor@10.1.3/dist/jsoneditor.min.js"></script>
<script type="module">
    $('#backoffice-form').on('submit', function(e) {
        e.preventDefault();
    });

    $(document).ready(function() {
        commonAjax.loadRouteList();
        commonAjax.initSelect2('#route_id', 'Select Route');
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
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {

                    return `
            <button type="button"
                class="btn btn-sm btn-primary btnViewTemplate"
                data-id="${row.enc_id}"
                title="View">
                <i class="fa fa-eye"></i>
            </button>
        `;
                },
                className: "text-center"
            }
        ];

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    };

    $(document).on('click', '.btnViewTemplate', function() {

        let enc_id = $(this).data('id');

        $.ajax({
            url: "{{ route('manage-template.view') }}",
            type: "POST",
            data: {
                enc_id: enc_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {

                if (res.status) {

                    let d = res.data;

                    $('#v_content').html(d.content ?? '');
                    $('#v_meta_title').text(d.meta_title ?? '--');
                    $('#v_meta_description').text(d.meta_description ?? '--');
                    renderJson('v_breadcrumb_schema', d.breadcrumb_schema);
                    renderJson('v_faq_schema', d.faq_schema);

                    const modal = new bootstrap.Modal(
                        document.getElementById('viewTemplateModal')
                    );

                    modal.show();
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });

    });

    function renderJson(id, jsonString) {

        const container = document.getElementById(id);
        container.innerHTML = '';

        try {

            let json = jsonString;

            if (typeof json === 'string') {
                json = json.replace(/^"+|"+$/g, '');
                json = json.replace(/\\"/g, '"');
                json = json.replace(/\\n/g, '');
                json = JSON.parse(json);
            }

            const editor = new JSONEditor(container, {
                mode: 'code', // Show formatted JSON text
                modes: ['code'], // Disable tree/text switching
                navigationBar: false,
                statusBar: false,
                mainMenuBar: false
            });

            editor.set(json);

        } catch (e) {

            container.innerHTML =
                '<pre style="white-space:pre-wrap;">' +
                jsonString +
                '</pre>';
        }
    }
</script>
@endpush