@extends('admin.layouts.master')
@section('page_title', 'Branch')
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
        <a href="{{ route('branch.index') }}" class="btn btn-success btn-sm">
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
                                        <div class="col-lg-6 mb-3">
                                            <div class="card border shadow-sm h-100">
                                                <div class="card-header bg-light fw-bold">
                                                    Branch Information
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label>Organization Type <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm selOrg"
                                                                id="orgType"
                                                                name="orgType">
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Branch Type <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm selBranchType"
                                                                id="branchType"
                                                                name="branchType">
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Parent Branch</label>
                                                            <select class="form-select form-select-sm selParentBranch"
                                                                id="parentBranch"
                                                                name="parentBranch">
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Branch Name <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control form-control-sm clearable"
                                                                id="branchName"
                                                                value="{{ old('branchName', $data['row']->branch_name ?? '') }}"
                                                                name="branchName">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Branch Code <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control form-control-sm clearable"
                                                                id="branchCode"
                                                                value="{{ old('branchCode', $data['row']->branch_code ?? '') }}"
                                                                name="branchCode">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- ================= Contact Information ================= -->
                                        <div class="col-lg-6 mb-3">
                                            <div class="card border shadow-sm h-100">
                                                <div class="card-header bg-light fw-bold">
                                                    Contact Information
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label>Phone No. <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control form-control-sm clearable"
                                                                id="phoneNo"
                                                                value="{{ old('phoneNo', $data['row']->phone ?? '') }}"
                                                                name="phoneNo">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Email <span class="text-danger">*</span></label>
                                                            <input type="email"
                                                                class="form-control form-control-sm clearable"
                                                                id="email"
                                                                value="{{ old('email', $data['row']->email ?? '') }}"
                                                                name="email">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Opening Date</label>
                                                            <input type="date"
                                                                class="form-control form-control-sm"
                                                                id="openingDate"
                                                                value="{{ old('openingDate', isset($data['row']->opening_date) ? date('Y-m-d', strtotime($data['row']->opening_date)) : '') }}"
                                                                name="openingDate">
                                                        </div>
                                                        <div class="col-md-6 d-flex align-items-end">
                                                            <div class="form-check form-switch mb-2">
                                                                <input class="form-check-input"
                                                                    type="checkbox"
                                                                    id="isHeadOffice"
                                                                    name="isHeadOffice"
                                                                    value="1"
                                                                    {{ old('isHeadOffice', $data['row']->is_head_office ?? 0) == 1 ? 'checked' : '' }}>

                                                                <label class="form-check-label fw-semibold"
                                                                    for="isHeadOffice">
                                                                    Is Head Office
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ================= Address Information ================= -->
                                        <div class="col-12">
                                            <div class="card border shadow-sm mb-3">
                                                <div class="card-header bg-light fw-bold">
                                                    Address Information
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label>Address Line 1 <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control form-control-sm"
                                                                id="address1"
                                                                value="{{ old('address1', $data['row']->address1 ?? '') }}"
                                                                name="address1">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Address Line 2</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm"
                                                                id="address2"
                                                                value="{{ old('address2', $data['row']->address2 ?? '') }}"
                                                                name="address2">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>Country <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm selCountry"
                                                                id="country"
                                                                name="country">
                                                                <option value=""> Select Country </option>
                                                                <option value="101" selected>India</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>State <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm "
                                                                id="state"
                                                                name="state">
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>City <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm "
                                                                id="city"
                                                                name="city">
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>Pin Code <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control form-control-sm"
                                                                id="pinCode"
                                                                value="{{ old('pinCode', $data['row']->pincode ?? '') }}"
                                                                name="pinCode">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Latitude</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm"
                                                                id="latitude"
                                                                value="{{ old('latitude', $data['row']->latitude ?? '') }}"
                                                                name="latitude">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Longitude</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm"
                                                                id="longitude"
                                                                value="{{ old('longitude', $data['row']->longitude ?? '') }}"
                                                                name="longitude">
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
        $('#phoneNo').on('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });

        $('#pinCode').on('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });

        $('#latitude').on('input', function() {
            this.value = this.value.replace(/[^0-9.\-]/g, '');
        });

        $('#longitude').on('input', function() {
            this.value = this.value.replace(/[^0-9.\-]/g, '');
        });

        $('#email').on('blur', function() {

            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if ($(this).val() !== '' && !emailRegex.test($(this).val())) {
                alert('Please enter a valid email address.');
                $(this).focus();
            }

        });


        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {

        commonAjax.initSelect2('#orgType', 'Select Organization');
        commonAjax.initSelect2('#branchType', 'Select Branch Type');
        commonAjax.initSelect2('#parentBranch', 'Select Parent Branch Type');
        commonAjax.initSelect2('#state', 'Select State');
        commonAjax.initSelect2('#city', 'Select City');

        let selectedOrganization = "{{ $data['row']->organization_id ?? '' }}";
        let selectedBranchType = "{{ $data['row']->branch_type_id ?? '' }}";
        let selectedParentBranchType = "{{ $data['row']->parent_branch_id ?? '' }}";
        let selectedStateList = "{{ $data['row']->state_id ?? '' }}";
        let selectedCityList = "{{ $data['row']->city_id ?? '' }}";

        commonAjax.loadOrganizationTypeList(selectedOrganization);
        commonAjax.loadBranchTypeList(selectedBranchType);
        commonAjax.loadParentBranchList(selectedParentBranchType);
        commonAjax.loadStateList(selectedStateList, "#state");
        commonAjax.loadCityList(selectedCityList, "#city");

        commonAjax.initClearableInputs();
        commonAjax.initClearableInputs();

    });

    $('#branchName').on('keyup blur', function() {

        let branchCode = $(this).val()
            .trim()
            .toUpperCase()
            .replace(/\s+/g, '_')
            .replace(/[^A-Z0-9_]/g, '');

        $('#branchCode').val(branchCode);

    });

    $('#branchCode').on('keyup blur', function() {

        $(this).val(
            $(this).val()
            .trim()
            .toUpperCase()
            .replace(/\s+/g, '_')
            .replace(/[^A-Z0-9_]/g, '')
        );

    });
</script>
@endpush