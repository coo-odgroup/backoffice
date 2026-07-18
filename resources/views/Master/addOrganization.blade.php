@extends('admin.layouts.master')
@section('page_title', 'Organization')
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
        <a href="{{ route('organization.index') }}" class="btn btn-success btn-sm">
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
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="row mb-2">
                                            <!-- Left Side -->
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="org">
                                                        Organization Type
                                                        <span class="text-danger important">*</span>
                                                    </label>
                                                    <select class="form-select form-select-sm selOrg" id="org" name="org">
                                                    </select>
                                                </div>
                                                <div class="mb-3 d-none">
                                                    <label for="uniqueId">
                                                        UniqueId
                                                        <span class="text-danger important">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control form-control-sm clearable"
                                                        placeholder="UniqueID"
                                                        id="uniqueId"
                                                        name="uniqueId"
                                                        value="{{ old('uniqueId', $data['uniqueId'] ?? ($data['row']->unique_id ?? '')) }}"
                                                        readonly>
                                                </div>


                                                <!-- Right Side -->

                                                <div class="mb-3">
                                                    <label for="orgName">
                                                        Organization Name
                                                        <span class="text-danger important">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control form-control-sm clearable"
                                                        placeholder="Enter Organization Name"
                                                        id="orgName"
                                                        name="orgName"
                                                        value="{{ old('orgName', $data['row']->organization_name ?? '') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="orgCode">
                                                        Organization Code
                                                        <span class="text-danger important">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control form-control-sm clearable"
                                                        placeholder="Enter Organization Code"
                                                        id="orgCode"
                                                        name="orgCode"
                                                        value="{{ $data['row']->organization_code ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 ">
                                        <div class="w-100">
                                            <div class="row text-center">
                                                <div class="w-100">
                                                    <div class="row">


                                                        <!-- Website URL -->
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="website_url">
                                                                    Website URL
                                                                </label>
                                                                <input type="url"
                                                                    class="form-control form-control-sm"
                                                                    id="website_url"
                                                                    name="website_url"
                                                                    placeholder="https://example.com"
                                                                    value="{{ old('website_url', $data['row']->website_url ?? '') }}">
                                                            </div>
                                                        </div>
                                                        <!-- Logo -->
                                                        <div class="col-md-6">
                                                            <div class="mb-3">

                                                                <label for="logo">
                                                                    Logo
                                                                    <small class="text-muted">(SVG, JPG, JPEG, PNG, WEBP)</small>
                                                                </label>

                                                                <input type="file"
                                                                    class="form-control form-control-sm"
                                                                    id="logo"
                                                                    name="logo"
                                                                    accept=".svg,.jpg,.jpeg,.png,.webp,image/svg+xml,image/jpeg,image/png,image/webp">

                                                                <!-- Image Preview -->
                                                                <div class="mt-3">
                                                                    <img id="logoPreview"
                                                                        src="@if(!empty($data['row']->logo)){{ asset('storage/organization/'.$data['row']->logo) }}@else{{ asset('images/no-image.png') }}@endif"
                                                                        alt="Logo Preview"
                                                                        class="img-thumbnail"
                                                                        style="max-height:120px; max-width:180px;">
                                                                </div>

                                                                @if(!empty($data['row']->logo))
                                                                <small class="text-success d-block mt-2">
                                                                    Current: {{ $data['row']->logo }}
                                                                </small>
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

                        <!-- BUTTONS -->
                        <div class="row">
                            <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                <button class="btn btn-primary btn-sm" type="submit">
                                    {{ $data['strSubmit'] }}
                                </button>
                                @if($data['strReset'] == 'Cancel')
                                <a href="{{ route('states.index') }}" class="btn btn-secondary btn-sm">
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

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.selectDropdown('org', 'Select Organization Type'))
            return false;
        if (!validator.blankCheck('orgName', 'Organization Name cannot be left blank'))
            return false;
        if (!validator.maxLength('orgName', 100, 'Organization Name'))
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
        commonAjax.initSelect2('#org', 'Select Organization Type');
        let selectedOrg = "{{ $data['row']->organization_type ?? '' }}";
        commonAjax.loadOrganizationTypeList(
            "{{ old('org', $data['row']->organization_type ?? '') }}"
        );
        commonAjax.initClearableInputs();
        commonAjax.initClearableInputs();

    });

    function renderDropdown(selector, items = [], selected = '') {
        let options = '<option value="">Select Organization Type</option>';

        $.each(items, function(index, item) {

            let isSelected =
                selected == item.annexture_value ? 'selected' : '';

            options += `
            <option value="${item.annexture_value}" ${isSelected}>
                ${item.annexture_name}
            </option>
        `;
        });

        $(selector).html(options).trigger('change');
    }

    $('#logo').on('change', function(e) {

        const file = e.target.files[0];
        if (!file) return;  
        const reader = new FileReader();
        reader.onload = function(event) {
            $('#logoPreview').attr('src', event.target.result);
        };

        reader.readAsDataURL(file);

    });
</script>
@endpush