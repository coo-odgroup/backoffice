@extends('admin.layouts.master')
@section('page_title', 'Blog Author')
@section('content')

<?php
$page_name = 'All ' . trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Blog Management</li>
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
        <a href="{{ route('blog-author.add') }}" class="btn btn-success btn-sm">
            + Add @yield('page_title')
        </a>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate>
    <div class="card">
        <div class="card-body">
            <!-- FILTER -->
            <div class="mb-2 border-bottom d-none" id="filterBox">
                <div class="card-body">
                    <div class="row align-items-end g-2">

                        <!-- Author -->
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Author</label>
                            <input type="text"
                                class="form-control form-control-sm clearable"
                                id="author"
                                name="author"
                                placeholder="Enter Author Name">
                        </div>

                        <!-- Alias -->
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Alias</label>
                            <input type="text"
                                class="form-control form-control-sm clearable"
                                id="authorAlias"
                                name="authorAlias"
                                placeholder="Enter Alias">
                        </div>
                        <!-- Status -->
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select form-select-sm" id="selStatus" name="selStatus">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-lg-4 d-flex justify-content-end flex-wrap action-btns gap-1">
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
                <table class="table table-hover table-bordered align-middle table-sm" id="datatable"
                    data-url="{{ route('blog-author.dataTableView') }}"
                    data-edit-url="{{ route('blog-author.edit', 'ID') }}">
                    <thead class="table-secondary">
                        <tr>
                            <th class="noPrint no-sort">
                                <div class="checkbox">
                                    <input id="checkboxall" name="btSelectItem" class="chkAll" type="checkbox">
                                </div>
                            </th>
                            <th>Sl No</th>
                            <th>Author Name</th>
                            <th>Alias</th>
                            <th>Canonical URL</th>
                            <th>About Author</th>
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
            <input type="hidden" id="hdn_model" value="BlogAuthor">

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div id="customTableInfo"></div>
                <div id="customPagination"></div>
            </div>
        </div>
    </div>
    </div>
</form>


