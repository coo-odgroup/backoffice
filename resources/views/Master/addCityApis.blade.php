@extends('admin.layouts.master')
@section('page_title', 'App City Ids')
@section('content')

<?php
$page_name = 'All ' . trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">{{ $data['strPage'] }} @yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('cityapis.index') }}" class="btn btn-success btn-sm">
            View @yield('page_title')
        </a>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form">
    {{csrf_field()}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- FILTER -->
                    <div class="mb-3">
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
                                        <div class="col-md-4 mb-3">
                                            <label for="selCity">City<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm selCity" id="selCity" name="city_id">
                                                <option value="0">Select City</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="apiApp">Api App<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm apiApp" id="apiApp" name="api_app_id">
                                                <option value="0">Select Api App</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="api_city_ids">App City Id<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control form-select-sm clearable" id="api_city_ids" name="api_city_ids" value="{{ $data['row']->api_city_ids ?? '' }}" placeholder="Enter App City Id">
                                        </div>
                                    </div>
                                </div>

                                <!-- BUTTONS -->
                                <div class="row">
                                    <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            {{ $data['strSubmit'] }}
                                        </button>
                                        @if($data['strReset'] == 'Cancel')
                                        <a href="{{ route('cityapis.index') }}" class="btn btn-secondary btn-sm">
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
    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $(document).ready(function() {

        commonAjax.initSelect2('.apiApp', 'Select Api App');
        commonAjax.initClearableInputs();
        commonAjax.initSelect2('.selCity', 'Select City');

        let city_id = <?= $data['row']->city_id ?? '0' ?>;
        let api_app_id = <?= $data['row']->api_app_id ?? '0' ?>;

        commonAjax.loadApiAppsList(api_app_id);
        commonAjax.loadCityList(city_id);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.selectDropdown('selCity', 'Select City')) {
            return false;
        }

        if (!validator.selectDropdown('apiApp', 'Select Api App')) {
            return false;
        }

        if (!validator.blankCheck('api_city_ids', 'Api City Ids cannot be left blank'))
            return false;

        var apiCityIds = document.getElementById('api_city_ids').value.trim();

        if (!/^\d+$/.test(apiCityIds)) {
            commonAjax.viewAlert('Api City Ids must contain numbers only');
            document.getElementById('api_city_ids').focus();
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