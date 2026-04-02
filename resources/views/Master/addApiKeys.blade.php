@extends('admin.layouts.master')
@section('page_title', 'Api Keys')
@section('content')

<?php
$page_name = 'All '.trim($__env->yieldContent('page_title'));
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
        <a href="{{ route('apikeys.index') }}" class="btn btn-success btn-sm">
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
                                        <div class="col-md-3 mb-3">
                                            <label for="apiApp">Api App<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm" id="apiApp" name="api_app_id">
                                                <option value="0">Select Api App</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="environment">Environment<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm" id="environment" name="environment">
                                                <option disabled selected>Select Environment</option>
                                                <option value="1"
                                                    {{ (isset($data['row']) && $data['row']->environment == 1) ? 'selected' : '' }}>
                                                    Staging
                                                </option>

                                                <option value="2"
                                                    {{ (isset($data['row']) && $data['row']->environment == 2) ? 'selected' : '' }}>
                                                    Production
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="api_key">
                                                Api Key<span class="text-danger important">*</span>
                                            </label>

                                            <div class="input-group input-group-sm">
                                                <input type="text"
                                                    class="form-control form-select-sm"
                                                    id="api_key"
                                                    name="api_key"
                                                    placeholder="Generate Api Key"
                                                    value="{{ $data['row']->api_key ?? '' }}">

                                                <button type="button" class="btn btn-primary" id="generateApiKey">
                                                    Generate
                                                </button>
                                            </div>
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
                                        <a href="{{ route('apikeys.index') }}" class="btn btn-secondary btn-sm">
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

    document.getElementById('generateApiKey').addEventListener('click', function () {

        function generateRandomCode(length = 64) {
            const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
            const array = new Uint8Array(length);
            window.crypto.getRandomValues(array);

            return Array.from(array, byte => chars[byte % chars.length]).join('');
        }

        document.getElementById('api_key').value = generateRandomCode(64);
    });

    $(document).ready(function() {

        commonAjax.initSelect2('#apiApp', 'Select Api App');
        commonAjax.initClearableInputs();

        let api_app_id = <?= $data['row']->api_app_id ?? '0' ?>

        commonAjax.loadApiAppsList(api_app_id);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.selectDropdown('apiApp', 'Select Api App')) {
            return false;
        }

        if (!validator.blankCheck('api_key', 'Api Key cannot be left blank'))
            return false;
        if (!validator.maxLength('api_key', 100, 'Api Key'))
            return false;

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