<div class="modal fade"
    id="viewAuthorModal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">

            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="fa fa-eye"></i>
                    <span>Blog Author Details</span>
                </h5>
                <button type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>

            <div class="modal-body p-4 bg-light">
                <div class="container-fluid p-0">

                    <!-- Row 1: Primary Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="row g-4">

                                <div class="col-md-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">
                                        Author Name
                                    </label>
                                    <div id="author_name" class="fw-bold fs-5">--</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">
                                        Alias
                                    </label>

                                    <div class="bg-light rounded border px-3 py-2">
                                        <code id="v_author_slug">--</code>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">
                                        Status
                                    </label>

                                    <div id="v_status">--</div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Descriptions -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">

                            <label class="text-uppercase text-muted fw-bold small mb-3 d-block border-bottom pb-2">
                                About Author
                            </label>

                            <div id="v_about_author" class="text-secondary lh-lg">
                                --
                            </div>

                        </div>
                    </div>

                    <!-- Row 3: SEO & Metadata -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="fa fa-search me-2"></i>SEO & Metadata
                            </h6>
                        </div>

                        <div class="card-body pt-3">
                            <div class="row g-4">

                                <!-- Meta Title -->
                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3 h-100">
                                        <div class="small text-uppercase text-muted fw-semibold mb-2">
                                            <i class="fa fa-heading text-primary me-2"></i>
                                            Meta Title
                                        </div>

                                        <div id="v_meta_title"
                                            class="fw-semibold text-dark fs-6">
                                            --
                                        </div>
                                    </div>
                                </div>

                                <!-- Meta Keywords -->
                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3 h-100">
                                        <div class="small text-uppercase text-muted fw-semibold mb-2">
                                            <i class="fa fa-tags text-success me-2"></i>
                                            Meta Keywords
                                        </div>

                                        <div id="v_meta_keywords">
                                            --
                                        </div>
                                    </div>
                                </div>

                                <!-- Meta Description -->
                                <div class="col-12">
                                    <div class="bg-light rounded-3 p-3">
                                        <div class="small text-uppercase text-muted fw-semibold mb-2">
                                            <i class="fa fa-align-left text-warning me-2"></i>
                                            Meta Description
                                        </div>

                                        <div id="v_meta_description"
                                            class="text-secondary lh-lg">
                                            --
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Technical & Images -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white">
                                    <strong>Author Schema</strong>
                                </div>
                                <div class="card-body">
                                    <pre style="max-height:400px;overflow:auto;">
                                        <code id="v_person_schema"></code>
                                    </pre>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white">
                                    <strong>Breadcrumb Schema</strong>
                                </div>
                                <div class="card-body">

                                    <pre style="max-height:400px;overflow:auto;">
                                        <code id="v_breadcrumb_schema"></code>
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white p-3">
                <button type="button" class="btn btn-outline-secondary px-4 fw-bold" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>
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
        let author = '';
        let authorAlias = '';

        if ($('#txtSearch').val() != '') {
            txtSearch = $('#txtSearch').val();
        }
        if ($('#selStatus').val() != '') {
            selStatus = $('#selStatus').val();
        }

        if ($('#author').val() != '') {
            author = $('#author').val();
        }

        if ($('#authorAlias').val() != '') {
            authorAlias = $('#authorAlias').val();
        }


        let tableId = 'datatable';
        let orderBy = [2, 'asc'];
        let searchParams = {
            txtSearch: txtSearch,
            selStatus: selStatus,
            author: $('#author').val(),
            authorAlias: $('#authorAlias').val(),
        };
        let displayColumns = [1, 2, 3, 4, 5, 6];
        let dataTableColumns = [{
                data: '',
                render: (d, t, r) =>
                    `<div class="checkbox">
            <input class="chkItem" type="checkbox" value="${r.blog_author_id}">
        </div>`
            },
            {
                data: 'slNo',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'author_name',
                defaultContent: '--'
            },
            {
                data: 'author_slug',
                defaultContent: '--'
            },
            {
                data: 'canonical_url',
                defaultContent: '--'
            },
            {
                data: 'about_author',
                defaultContent: '--'
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
                render: function(data) {
                    let cls = data === 'Active' ? 'badge bg-success' : 'badge bg-danger';
                    return `<span class="${cls}">${data}</span>`;
                },
                className: "text-center"
            },
            {
                data: null,
                render: function(data, type, row) {

                    let editUrl = $('#' + tableId).data('edit-url');

                    if (!editUrl) return '';

                    return `

                        <span class="btn btn-sm btn-primary btnViewAuthor"
                            data-id="${row.blog_author_id}">
                            <i class="fa fa-eye"></i>
                        </span>

                        <a class="btn btn-sm btn-info text-white"
                        href="${editUrl.replace('ID', row.enc_blog_author_id)}">
                        <i class="fa fa-edit"></i>
                        </a>

                        <a href="javascript:void(0);"
                            class="btn btn-sm btn-success btn-view-log"
                            data-table="blog_authors"
                            data-id="${row.enc_blog_author_id}">
                                <i class="fa fa-history"></i>
                        </a>

                    `;
                },
                className: "noPrint text-center"
            }
        ];


        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }


    $(document).on('click', '.btnViewAuthor', function() {

        let id = $(this).data('id');

        let modal = bootstrap.Modal.getOrCreateInstance(
            document.getElementById('viewAuthorModal')
        );

        modal.show();

        $.ajax({

            url: '/admin/blog-author/details',

            type: 'POST',

            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },

            success: function(res) {

                if (res.status) {

                    let d = res.data;

                    $('#author_name').text(d.author_name || '--');
                    $('#v_author_slug').text(d.author_slug || '--');
                    $('#v_about_author').html(d.about_author || '--');

                    $('#v_status').html(
                        d.active_status == 1 ?
                        '<span class="badge bg-success">Active</span>' :
                        '<span class="badge bg-danger">Inactive</span>'
                    );

                    function decodeAndStrip(html) {
                        let txt = document.createElement("textarea");
                        txt.innerHTML = html; // Decode HTML entities

                        let div = document.createElement("div");
                        div.innerHTML = txt.value; // Convert decoded HTML into DOM

                        return div.textContent || div.innerText || "";
                    }

                    $('#v_meta_title').text(d.meta_title || '--');
                    $('#v_meta_description').text(d.meta_description || '--');
                    $('#v_canonical_url')
                        .text(d.canonical_url || '--')
                        .attr('href', d.canonical_url || '#');

                    let keywordsHtml = '--';
                    if (d.meta_keywords) {
                        keywordsHtml = '';
                        d.meta_keywords.split(',').forEach(function(keyword) {
                            keywordsHtml += `
                                <span class="badge bg-primary me-1 mb-1">
                                    ${keyword.trim()}
                                </span>
                            `;
                        });
                    }

                    $('#v_meta_keywords').html(keywordsHtml);

                    // Person Schema
                    if (d.person_schema) {

                        let formattedJson = d.person_schema;

                        try {
                            formattedJson = JSON.stringify(
                                JSON.parse(d.person_schema),
                                null,
                                4
                            );
                        } catch (e) {}

                        const code = document.getElementById('v_person_schema');

                        code.textContent = formattedJson;

                        Prism.highlightElement(code);

                    } else {

                        $('#v_person_schema').text('--');

                    }

                    if (d.canonical_url) {
                        $('#v_canonical_url').show();
                    } else {
                        $('#v_canonical_url').removeAttr('href');
                    }

                    if (d.breadcrumb_schema) {

                        let formattedJson = d.breadcrumb_schema;

                        try {
                            formattedJson = JSON.stringify(
                                JSON.parse(d.breadcrumb_schema),
                                null,
                                4
                            );
                        } catch (e) {}

                        const code = document.getElementById('v_breadcrumb_schema');

                        code.textContent = formattedJson;

                        Prism.highlightElement(code);

                    } else {

                        $('#v_breadcrumb_schema').text('--');

                    }
                }
            }
        });
    });
</script>
@endpush