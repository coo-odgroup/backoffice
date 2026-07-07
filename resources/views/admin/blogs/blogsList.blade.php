@extends('admin.layouts.master')
@section('page_title', 'Blogs')
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
        <a href="{{ route('blogs.add') }}" class="btn btn-success btn-sm">
            + Add @yield('page_title')
        </a>
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
                            <label class="form-label">Title</label>
                            <input type="text"
                                class="form-control form-control-sm clearable"
                                id="title"
                                name="title"
                                placeholder="Enter Blog Title">
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Author</label>
                            <select class="form-select form-select-sm" id="blogAuthor" name="author_id">
                                <option value="">Select Author</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select form-select-sm" id="blogCategory" name="category_id">
                                <option value="">Select Category</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select form-select-sm" id="selStatus" name="selStatus">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
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
                        <button type="button" id="btnDelete" class="btn btn-warning btn-sm btn-mob d-none" onclick="actionRec('D');">
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
                    data-url="{{ route('blogs.dataTableView') }}"
                    data-edit-url="{{ route('blogs.edit', 'ID') }}">
                    <thead class="table-secondary">
                        <tr>
                            <th class="noPrint no-sort">
                                <div class="checkbox">
                                    <input id="checkboxall" name="btSelectItem" class="chkAll" type="checkbox">
                                </div>
                            </th>
                            <th>Sl No</th>
                            <th>Category Name</th>
                            <th>Title</th>
                            <th width="450">Allias</th>
                            <th>Author</th>
                            <th>Published Date</th>
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
            <input type="hidden" id="hdn_model" value="Blog">

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div id="customTableInfo"></div>
                <div id="customPagination"></div>
            </div>
        </div>
    </div>
    </div>
</form>

<div class="modal fade"
    id="viewBlogModal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="fa fa-eye"></i>
                    <span>Blog Details</span>
                </h5>
                <button type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>

            <div class="modal-body p-4 bg-light">
                <div class="container-fluid p-0">

                    <!-- Basic Details -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="row g-4">

                                <div class="col-md-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">Title</label>
                                    <div id="v_title" class="fw-bold fs-5">--</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">Alias</label>
                                    <div class="bg-light rounded border px-3 py-2">
                                        <code id="v_slug">--</code>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">Status</label>
                                    <div id="v_status">--</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">Category</label>
                                    <div id="v_category">--</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">Author</label>
                                    <div id="v_author">--</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">Published At</label>
                                    <div id="v_published_at">--</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">Is Featured</label>
                                    <div id="v_is_featured">--</div>
                                </div>

                                <div class="col-md-8">
                                    <label class="text-uppercase text-muted fw-bold small mb-2 d-block">Canonical URL</label>
                                    <a href="javascript:void(0)" id="v_canonical_url" target="_blank">--</a>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Descriptions -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <label class="text-uppercase text-muted fw-bold small mb-3 d-block border-bottom pb-2">
                                Short Description
                            </label>
                            <div id="v_short_description" class="text-secondary lh-lg mb-4">--</div>

                            <label class="text-uppercase text-muted fw-bold small mb-3 d-block border-bottom pb-2">
                                Content
                            </label>
                            <div id="v_content" class="text-secondary lh-lg">--</div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="fa fa-image me-2"></i>Images
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-4">

                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3 h-100">
                                        <div class="small text-uppercase text-muted fw-semibold mb-2">Thumb Image</div>
                                        <div class="mb-2"><strong>Alt:</strong> <span id="v_thumb_alt_text">--</span></div>
                                        <img id="v_thumb_image" src="" class="img-fluid rounded border d-none">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3 h-100">
                                        <div class="small text-uppercase text-muted fw-semibold mb-2">Featured Image</div>
                                        <div class="mb-2"><strong>Alt:</strong> <span id="v_feature_alt_text">--</span></div>
                                        <img id="v_featured_image" src="" class="img-fluid rounded border d-none">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="fa fa-search me-2"></i>SEO & Metadata
                            </h6>
                        </div>

                        <div class="card-body pt-3">
                            <div class="row g-4">

                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3 h-100">
                                        <div class="small text-uppercase text-muted fw-semibold mb-2">Meta Title</div>
                                        <div id="v_meta_title">--</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3 h-100">
                                        <div class="small text-uppercase text-muted fw-semibold mb-2">Meta Keywords</div>
                                        <div id="v_meta_keywords">--</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="bg-light rounded-3 p-3">
                                        <div class="small text-uppercase text-muted fw-semibold mb-2">Meta Description</div>
                                        <div id="v_meta_description">--</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Schema -->
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white"><strong>Breadcrumb Schema</strong></div>
                                <div class="card-body">
                                    <pre style="max-height:350px;overflow:auto;"><code id="v_breadcrumb_schema"></code></pre>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white"><strong>FAQ Schema</strong></div>
                                <div class="card-body">
                                    <pre style="max-height:350px;overflow:auto;"><code id="v_faq_schema"></code></pre>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white"><strong>Service Schema</strong></div>
                                <div class="card-body">
                                    <pre style="max-height:350px;overflow:auto;"><code id="v_service_schema"></code></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer bg-white p-3">
                <button type="button" class="btn btn-outline-secondary px-4 fw-bold" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

