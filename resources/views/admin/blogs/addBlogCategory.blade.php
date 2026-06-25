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
        <li class="breadcrumb-item active">{{ $data['strPage'] }} @yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('blog-category.index') }}" class="btn btn-success btn-sm">
            View @yield('page_title')
        </a>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form" enctype="multipart/form-data">
    {{csrf_field()}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- FILTER -->
                    <div class="mb-1">
                        <div class="card-body">
                            <div class="row">
                                @if (session('message'))

                                <div class="alert alert-{{ session('level') ?? 'success' }} alert-dismissible fade show" role="alert">
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                                @endif
                                @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>

                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @endif

                                <!-- POST FIELDS -->
                                <div class="col-12">
                                    <div class="row">
                                        <!-- LEFT COLUMN (8) -->
                                        <div class="col-md-8">
                                            <div class="p-3 border rounded bg-white">
                                                <div class="row">

                                                    <div class="col-md-6 mb-3">
                                                        <label for="categoryName">Category Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control clearable form-select-sm" id="categoryName" name="categoryName"
                                                            value="{{ $data['row']->category_name ?? '' }}"
                                                            placeholder="Enter Category Name" maxlength="50">
                                                        <small class="text-muted char-counter float-end"></small>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="alias">Alias <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control clearable form-select-sm" id="categoryAlias" name="categoryAlias"
                                                            value="{{ $data['row']->slug ?? '' }}"
                                                            placeholder="Enter Alias"
                                                            maxlength="50">
                                                        <small class="text-muted char-counter float-end"></small>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="icon">Icon</label>
                                                        <input type="text" class="form-control clearable form-select-sm" id="icon" name="icon"
                                                            value="{{ $data['row']->icon ?? '' }}"
                                                            placeholder="Icon Class Name Only">
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="description">Content</label>
                                                        <textarea
                                                            class="form-control clearable form-select-sm"
                                                            id="description"
                                                            name="description"
                                                            maxlength="512">{{ strip_tags(html_entity_decode($data['row']->description ?? '')) }}</textarea>
                                                        <small class="text-muted char-counter float-end"></small>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- RIGHT COLUMN (4) -->
                                        <div class="col-md-4">

                                            <div class="mb-3">
                                                <label for="alt_text">Alt Text</label>
                                                <input type="text" class="form-control clearable form-select-sm" id="alt_text" name="alt_text"
                                                    value="{{ $data['row']->alt_text ?? '' }}"
                                                    placeholder="Enter Image Alt Text">
                                            </div>

                                            <div class="mb-3">
                                                <label for="banner_image">Banner Image</label>
                                                <input type="file" class="form-control clearable form-select-sm" id="bannerImageInput" name="banner_image" accept="image/*">
                                                <small class="text-muted text-md-end mt-2">
                                                    Allowed: JPG, JPEG, PNG | Max: 2MB | Size: 1600×500px
                                                </small>
                                            </div>

                                            <div id="previewContainer" class="{{ empty($data['row']->banner_image) ? 'd-none' : '' }}">

                                                <div class="mb-3">
                                                    <img id="bannerPreview"
                                                        src="{{ !empty($data['row']->banner_image) ? asset('storage/uploads/blog/categories/'.$data['row']->banner_image) : '#' }}"
                                                        alt="Preview"
                                                        class="img-fluid border p-1 {{ empty($data['row']->banner_image) ? 'd-none' : '' }}">
                                                </div>

                                                <div class="mb-1">
                                                    @if($data['strPage']=='Add')
                                                    <button type="button"
                                                        id="removeImageBtn"
                                                        class="btn btn-danger btn-sm">
                                                        Remove Image
                                                    </button>
                                                    @else
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm remove-image"
                                                        data-id="{{ $data['row']->id }}"
                                                        data-table="odbusdev.blog_categories"
                                                        data-column="banner_image"
                                                        data-path="uploads/blog/categories"
                                                        data-container="previewContainer">
                                                        Remove Image
                                                    </button>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="accordion mt-3" id="metaSection">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="seoHeading">
                                                <button class="accordion-button" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#seoSection"
                                                    aria-expanded="true"
                                                    aria-controls="seoSection">
                                                    Seo
                                                </button>
                                            </h2>
                                            <div id="seoSection"
                                                class="accordion-collapse collapse show"
                                                aria-labelledby="seoHeading"
                                                data-bs-parent="#metaSection">

                                                <div class="accordion-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-8 mb-3">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="meta_title">Meta Title</label>
                                                                    <input type="text" class="form-control clearable form-select-sm" id="meta_title" name="meta_title" value="{{ $data['row']->meta_title ?? old('meta_title') }}" placeholder="Enter Meta Title">
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="canonical_url">Canonical Url</label>
                                                                    <input type="text" class="form-control clearable form-select-sm" id="canonical_url" name="canonical_url" value="{{ $data['row']->canonical_url ?? old('canonical_url') }}" placeholder="Enter Canonical Url">
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="meta_description">Meta Description</label>
                                                                    <textarea class="form-control clearableform-select-sm" id="meta_description" name="meta_description" placeholder="Enter Meta Description">{{ $data['row']->meta_description ?? old('meta_description') }}</textarea>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="meta_keywords">Meta Keywords</label>
                                                                    <textarea class="form-control clearable form-select-sm" id="meta_keywords" name="meta_keywords" placeholder="Enter Meta Keywords">{{ $data['row']->meta_keywords ?? old('meta_keywords') }}</textarea>
                                                                </div>
                                                                <div class="col-md-12 mb-3">
                                                                    <label for="breadcrumb_schema">Bread Crumb Schema</label>
                                                                    <textarea class="form-control clearable form-select-sm" id="breadcrumb_schema" name="breadcrumb_schema" rows="20" placeholder="Enter Bread Crumb Schema">{{ $data['row']->breadcrumb_schema  ?? old('breadcrumb_schema ') }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <div class="mb-3"><label for="small_desc">Small Description</label>
                                                                <textarea class="form-control clearable form-select-sm" id="small_desc" name="small_desc" rows="6" placeholder="Enter Small Description">{{ $data['row']->small_desc  ?? old('small_desc ') }}</textarea>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="og_image">Og Image</label>
                                                                <input type="file" class="form-control clearable form-select-sm" id="img_3" name="og_image" accept="image/*">
                                                                <small class="text-muted text-md-end mt-2">
                                                                    Allowed: JPG, JPEG, PNG | Max: 2MB | Size: 1200×630px
                                                                </small>
                                                            </div>

                                                            <div id="previewContainer_3" class="{{ empty($data['row']->og_image) ? 'd-none' : '' }}">

                                                                <div class="mb-3">
                                                                    <img id="prv_3"
                                                                        src="{{ !empty($data['row']->og_image) ? asset('storage/uploads/blog/categories/'.$data['row']->og_image) : '#' }}"
                                                                        alt="Preview"
                                                                        class="img-fluid border p-1 {{ empty($data['row']->og_image) ? 'd-none' : '' }}">
                                                                </div>

                                                                <div class="mb-1">
                                                                    @if($data['strPage']=='Add')
                                                                    <button type="button"
                                                                        id="removeImageBtn_3"
                                                                        class="btn btn-danger btn-sm">
                                                                        Remove Image
                                                                    </button>
                                                                    @else
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm remove-image"
                                                                        data-id="{{ $data['row']->id }}"
                                                                        data-table="odbusdev.blog_categories"
                                                                        data-column="og_image"
                                                                        data-path="uploads/blog/categories"
                                                                        data-container="previewContainer_3">
                                                                        Remove Image
                                                                    </button>
                                                                    @endif
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BUTTONS -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            {{ $data['strSubmit'] }}
                                        </button>
                                        @if($data['strReset'] == 'Cancel')
                                        <a href="{{ route('blog-category.index') }}" class="btn btn-secondary btn-sm">
                                            {{ $data['strReset'] }}
                                        </a>
                                        @else
                                        <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                            {{ $data['strReset'] }}
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>

@endsection
@push('scripts')

<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        initCkEditor('#description');
    });

    document.addEventListener('DOMContentLoaded', function() {

        const input = document.getElementById('bannerImageInput');
        const preview = document.getElementById('bannerPreview');
        const previewContainer = document.getElementById('previewContainer');
        const removeBtn = document.getElementById('removeImageBtn');

        if (!input) return;

        input.addEventListener('change', function(event) {

            const file = event.target.files[0];
            if (!file) return;

            // 2MB validation
            if (file.size > 2 * 1024 * 1024) {
                commonAjax.viewAlert("File size must be less than 2MB", 'bannerImageInput');
                input.value = '';

                previewContainer.classList.add('d-none');
                preview.classList.add('d-none');
                return;
            }

            const allowedExtensions = ['jpg', 'jpeg', 'png'];
            const allowedMimeTypes = ['image/jpeg', 'image/png'];

            const fileExtension = file.name.split('.').pop().toLowerCase();

            //  Validate Extension
            if (!allowedExtensions.includes(fileExtension)) {
                commonAjax.viewAlert("Only JPG, JPEG, and PNG images are allowed.", 'bannerImageInput');
                resetImage();
                return;
            }

            //  Validate MIME Type
            if (!allowedMimeTypes.includes(file.type)) {
                commonAjax.viewAlert("Invalid image format.", 'bannerImageInput');
                resetImage();
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;

                previewContainer.classList.remove('d-none');
                preview.classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        });

        // Remove image
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {

                input.value = '';
                preview.src = '#';

                preview.classList.add('d-none');
                previewContainer.classList.add('d-none');
            });
        }

    });

    document.addEventListener('DOMContentLoaded', function() {

        const input = document.getElementById('img_3');
        const preview = document.getElementById('prv_3');
        const previewContainer_1 = document.getElementById('previewContainer_3');
        const removeBtn = document.getElementById('removeImageBtn_3');

        if (!input) return;

        input.addEventListener('change', function(event) {

            const file = event.target.files[0];
            if (!file) return;

            // 2MB validation
            if (file.size > 2 * 1024 * 1024) {
                commonAjax.viewAlert("File size must be less than 2MB", 'img_3');
                input.value = '';

                previewContainer_1.classList.add('d-none');
                preview.classList.add('d-none');
                return;
            }

            const allowedExtensions = ['jpg', 'jpeg', 'png'];
            const allowedMimeTypes = ['image/jpeg', 'image/png'];

            const fileExtension = file.name.split('.').pop().toLowerCase();

            //  Validate Extension
            if (!allowedExtensions.includes(fileExtension)) {
                commonAjax.viewAlert("Only JPG, JPEG, and PNG images are allowed.", 'img_3');
                resetImage();
                return;
            }

            //  Validate MIME Type
            if (!allowedMimeTypes.includes(file.type)) {
                commonAjax.viewAlert("Invalid image format.", 'img_3');
                resetImage();
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;

                previewContainer_1.classList.remove('d-none');
                preview.classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        });

        // Remove image
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {

                input.value = '';
                preview.src = '#';

                preview.classList.add('d-none');
                previewContainer_1.classList.add('d-none');
            });
        }

    });

    document.getElementById('categoryName').addEventListener('input', function() {

        this.value = this.value.replace(/\s+/g, ' ').trimStart();

        let aliasName = this.value;

        let alias = aliasName
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9-\s]/g, '')
            .replace(/\s+/g, '-') // replace spaces with -
            .replace(/-+/g, '-'); // remove duplicate -

        document.getElementById('categoryAlias').value = alias;

        const frontUrl = "{{rtrim(config('constants.CONSUMER_FRONT_URL'), '/')}}";

        document.getElementById('canonical_url').value = frontUrl + '/blog/category/' + alias;

        generateBreadcrumbSchema();
    });

    document.getElementById('categoryAlias').addEventListener('input', function() {

        this.value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9-]/g, '') // allow only a-z, 0-9, -
            .replace(/-+/g, '-') // remove duplicate -
            .replace(/^-+$/g, ''); // remove hyphen from start & end

        const frontUrl = "{{ rtrim(config('constants.CONSUMER_FRONT_URL'), '/') }}";
        const alias = this.value;

        document.getElementById('canonical_url').value =
            frontUrl + '/blog/category/' + alias;

        generateBreadcrumbSchema();

    });

    document.getElementById('canonical_url').addEventListener('input', function() {
        generateBreadcrumbSchema();
    });


    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $(document).ready(function() {

        commonAjax.initClearableInputs();
        commonAjax.initCharCounter(['categoryName', 'categoryAlias']);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.blankCheck('categoryName', 'Category Name cannot be left blank')) {
            return false;
        }
        if (!validator.maxLength('categoryName', 50, 'Category Name')) {
            return false;
        }

        if (!validator.blankCheck('categoryAlias', 'Category Alias cannot be left blank')) {
            return false;
        }
        if (!validator.maxLength('categoryAlias', 50, 'Category Alias')) {
            return false;
        }

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {

        $('#has_gst').on('change', function() {
            if ($(this).is(':checked')) {
                $('#gst_no').prop('disabled', false);
            } else {
                $('#gst_no').prop('disabled', true).val('');
            }
        });

    });

    function generateBreadcrumbSchema() {

        const categoryName = document.getElementById('categoryName').value.trim();
        const alias = document.getElementById('categoryAlias').value.trim();

        const frontUrl = "{{ rtrim(config('constants.CONSUMER_FRONT_URL'), '/') }}";

        const canonicalUrl =
            document.getElementById('canonical_url').value ||
            frontUrl + '/blog/category/' + alias;

        const schema = {
            "@context": "https://schema.org/",
            "@type": "BreadcrumbList",
            "itemListElement": [{
                "@type": "ListItem",
                "position": 1,
                "name": "Home Page",
                "item": frontUrl + "/"
            }, {
                "@type": "ListItem",
                "position": 2,
                "name": "Blog Page",
                "item": frontUrl + "/blog"
            }, {
                "@type": "ListItem",
                "position": 3,
                "name": "Blog Category",
                "item": canonicalUrl
            }]
        };

        document.getElementById('breadcrumb_schema').value =
            JSON.stringify(schema, null, 2);
    }
</script>
@endpush