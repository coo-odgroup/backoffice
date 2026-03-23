@extends('admin.layouts.master')
@section('page_title', 'Blog Routes')
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
        <a href="{{ route('blog-routes.index') }}" class="btn btn-success btn-sm">
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

                                                    <div class="col-md-4 mb-3">
                                                        <label for="blog">Blog<span class="text-danger important">*</span></label>
                                                        <select class="form-select form-select-sm" id="blog" name="blog_id">
                                                            <option disabled selected>Select Blog</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label for="from_city_id">From City<span class="text-danger important">*</span></label>
                                                        <select class="form-select form-select-sm citySlugVal" id="from_city_id" name="from_city_id">
                                                            <option disabled selected>Select City</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label for="to_city_id">To City<span class="text-danger important">*</span></label>
                                                        <select class="form-select form-select-sm citySlugVal" id="to_city_id" name="to_city_id">
                                                            <option disabled selected>Select City</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <label for="route_slug">Route Slug<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control form-select-sm" id="route_slug" name="route_slug"
                                                            value="{{ $data['row']->route_slug ?? '' }}"
                                                            placeholder="Enter Route Slug" readonly maxlength="100">
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
                                        <a href="{{ route('blog-routes.index') }}" class="btn btn-secondary btn-sm">
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

    $(document).ready(function() {
        commonAjax.initCharCounter(['route_slug']);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.selectDropdown('blog', 'Select Blog')) {
            return false;
        }

        if (!validator.selectDropdown('from_city_id', 'Select From City')) {
            return false;
        }

        if (!validator.selectDropdown('to_city_id', 'Select To City')) {
            return false;
        }

        if (!validator.blankCheck('route_slug', 'Route Slug cannot be left blank')) {
            return false;
        }
        if (!validator.maxLength('route_slug', 100, 'Route Slug')) {
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

        commonAjax.initSelect2('#blog', 'Select Blog');
        commonAjax.initSelect2('.citySlugVal', 'Select From City');
        commonAjax.initSelect2('.citySlugVal', 'Select To City');

        let blog_id = <?= $data['row']->blog_id ?? '0' ?>;
        let from_city_id = <?= $data['row']->from_city_id ?? '0' ?>;
        let to_city_id = <?= $data['row']->to_city_id ?? '0' ?>;

        commonAjax.loadBlogList(blog_id);
        commonAjax.loadCityListSlugVal(from_city_id);
        commonAjax.loadCityListSlugVal(to_city_id);
    });

    $(document).on('change', '#from_city_id, #to_city_id', function () {

        let fromAlias = $('#from_city_id option:selected').data('alias');
        let toAlias = $('#to_city_id option:selected').data('alias');

        if (fromAlias && toAlias) {

            let slug = fromAlias + '-' + toAlias;

            $('#route_slug').val(slug);
        }

    });
</script>
@endpush