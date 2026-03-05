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
        <li class="breadcrumb-item">Bus Management</li>
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
                                                        <input type="text" class="form-control" id="categoryName" name="categoryName"
                                                            value="{{ $data['row']->category_name ?? '' }}"
                                                            placeholder="Enter Category Name" maxlength="50">
                                                        <small class="text-muted char-counter float-end"></small>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="alias">Alias <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="categoryAlias" name="categoryAlias"
                                                            value="{{ $data['row']->alias ?? '' }}"
                                                            placeholder="Enter Alias"
                                                            maxlength="50">
                                                        <small class="text-muted char-counter float-end"></small>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="icon">Icon</label>
                                                        <input type="text" class="form-control" id="icon" name="icon"
                                                            value="{{ $data['row']->icon ?? '' }}"
                                                            placeholder="Icon Class Name Only">
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="description">Content</label>
                                                        <textarea
                                                                class="form-control"
                                                                id="description"
                                                                name="description"
                                                                maxlength="512"
                                                                >{{ $data['row']->content ?? '' }}</textarea>
                                                          <small class="text-muted char-counter float-end"></small>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- RIGHT COLUMN (4) -->
                                        <div class="col-md-4">
                                            
                                            <div class="mb-3">
                                                <label for="alt_text">Alt Text</label>
                                                <input type="text" class="form-control" id="alt_text" name="alt_text"
                                                    value="{{ $data['row']->alt_text ?? '' }}"
                                                    placeholder="Enter Image Alt Text">
                                            </div>

                                            <div class="mb-3">
                                                <label for="banner_image">Banner Image</label>
                                                <input type="file" class="form-control" id="bannerImageInput" name="banner_image" accept="image/*">
                                                <small class="text-muted text-md-end mt-2">
                                                    Allowed: JPG, JPEG, PNG | Max: 2MB | Size: 1600×500px
                                                </small>
                                            </div>
                                           <div id="previewContainer" class="d-none">
                                           
                                                <div class="mb-3">
                                                   <img id="bannerPreview"
                                                        src="#"
                                                        alt="Preview"
                                                        class="img-fluid border p-1 d-none">
                                                </div>
                                                <div  class="mb-1">
                                                    <button type="button"
                                                            id="removeImageBtn"
                                                            class="btn btn-danger btn-sm">
                                                        Remove Image
                                                    </button>
                                                </div>
                                           </div>
                                        </div>
                                    </div>
                                 
                                    <div class="accordion mt-3" id="userFormAccordion">

                                      
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingBasicEdit">
                                                <button class="accordion-button {{ ($data['edit_param'] ?? '') === 'basic' ? '' : 'collapsed' }}" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseBasicEdit"
                                                    aria-expanded="{{ ($data['edit_param'] ?? '') === 'basic' ? 'true' : 'false' }}"
                                                    aria-controls="collapseBasicEdit">
                                                    Basic Info
                                                </button>
                                            </h2>
                                            <div id="collapseBasicEdit"
                                                class="accordion-collapse collapse {{ ($data['edit_param'] ?? '') === 'basic' ? 'show' : '' }}"
                                                aria-labelledby="headingBasicEdit"
                                                data-bs-parent="#userFormAccordion">

                                                <div class="accordion-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="userRole">User Role<span class="text-danger important">*</span></label>
                                                            <select class="form-select user_role" id="userRole" name="user_role">
                                                                <option value="0">Select User Role</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="name">Name<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="name" name="name" value="{{ $data['row']->name ?? old('name') }}" placeholder="Enter Name">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="organization_name">Organization Name<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="organization_name" name="organization_name" value="{{ $data['row']->organization_name ?? old('organization_name') }}" placeholder="Enter Organization Name">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="primary_email">Primary Email<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="primary_email" name="primary_email" value="{{ $data['row']->primary_email ?? old('primary_email') }}" placeholder="Enter Primary Email">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="primary_contact">Primary Contact<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="primary_contact" name="primary_contact" value="{{ $data['row']->primary_contact ?? old('primary_contact') }}" placeholder="Enter Primary Contact">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="location">Location<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="location" name="location" value="{{ $data['row']->location ?? old('location') }}" placeholder="Enter Location">
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
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
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

   document.addEventListener('DOMContentLoaded', function () {

        const input = document.getElementById('bannerImageInput');
        const preview = document.getElementById('bannerPreview');
        const previewContainer = document.getElementById('previewContainer');
        const removeBtn = document.getElementById('removeImageBtn');

        if (!input) return;

        input.addEventListener('change', function (event) {

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
                commonAjax.viewAlert("Only JPG, JPEG, and PNG images are allowed.",'bannerImageInput');
                resetImage();
                return;
            }

            //  Validate MIME Type
            if (!allowedMimeTypes.includes(file.type)) {
                commonAjax.viewAlert("Invalid image format.",'bannerImageInput');
                resetImage();
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;

                previewContainer.classList.remove('d-none');
                preview.classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        });

        // Remove image
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {

                input.value = '';
                preview.src = '#';

                preview.classList.add('d-none');
                previewContainer.classList.add('d-none');
            });
        }

   });

    document.getElementById('categoryName').addEventListener('input', function() {

        this.value = this.value.replace(/\s+/g, ' ').trimStart();

        let cityName = this.value;

        let alias = cityName
            .toLowerCase() // convert to lowercase
            .trim() // remove extra spaces
            .replace(/[^a-z0-9-\s]/g, '') // remove special characters
            .replace(/\s+/g, '-') // replace spaces with -
            .replace(/-+/g, '-'); // remove duplicate -

        document.getElementById('categoryAlias').value = alias;
    });

    document.getElementById('categoryAlias').addEventListener('input', function () {

        this.value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9-]/g, '')   // allow only a-z, 0-9, -
            .replace(/-+/g, '-')      // remove duplicate -
            .replace(/^-+$/g, '');     // remove hyphen from start & end

    });


    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $(document).ready(function() {
      
        commonAjax.initCharCounter(['categoryName','categoryAlias']);
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
</script>
@endpush
