@extends('admin.layouts.master')
@section('page_title', 'Blog Category')
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
        <a href="{{ route('blog-category.add') }}" class="btn btn-success btn-sm">
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

                        <!-- Search -->
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">Search By FAQ</label>
                            <input type="text" class="form-control clearable form-control-sm"
                                id="txtSearch" name="txtSearch"
                                placeholder="FAQ">
                        </div>

                        <!-- Category -->
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select form-select-sm" id="faqCategory" name="faqCategory">
                                <option value="">Select Category</option>
                            </select>
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
                    data-url="{{ route('blog-category.dataTableView') }}"
                    data-edit-url="{{ route('blog-category.edit', 'ID') }}">
                    <thead class="table-secondary">
                        <tr>
                            <th class="noPrint no-sort">
                                <div class="checkbox">
                                    <input id="checkboxall" name="btSelectItem" class="chkAll" type="checkbox">
                                </div>
                            </th>
                            <th>Sl No</th>
                            <th>Banner</th>
                            <th>Category Name</th>
                            <th>Alias</th>
                            <th width="60">Sequence</th>
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
            <input type="hidden" id="hdn_model" value="BlogCategory">

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div id="customTableInfo"></div>
                <div id="customPagination"></div>
            </div>
        </div>
    </div>
    </div>
</form>


<div class="modal fade"
    id="viewCategoryModal"
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
                    <span>Blog Category Details</span>
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
                                <div class="col-lg-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">Category Name</label>
                                    <div id="v_category_name" class="h5 fw-bold text-dark mb-0">--</div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">Alias (Slug)</label>
                                    <div class="bg-light px-3 py-2 rounded border d-inline-flex align-items-center gap-2">
                                        <i class="fa fa-link text-muted small"></i>
                                        <code id="v_slug" class="text-primary fw-medium">--</code>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">Canonical URL</label>
                                    <a id="v_canonical_url" href="#" target="_blank" class="text-decoration-none text-truncate d-block text-primary">--</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Descriptions -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-12">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-3 d-block border-bottom pb-2">Small Description</label>
                                    <div id="v_small_desc" class="text-secondary lh-base">--</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-3 d-block border-bottom pb-2">Full Description</label>
                                    <div id="v_description" class="text-secondary lh-base">--</div>
                                </div>
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
                        <!-- Schema -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-3 d-block border-bottom pb-2">Breadcrumb Schema</label>
                                    <div class="bg-dark text-light p-3 rounded">
                                        <pre class="rounded m-0" style="max-height:400px;overflow:auto;">
                                            <code id="v_breadcrumb_schema" class="language-json"></code>
                                         </pre>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media Previews -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-3 d-block border-bottom pb-2">Media Previews</label>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="small text-muted mb-1 d-block">Banner Image</label>
                                            <div id="v_banner_image" class="image-preview-container bg-white border rounded d-flex align-items-center justify-content-center overflow-hidden" style="height: 120px;">
                                                <span class="text-muted small">No Image</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="small text-muted mb-1 d-block">OG Image</label>
                                            <div id="v_og_image" class="image-preview-container bg-white border rounded d-flex align-items-center justify-content-center overflow-hidden" style="height: 120px;">
                                                <span class="text-muted small">No Image</span>
                                            </div>
                                        </div>
                                    </div>
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
        let faqCategory = '';

        if ($('#txtSearch').val() != '') {
            txtSearch = $('#txtSearch').val();
        }
        if ($('#selStatus').val() != '') {
            selStatus = $('#selStatus').val();
        }

        if ($('#faqCategory').val() != '') {
            faqCategory = $('#faqCategory').val();
        }

        let tableId = 'datatable';
        let orderBy = [2, 'asc'];
        let searchParams = {
            txtSearch: txtSearch,
            selStatus: selStatus,
            faqCategory: faqCategory
        };
        let displayColumns = [1, 2, 3, 4, 5, 6];
        let dataTableColumns = [{
                data: '',
                className: "noPrint text-center",
                render: (d, t, r) =>
                    `<div class="checkbox"><input class="chkItem" type="checkbox" value="${r.blog_cat_id}"></div>`
            },
            {
                data: 'slNo',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center"
            },
            {
                data: 'banner_image',
                defaultContent: "--",
                render: function(data, type, row) {
                    if (data) {
                        return `<img src="/storage/uploads/blog/categories/${data}" width="60" height="40" style="object-fit:cover;">`;
                    }
                    return "--";
                }
            },
            {
                data: 'category_name',
                defaultContent: "--"
            },
            {
                data: 'slug',
                defaultContent: "--"
            },
            {
                data: 'sort_order',
                render: function(data, type, row) {
                    return `<input type="text"
                            value="${data ?? ''}"
                            minlength="1"
                            maxlength="3"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="form-control form-control-sm order-input"
                            data-id="${row.enc_blog_cat_id}"
                            data-table="odbusdev.blog_categories"
                            data-column="sort_order">`;
                },
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

                    <span class="btn btn-sm btn-primary btnViewCategory"
                                data-id="${row.blog_cat_id}">
                                <i class="fa fa-eye"></i> View
                            </span>
                        <a class="btn btn-sm btn-info text-white"
                        href="${editUrl.replace('ID', row.enc_blog_cat_id)}">
                        <i class="fa fa-edit"></i> Edit
                        </a>

                        <a href="javascript:void(0);"
                            class="btn btn-sm btn-success btn-view-log"
                            data-table="blogs"
                            data-id="${row.enc_blog_cat_id}">
                                <i class="fa fa-history"></i> View Log
                        </a>

                    `;
                },
                className: "noPrint text-center"
            }
        ];


        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }

    $(document).ready(function() {

        commonAjax.initSelect2('#faqCategory', 'Select Category');
        commonAjax.loadFaqCategory(0);
    });


    $(document).on('click', '.btnViewCategory', function() {

        let id = $(this).data('id');

        let modal = bootstrap.Modal.getOrCreateInstance(
            document.getElementById('viewCategoryModal')
        );

        modal.show();

        $.ajax({

            url: '/admin/blog-category/details',

            type: 'POST',

            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },

            success: function(res) {

                if (res.status) {

                    let d = res.data;

                    $('#v_category_name').text(d.category_name || '--');
                    $('#v_slug').text(d.slug || '--');
                    $('#v_small_desc').text(d.small_desc || '--');

                    function decodeAndStrip(html) {
                        let txt = document.createElement("textarea");
                        txt.innerHTML = html; // Decode HTML entities

                        let div = document.createElement("div");
                        div.innerHTML = txt.value; // Convert decoded HTML into DOM

                        return div.textContent || div.innerText || "";
                    }

                    $('#v_description').text(
                        d.description ? decodeAndStrip(d.description) : '--'
                    );

                    $('#v_small_desc').text(
                        d.small_desc ? decodeAndStrip(d.small_desc) : '--'
                    );
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

                    $('#v_banner_image').html(
                        d.banner_image ?
                        `<img src="/storage/uploads/blog/categories/${d.banner_image}"
                            class="img-fluid w-100 h-100"
                            style="object-fit:cover;">` :
                        `<span class="text-muted small">No Image</span>`
                    );

                    $('#v_og_image').html(
                        d.og_image ?
                        `<img src="/storage/uploads/blog/categories/${d.og_image}"
                            class="img-fluid w-100 h-100"
                            style="object-fit:cover;">` :
                        `<span class="text-muted small">No Image</span>`
                    );
                }
            }
        });
    });
</script>
@endpush