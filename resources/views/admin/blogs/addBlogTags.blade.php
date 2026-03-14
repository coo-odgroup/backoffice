@extends('admin.layouts.master')
@section('page_title', 'Blog Tags')
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
        <a href="{{ route('blog-tags.index') }}" class="btn btn-success btn-sm">
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
                                        <div class="col-md-12">
                                            <div class="p-3 border rounded bg-white">
                                                <div class="row">

                                                    <div class="col-md-6 mb-3">
                                                        <label for="tag_name">Tag Name<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="tag_name" name="tag_name"
                                                            value="{{ $data['row']->tag_name ?? '' }}"
                                                            placeholder="Enter Tag Name" maxlength="100">
                                                        <small class="text-muted char-counter float-end"></small>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="slug">Slug<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="slug" name="slug"
                                                            value="{{ $data['row']->slug ?? '' }}"
                                                            placeholder="Enter Slug" maxlength="100">
                                                        <small class="text-muted char-counter float-end"></small>
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
    document.getElementById('tag_name').addEventListener('input', function() {

        this.value = this.value.replace(/\s+/g, ' ').trimStart();

        let val = this.value;

        let alias = val
            .toLowerCase() // convert to lowercase
            .trim() // remove extra spaces
            .replace(/[^a-z0-9-\s]/g, '') // remove special characters
            .replace(/\s+/g, '-') // replace spaces with -
            .replace(/-+/g, '-'); // remove duplicate -

        document.getElementById('slug').value = alias;
    });

    document.getElementById('slug').addEventListener('input', function () {

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
        commonAjax.initCharCounter(['tag_name','slug']);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.blankCheck('tag_name', 'Route Slug cannot be left blank')) {
            return false;
        }
        if (!validator.maxLength('tag_name', 100, 'Route Slug')) {
            return false;
        }

        if (!validator.blankCheck('slug', 'Route Slug cannot be left blank')) {
            return false;
        }
        if (!validator.maxLength('slug', 100, 'Route Slug')) {
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
</script>
@endpush