@endsection
@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>

<script type="module">
    window.bulkActionUrl = "{{ route('admin.bulkAction') }}";

    $('#backoffice-form').on('submit', function(e) {
        e.preventDefault();
    });

    $(document).ready(function() {

        let author_id = 0;
        let category_id = 0;
        commonAjax.initClearableInputs();
        commonAjax.initTableCheckbox('#checkboxall', '.chkItem');
        commonAjax.initSelect2('#author_id', 'Select Author');
        commonAjax.initSelect2('#category_id', 'Select Blog Category');
        commonAjax.initSelect2('#selStatus', 'Select Status');
        commonAjax.loadBlogAuthorList(author_id);
        commonAjax.loadBlogCategoryList(category_id);
        getDataTableView();
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
           $('#selStatus').val('').trigger('change');
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

        if ($('#selStatus').val() != '') {
            selStatus = $('#selStatus').val();
        }

        let tableId = 'datatable';
        let orderBy = [2, 'asc'];
        let searchParams = {
            title: $('#title').val(),
            slug: $('#slug').val(),
            author_id: $('#blogAuthor').val(),
            category_id: $('#blogCategory').val(),
            selstatus: $('#selStatus').val()
        };
        let displayColumns = [1, 2, 3, 4, 5, 6];
        let dataTableColumns = [{
                data: '',
                className: "noPrint text-center",
                render: (d, t, r) =>
                    `<div class="checkbox"><input class="chkItem" type="checkbox" value="${r.blog_id}"></div>`
            },
            {
                data: 'slNo',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center"
            },
            {
                data: 'category_name',
                defaultContent: "--"
            },
            {
                data: 'title',
                render: function(data, type, row) {
                    if (!data) return "--";

                    return data.length > 30 ?
                        data.substring(0, 30) + '...' :
                        data;
                }
            },
            {
                data: 'slug',
                render: function(data, type, row) {
                    if (!data) return "--";

                    return data.length > 30 ?
                        data.substring(0, 30) + '...' :
                        data;
                },
                className: 'slug-column'
            },
            {
                data: 'author_name',
                defaultContent: "--"
            },
            {
                data: 'published_at',
                render: function(data, type, row) {

                    if (!data) return "--";

                    let dateObj = new Date(data);

                    let formattedDate = dateObj.toLocaleString('en-GB', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    let createdBy = row.created_by_name ?? '--';
                    let updatedBy = row.updated_by_name ?? '--';

                    return `
            <span
                class="text-decoration-underline fw-semibold"
                data-bs-toggle="tooltip"
                data-bs-html="true"
                title="
                    <div class='audit-box'>
                        <div><strong>Published On:</strong> ${formattedDate}</div>
                        <hr class='my-1'>
                        <div><strong>Created By:</strong> ${createdBy}</div>
                        <div><strong>Updated By:</strong> ${updatedBy}</div>
                    </div>
                ">
                ${formattedDate}
            </span>
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
                render: function(data, type, row) {
                    var cls = ((row.is_active == 'Active') ? 'badge bg-success' : 'badge bg-danger');
                    return '<span class="' + cls + '">' + row.is_active + '</span>';
                },
                className: "text-center"
            },
            {
                data: '',
                render: function(data, type, row) {

                    let editUrl = $('#' + tableId).data('edit-url');

                    if (!editUrl) return '';

                    return `
                        <span class="btn btn-sm btn-primary btnViewBlog"
                            data-id="${row.blog_id}">
                            <i class="fa fa-eye"></i> View
                        </span>

                        <a class="btn btn-sm btn-info text-white"
                        href="${editUrl.replace('ID', row.enc_blog_id)}">
                        <i class="fa fa-edit"></i> Edit
                        </a>

                        <a href="javascript:void(0);"
                            class="btn btn-sm btn-success btn-view-log"
                            data-table="blog"
                            data-id="${row.enc_blog_id}">
                                <i class="fa fa-history"></i> View Log
                        </a>

                    `;
                },
                className: "noPrint text-center"
            }
        ]

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }

    $(document).on('click', '.btnViewBlog', function() {

        let id = $(this).data('id');

        let modal = bootstrap.Modal.getOrCreateInstance(
            document.getElementById('viewBlogModal')
        );

        modal.show();

        $.ajax({
            url: '/admin/blogs/details',
            type: 'POST',
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {

                if (!res.status) return;

                let d = res.data;

                $('#v_title').text(d.title || '--');
                $('#v_slug').text(d.slug || '--');
                $('#v_category').text(d.category_name || '--');
                $('#v_author').text(d.author_name || '--');
                $('#v_short_description').html(d.short_description || '--');
                $('#v_content').html(d.content || '--');
                $('#v_thumb_alt_text').text(d.thumb_alt_text || '--');
                $('#v_feature_alt_text').text(d.feature_alt_text || '--');
                $('#v_meta_title').text(d.meta_title || '--');
                $('#v_meta_description').text(d.meta_description || '--');
                $('#v_published_at').text(d.published_at || '--');

                $('#v_status').html(
                    d.active_status == 1 ?
                    '<span class="badge bg-success">Active</span>' :
                    '<span class="badge bg-danger">Inactive</span>'
                );

                $('#v_is_featured').html(
                    d.is_featured == 1 ?
                    '<span class="badge bg-success">Yes</span>' :
                    '<span class="badge bg-secondary">No</span>'
                );

                if (d.canonical_url) {
                    $('#v_canonical_url')
                        .text(d.canonical_url)
                        .attr('href', d.canonical_url);
                } else {
                    $('#v_canonical_url')
                        .text('--')
                        .removeAttr('href');
                }

                // meta keywords badges
                let keywordsHtml = '--';
                if (d.meta_keywords) {
                    keywordsHtml = '';
                    d.meta_keywords.split(',').forEach(function(keyword) {
                        keywordsHtml += `<span class="badge bg-primary me-1 mb-1">${keyword.trim()}</span>`;
                    });
                }
                $('#v_meta_keywords').html(keywordsHtml);

                // thumb image
                if (d.thumb_image) {
                    $('#v_thumb_image')
                        .attr('src', "{{ asset('storage/uploads/blog') }}/" + d.thumb_image)
                        .removeClass('d-none');
                } else {
                    $('#v_thumb_image').attr('src', '').addClass('d-none');
                }

                // featured image
                if (d.featured_image) {
                    $('#v_featured_image')
                        .attr('src', "{{ asset('storage/uploads/blog') }}/" + d.featured_image)
                        .removeClass('d-none');
                } else {
                    $('#v_featured_image').attr('src', '').addClass('d-none');
                }

                // JSON formatter helper
                function formatJson(value, elementId) {
                    if (!value) {
                        $(elementId).text('--');
                        return;
                    }

                    let formattedJson = value;
                    try {
                        formattedJson = JSON.stringify(JSON.parse(value), null, 4);
                    } catch (e) {}

                    const code = document.querySelector(elementId);
                    code.textContent = formattedJson;

                    if (window.Prism) {
                        Prism.highlightElement(code);
                    }
                }

                formatJson(d.breadcrumb_schema, '#v_breadcrumb_schema');
                formatJson(d.faq_schema, '#v_faq_schema');
                formatJson(d.service_schema, '#v_service_schema');
            }
        });
    });
</script>
@endpush