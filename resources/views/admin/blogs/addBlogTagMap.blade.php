@extends('admin.layouts.master')
@section('page_title', 'Blog Tag Map')
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
        <a href="{{ route('blog-tag-map.index') }}" class="btn btn-success btn-sm">
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
                                                        <label for="blog">Blog<span class="text-danger important">*</span></label>
                                                        <select class="form-select form-select-sm" id="blog" name="blog_id">
                                                            <option value="">Select Blog</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="blogTags">Blog Tags<span class="text-danger important">*</span></label>
                                                        <select class="form-select form-select-sm" id="blogTags" name="tag_id[]" multiple>
                                                            <option value="">Select Blog Tags</option>
                                                        </select>
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
                                        <a href="{{ route('blog-tag-map.index') }}" class="btn btn-secondary btn-sm">
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
    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();


        let blogVal = $('#blog').val();
        let tagVal = $('#blogTags').val();


        if (!blogVal || blogVal.length === 0) {
            commonAjax.viewAlert('Please select at least one Blog', 'blog');
            return false;
        }

        if (!tagVal || tagVal.length === 0) {
            commonAjax.viewAlert('Please select at least one Blog Tag', 'blogTags');
            return false;
        }

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').off('click').on('click', function() {
            e.currentTarget.submit();
        });


    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {

        commonAjax.initClearableInputs();

        let blog_id = @json($data['selectedBlog'] ?? 0);
        let tag_id = @json($data['selectedTags'] ?? []);

        // init select2 first
        $('#blog').select2({
            placeholder: 'Select Blog',
            allowClear: true,
            width: '100%'
        });

        $('#blogTags').select2({
            placeholder: 'Select Blog Tags',
            allowClear: true,
            width: '100%'
        });

        // load dropdowns
        commonAjax.loadBlogList(blog_id);
        commonAjax.loadBlogTagsList(tag_id);

        // force selected values after ajax dropdown bind
        setTimeout(function() {
            $('#blog').val(blog_id).trigger('change');
            $('#blogTags').val(tag_id).trigger('change');
        }, 500);
    });
</script>
@endpush