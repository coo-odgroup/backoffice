@extends('admin.layouts.master')
@section('page_title', 'Blogs')
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
        <a href="{{ route('blogs.index') }}" class="btn btn-success btn-sm">
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

                                                    <div class="col-md-12 mb-3">
                                                        <label for="title">Title <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="title" name="title"
                                                            value="{{ $data['row']->title ?? '' }}"
                                                            placeholder="Enter Title" maxlength="100">
                                                        <small class="text-muted char-counter float-end"></small>
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <label for="blogAlias">Alias <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="blogAlias" name="slug"
                                                            value="{{ $data['row']->slug ?? '' }}"
                                                            placeholder="Enter Alias"
                                                            maxlength="100">
                                                        <small class="text-muted char-counter float-end"></small>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="short_description">Short Description</label>
                                                        <textarea
                                                            class="form-control"
                                                            id="short_description"
                                                            name="short_description"
                                                            maxlength="512">{{ strip_tags(html_entity_decode($data['row']->short_description ?? '')) }}</textarea>
                                                        <small class="text-muted char-counter float-end"></small>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="content">Content</label>
                                                        <textarea
                                                            class="form-control"
                                                            id="content"
                                                            name="content"
                                                            maxlength="512">{{ strip_tags(html_entity_decode($data['row']->content ?? '')) }}</textarea>
                                                        <small class="text-muted char-counter float-end"></small>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- RIGHT COLUMN (4) -->
                                        <div class="col-md-4">

                                            <div class="p-3 border rounded bg-white mb-3">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="blogCategory">Category</label>
                                                        <select class="form-select" id="blogCategory" name="category_id">
                                                            <option disabled selected>Select Category</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="is_featured">Is Featured</label>
                                                        <select class="form-select" id="is_featured" name="is_featured">
                                                            <option disabled selected>Select</option>
                                                            <option value="1"
                                                                {{ (isset($data['row']) && $data['row']->is_featured == 1) ? 'selected' : '' }}>
                                                                Yes
                                                            </option>
                                                            <option value="2"
                                                                {{ (isset($data['row']) && $data['row']->is_featured == 2) ? 'selected' : '' }}>
                                                                No
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="p-3 border rounded bg-white">
                                                <div class="row">
                                                    <div class="mb-3">
                                                        <label for="thumb_alt_text">Thumb Image Alt Text</label>
                                                        <input type="text" class="form-control" id="thumb_alt_text" name="thumb_alt_text"
                                                            value="{{ $data['row']->thumb_alt_text ?? '' }}"
                                                            placeholder="Enter Thumb Image Alt Text">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="thumb_image">Thumb Image</label>
                                                        <input type="file" class="form-control" id="img_1" name="thumb_image" accept="image/*">
                                                        <small class="text-muted text-md-end mt-2">
                                                            Allowed: JPG, JPEG, PNG | Max: 2MB | Size: 1600×500px
                                                        </small>
                                                    </div>

                                                    <div id="previewContainer_1" class="{{ empty($data['row']->thumb_image) ? 'd-none' : '' }}">

                                                        <div class="mb-3">
                                                            <img id="prv_1"
                                                                src="{{ !empty($data['row']->thumb_image) ? asset('storage/uploads/blog/'.$data['row']->thumb_image) : '#' }}"
                                                                alt="Preview"
                                                                class="img-fluid border p-1 {{ empty($data['row']->thumb_image) ? 'd-none' : '' }}">
                                                        </div>

                                                        <div class="mb-1">
                                                            @if($data['strPage']=='Add')
                                                            <button type="button"
                                                                id="removeImageBtn_1"
                                                                class="btn btn-danger btn-sm">
                                                                Remove Image
                                                            </button>
                                                            @else
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-image"
                                                                data-id="{{ $data['row']->id }}"
                                                                data-table="odbusdev.blogs"
                                                                data-column="thumb_image"
                                                                data-path="uploads/blog"
                                                                data-container="previewContainer_1">
                                                                Remove Image
                                                            </button>
                                                            @endif
                                                        </div>

                                                    </div>

                                                    <hr />

                                                    <div class="mb-3">
                                                        <label for="feature_alt_text">Featured Image Alt Text</label>
                                                        <input type="text" class="form-control" id="feature_alt_text" name="feature_alt_text"
                                                            value="{{ $data['row']->feature_alt_text ?? '' }}"
                                                            placeholder="Enter Featured Image Alt Text">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="featured_image">Featured Image</label>
                                                        <input type="file" class="form-control" id="img_2" name="featured_image" accept="image/*">
                                                        <small class="text-muted text-md-end mt-2">
                                                            Allowed: JPG, JPEG, PNG | Max: 2MB | Size: 1600×500px
                                                        </small>
                                                    </div>

                                                    <div id="previewContainer_2" class="{{ empty($data['row']->featured_image) ? 'd-none' : '' }}">

                                                        <div class="mb-3">
                                                            <img id="prv_2"
                                                                src="{{ !empty($data['row']->featured_image) ? asset('storage/uploads/blog/'.$data['row']->featured_image) : '#' }}"
                                                                alt="Preview"
                                                                class="img-fluid border p-1 {{ empty($data['row']->featured_image) ? 'd-none' : '' }}">
                                                        </div>

                                                        <div class="mb-1">
                                                            @if($data['strPage']=='Add')
                                                            <button type="button"
                                                                id="removeImageBtn_2"
                                                                class="btn btn-danger btn-sm">
                                                                Remove Image
                                                            </button>
                                                            @else
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-image"
                                                                data-id="{{ $data['row']->id }}"
                                                                data-table="odbusdev.blogs"
                                                                data-column="featured_image"
                                                                data-path="uploads/blog"
                                                                data-container="previewContainer_2">
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
                                                                    <label for="meta_title">Meta Title<span class="text-danger important">*</span></label>
                                                                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ $data['row']->meta_title ?? old('meta_title') }}" placeholder="Enter Meta Title">
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="canonical_url">Canonical Url<span class="text-danger important">*</span></label>
                                                                    <input type="text" class="form-control" id="canonical_url" name="canonical_url" value="{{ $data['row']->canonical_url ?? old('canonical_url') }}" placeholder="Enter Canonical Url">
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="meta_description">Meta Description<span class="text-danger important">*</span></label>
                                                                    <textarea class="form-control" id="meta_description" name="meta_description" placeholder="Enter Meta Description">{{ $data['row']->meta_description ?? old('meta_description') }}</textarea>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="meta_keywords">Meta Keywords<span class="text-danger important">*</span></label>
                                                                    <textarea class="form-control" id="meta_keywords" name="meta_keywords" placeholder="Enter Meta Keywords">{{ $data['row']->meta_keywords ?? old('meta_keywords') }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <div class="mb-3">
                                                                <label for="og_image">Og Image</label>
                                                                <input type="file" class="form-control" id="img_3" name="og_image" accept="image/*">
                                                                <small class="text-muted text-md-end mt-2">
                                                                    Allowed: JPG, JPEG, PNG | Max: 2MB | Size: 1200×630px
                                                                </small>
                                                            </div>

                                                            <div id="previewContainer_3" class="{{ empty($data['row']->og_image) ? 'd-none' : '' }}">

                                                                <div class="mb-3">
                                                                    <img id="prv_3"
                                                                        src="{{ !empty($data['row']->og_image) ? asset('storage/uploads/blog/'.$data['row']->og_image) : '#' }}"
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
                                                                        data-table="odbusdev.blogs"
                                                                        data-column="og_image"
                                                                        data-path="uploads/blog"
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
                                        <a href="{{ route('blogs.index') }}" class="btn btn-secondary btn-sm">
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
</form>

@endsection
@push('scripts')

<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        initCkEditor('#content');
    });

    document.addEventListener('DOMContentLoaded', function() {

        const input_1 = document.getElementById('img_1');
        const preview_1 = document.getElementById('prv_1');
        const previewContainer_1 = document.getElementById('previewContainer_1');
        const removeBtn_1 = document.getElementById('removeImageBtn_1');

        if (!input_1) return;

        input_1.addEventListener('change', function(event) {

            const file = event.target.files[0];
            if (!file) return;

            // 2MB validation
            if (file.size > 2 * 1024 * 1024) {
                commonAjax.viewAlert("File size must be less than 2MB", 'img_1');
                input_1.value = '';

                previewContainer_1.classList.add('d-none');
                preview_1.classList.add('d-none');
                return;
            }

            const allowedExtensions = ['jpg', 'jpeg', 'png'];
            const allowedMimeTypes = ['image/jpeg', 'image/png'];

            const fileExtension = file.name.split('.').pop().toLowerCase();

            //  Validate Extension
            if (!allowedExtensions.includes(fileExtension)) {
                commonAjax.viewAlert("Only JPG, JPEG, and PNG images are allowed.", 'img_1');
                resetImage();
                return;
            }

            //  Validate MIME Type
            if (!allowedMimeTypes.includes(file.type)) {
                commonAjax.viewAlert("Invalid image format.", 'img_1');
                resetImage();
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                preview_1.src = e.target.result;

                previewContainer_1.classList.remove('d-none');
                preview_1.classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        });

        // Remove image
        if (removeBtn_1) {
            removeBtn_1.addEventListener('click', function() {

                input_1.value = '';
                preview_1.src = '#';

                preview_1.classList.add('d-none');
                previewContainer_1.classList.add('d-none');
            });
        }

    });

    document.addEventListener('DOMContentLoaded', function() {

        const input_2 = document.getElementById('img_2');
        const preview_2 = document.getElementById('prv_2');
        const previewContainer_2 = document.getElementById('previewContainer_2');
        const removeBtn_2 = document.getElementById('removeImageBtn_2');

        if (!input_2) return;

        input_2.addEventListener('change', function(event) {

            const file = event.target.files[0];
            if (!file) return;

            // 2MB validation
            if (file.size > 2 * 1024 * 1024) {
                commonAjax.viewAlert("File size must be less than 2MB", 'img_2');
                input_2.value = '';

                previewContainer_2.classList.add('d-none');
                preview_2.classList.add('d-none');
                return;
            }

            const allowedExtensions = ['jpg', 'jpeg', 'png'];
            const allowedMimeTypes = ['image/jpeg', 'image/png'];

            const fileExtension = file.name.split('.').pop().toLowerCase();

            // Validate Extension
            if (!allowedExtensions.includes(fileExtension)) {
                commonAjax.viewAlert("Only JPG, JPEG, and PNG images are allowed.", 'img_2');
                resetImage();
                return;
            }

            // Validate MIME Type
            if (!allowedMimeTypes.includes(file.type)) {
                commonAjax.viewAlert("Invalid image format.", 'img_2');
                resetImage();
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                preview_2.src = e.target.result;

                previewContainer_2.classList.remove('d-none');
                preview_2.classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        });

        // Remove image
        if (removeBtn_2) {
            removeBtn_2.addEventListener('click', function() {

                input_2.value = '';
                preview_2.src = '#';

                preview_2.classList.add('d-none');
                previewContainer_2.classList.add('d-none');
            });
        }

    });

    document.addEventListener('DOMContentLoaded', function() {

        const input_3 = document.getElementById('img_3');
        const preview_3 = document.getElementById('prv_3');
        const previewContainer_3 = document.getElementById('previewContainer_3');
        const removeBtn_3 = document.getElementById('removeImageBtn_3');

        if (!input_3) return;

        input_3.addEventListener('change', function(event) {

            const file = event.target.files[0];
            if (!file) return;

            // 2MB validation
            if (file.size > 2 * 1024 * 1024) {
                commonAjax.viewAlert("File size must be less than 2MB", 'img_3');
                input_3.value = '';

                previewContainer_3.classList.add('d-none');
                preview_3.classList.add('d-none');
                return;
            }

            const allowedExtensions = ['jpg', 'jpeg', 'png'];
            const allowedMimeTypes = ['image/jpeg', 'image/png'];

            const fileExtension = file.name.split('.').pop().toLowerCase();

            // Validate Extension
            if (!allowedExtensions.includes(fileExtension)) {
                commonAjax.viewAlert("Only JPG, JPEG, and PNG images are allowed.", 'img_3');
                resetImage();
                return;
            }

            // Validate MIME Type
            if (!allowedMimeTypes.includes(file.type)) {
                commonAjax.viewAlert("Invalid image format.", 'img_3');
                resetImage();
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                preview_3.src = e.target.result;

                previewContainer_3.classList.remove('d-none');
                preview_3.classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        });

        // Remove image
        if (removeBtn_3) {
            removeBtn_3.addEventListener('click', function() {

                input_3.value = '';
                preview_3.src = '#';

                preview_3.classList.add('d-none');
                previewContainer_3.classList.add('d-none');
            });
        }

    });

    document.getElementById('title').addEventListener('input', function() {

        this.value = this.value.replace(/\s+/g, ' ').trimStart();

        let cityName = this.value;

        let alias = cityName
            .toLowerCase() // convert to lowercase
            .trim() // remove extra spaces
            .replace(/[^a-z0-9-\s]/g, '') // remove special characters
            .replace(/\s+/g, '-') // replace spaces with -
            .replace(/-+/g, '-'); // remove duplicate -

        document.getElementById('blogAlias').value = alias;
    });

    document.getElementById('blogAlias').addEventListener('input', function() {

        this.value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9-]/g, '') // allow only a-z, 0-9, -
            .replace(/-+/g, '-') // remove duplicate -
            .replace(/^-+$/g, ''); // remove hyphen from start & end

    });


    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $(document).ready(function() {

        commonAjax.initCharCounter(['title', 'blogAlias']);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.blankCheck('title', 'Category Name cannot be left blank')) {
            return false;
        }
        if (!validator.maxLength('title', 50, 'Category Name')) {
            return false;
        }

        if (!validator.blankCheck('blogAlias', 'Category Alias cannot be left blank')) {
            return false;
        }
        if (!validator.maxLength('blogAlias', 50, 'Category Alias')) {
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

        commonAjax.initSelect2('#blogCategory', 'Select Blog Category');

        let category_id = <?= $data['row']->category_id ?? '0' ?>

        commonAjax.loadBlogCategoryList(category_id);
    });
</script>
@endpush