@extends('admin.layouts.master')
@section('page_title', 'Blog Images')
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
        <a href="{{ route('blog-images.index') }}" class="btn btn-success btn-sm">
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
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="blog_image">Banner Images
                                                @if($data['strPage']=='Add')
                                                <span class="text-danger important">*</span>
                                                @endif
                                            </label>

                                            <input type="file"
                                                class="form-control"
                                                id="blog_image"
                                                name="image_name[]"
                                                accept="image/*"
                                                multiple>

                                            <small class="text-muted text-md-end mt-2">
                                                Allowed: JPG, JPEG, PNG | Max: 2MB each | Size: 1600×500px
                                            </small>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="blog">Blog<span class="text-danger important">*</span></label>
                                            <select class="form-select" id="blog" name="blog_id">
                                                <option disabled selected>Select Blog</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2 gap-2 text-center">
                                            <!-- BUTTONS -->
                                            <button class="btn btn-primary btn-sm mt-4" type="submit">
                                                {{ $data['strSubmit'] }}
                                            </button>
                                            @if($data['strReset'] == 'Cancel')
                                            <a href="{{ route('blog-images.index') }}" class="btn btn-secondary btn-sm mt-4">
                                                {{ $data['strReset'] }}
                                            </a>
                                            @else
                                            <a href="{{ route('blog-images.index') }}" class="btn btn-secondary btn-sm mt-4">
                                                {{ $data['strReset'] }}
                                            </a>
                                            @endif
                                        </div>

                                        <div id="previewContainer" class="row">

                                            @if(!empty($data['row']->images))
                                            @foreach($data['row']->images as $k => $image)
                                            <div class="col-md-2 mb-3 image-box" id="preview-box-{{$k+1}}">
                                                <div class="row">
                                                    <div class="col-md-12 mt-2">
                                                        <img src="{{ asset('storage/uploads/blog/'.$image->image_name) }}"
                                                            class="img-fluid border p-1 w-100" alt="Img">
                                                        <input type="hidden" name="image_id[]" value="{{ $image->id }}">
                                                    </div>

                                                    <div class="col-md-5 mt-2">
                                                        <input type="text" class="form-control form-control-sm" name="alt_text[]" value="{{$image->alt_text}}" placeholder="Alt Tag">
                                                    </div>
                                                    <div class="col-md-4 mt-2">
                                                        <input type="number" class="form-control form-control-sm" name="sort_order[]" value="{{$image->sort_order}}">
                                                    </div>

                                                    <div class="col-md-3 mt-2">

                                                        @if($data['strPage']=='Add')
                                                        <button type="button" class="btn btn-danger btn-sm removePreview">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                        @else
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm remove-blog-image"
                                                            data-id="{{ $image->id }}"
                                                            data-table="odbusdev.blog_images"
                                                            data-path="uploads/blog"
                                                            data-container="preview-box-{{$k+1}}">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                        @endif

                                                    </div>

                                                </div>

                                            </div>
                                            @endforeach
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
    </div>
</form>

@endsection
@push('scripts')

<script type="module">
    document.addEventListener('DOMContentLoaded', function() {

        const input = document.getElementById('blog_image');
        const previewContainer = document.getElementById('previewContainer');

        if (!input) return;

        let selectedFiles = [];

        input.addEventListener('change', function(event) {

            const files = event.target.files;

            const allowedExtensions = ['jpg', 'jpeg', 'png'];
            const allowedMimeTypes = ['image/jpeg', 'image/png'];

            Array.from(files).forEach(file => {

                if (file.size > 2 * 1024 * 1024) {
                    commonAjax.viewAlert("File size must be less than 2MB", 'blog_image');
                    return;
                }

                const ext = file.name.split('.').pop().toLowerCase();

                if (!allowedExtensions.includes(ext) || !allowedMimeTypes.includes(file.type)) {
                    commonAjax.viewAlert("Only JPG, JPEG, PNG images allowed", 'blog_image');
                    return;
                }

                selectedFiles.push(file);

                const reader = new FileReader();

                reader.onload = function(e) {

                    const div = document.createElement('div');
                    div.classList.add('col-md-2', 'mb-3', 'image-box');

                    div.innerHTML = `
                    <div class="row">
                    <div class="col-md-12 mt-2">
                    <img src="${e.target.result}" class="img-fluid border p-1 w-100">
                    </div>
                    <div class="col-md-5 mt-2">
                    <input type="text" class="form-control form-control-sm" name="alt_text[]" value="{{old('alt_text')}}" placeholder="Alt Tag">
                    </div>
                    <div class="col-md-4 mt-2">
                    <input type="number" class="form-control form-control-sm" name="sort_order[]" value="0">
                    </div>
                    <div class="col-md-3 mt-2">
                        <button type="button" class="btn btn-danger btn-sm removePreview">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                `;

                    div.dataset.name = file.name;

                    previewContainer.appendChild(div);
                };

                reader.readAsDataURL(file);

            });

            updateInputFiles();
        });

        function updateInputFiles() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        }

        // Remove preview and file
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('removePreview')) {

                const box = e.target.closest('.image-box');
                const fileName = box.dataset.name;

                selectedFiles = selectedFiles.filter(file => file.name !== fileName);

                box.remove();

                updateInputFiles();
            }
        });

    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        <?php if($data['strPage']=='Add') { ?>
            if (!validator.blankCheck('blog_image', 'Image cannot be left blank')) {
                return false;
            }
        <?php } ?>

        if (!validator.selectDropdown('blog', 'Please select a blog from the list.'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {

        commonAjax.initSelect2('#blog', 'Select Blog');

        let id = <?= $data['row']->id ?? '0' ?>

        commonAjax.loadBlogList(id);
    });

    $(document).on('click', '.remove-blog-image', function() {

        let button = $(this);
        let containerId = button.data('container');

        console.log(containerId);

        commonAjax.confirmAlert('Are you sure to proceed!', function() {

            let ajaxUrl = "http://127.0.0.1:8000/admin/";

            $.ajax({
                url: ajaxUrl + "remove-blog-image",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: button.data('id'),
                    table: button.data('table'),
                    column: button.data('column'),
                    path: button.data('path')
                },
                success: function(response) {

                    if (response.status) {
                        $('#' + containerId).addClass('d-none');
                        commonAjax.viewAlert(response.message);
                    } else {
                        commonAjax.viewAlert(response.message);
                    }

                }
            });

        });

    });
</script>
@endpush