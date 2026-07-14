@extends('admin.layouts.master')
@section('page_title', 'Organization Address')
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
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fa fa-map-marker-alt text-primary"></i>
                                        Organization Addresses
                                    </h5>

                                    <button type="button"
                                        class="btn btn-primary btn-sm btn-add-runtime">
                                        <i class="fa fa-plus"></i> Add Address
                                    </button>
                                </div>

                                <div id="addressContainer">
                                    <div class="address-card card shadow-sm border-1 mb-4">
                                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                            <strong>
                                                <i class="fa fa-map-marker-alt"></i>
                                                Address
                                            </strong>
                                            <!-- hidden in first card -->
                                            <button type="button"
                                                class="btn btn-sm btn-light text-danger btn-remove-runtime d-none">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-lg-4">
                                                    Address Type
                                                    <select class="form-select form-select-sm addressType"
                                                        name="address_type[]" id="addressType">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row g-3">

                                                <div class="col-6">
                                                    Address Line 1
                                                    <input class="form-control form-control-sm"
                                                        name="address1[]">
                                                </div>
                                                <div class="col-6">
                                                    Address Line 2
                                                    <input class="form-control form-control-sm"
                                                        name="address2[]">
                                                </div>
                                                <div class="col-lg-3">
                                                    <label for="country_id">Country</label>
                                                    <select class="form-select form-select-sm country" name="country_id[]">
                                                        <option value="1" selected>India</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    State
                                                    <select class="form-select form-select-sm selState"
                                                        id="selState"
                                                        name="state_id[]">
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    District
                                                    <select class="form-select form-select-sm selDistrict"
                                                        id="selDistrict"
                                                        name="district_id[]">
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="selCity">City </label>
                                                    <select class="form-select form-select-sm selCity" id="selCity" name="city_id[]">
                                                        <option value="0">Select City</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3">
                                                    Pincode
                                                    <input class="form-control form-control-sm"
                                                        name="pincode[]">
                                                </div>
                                                <div class="col-lg-3">
                                                    Landmark
                                                    <input class="form-control form-control-sm"
                                                        name="landmark[]">
                                                </div>
                                                <div class="col-md-3">
                                                    Latitude
                                                    <input class="form-control form-control-sm"
                                                        name="latitude[]">
                                                </div>
                                                <div class="col-md-3">
                                                    Longitude
                                                    <input class="form-control form-control-sm"
                                                        name="longitude[]">
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
        commonAjax.confirmAlert('Are you sure to proceed !');
        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });
    });
    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    let addressTypeList = [];

    $(document).ready(function() {
        let state_id = "{{ $data['row']->state_id ?? 0 }}";
        let district_id = "{{ $data['row']->district_id ?? 0 }}";
        let city_id = "{{ $data['row']->city_id ?? 0 }}";
        let selectedAddressType = "{{ $data['row']->address_type ?? '' }}";
        commonAjax.initSelect2('#addressType', 'Select Address Type');
        commonAjax.initSelect2('#selState', 'Select State');
        commonAjax.initSelect2('#selCity', 'Select City');
        commonAjax.initSelect2('#selDistrict', 'Select District');
        commonAjax.loadCityList('   ', '#selCity');
        commonAjax.loadStateList(state_id, '#selState');
        commonAjax.getDistrictList(state_id, district_id, '#selDistrict');
        commonAjax.loadAnnextureList([
            'ADDRESS_TYPE'
        ], function(data) {
            addressTypeList = data.ADDRESS_TYPE || [];
            renderDropdown(
                '#addressType',
                addressTypeList,
                selectedAddressType
            );
        });
        commonAjax.initClearableInputs();
    });

    $(document).on('change', 'select[id^="selState"]', function () {

    let stateId = $(this).val();
    let stateSelectId = $(this).attr('id');
    let districtId = '';

    if (stateSelectId === 'selState') {
        districtId = '#selDistrict';
    } else {
        let uid = stateSelectId.replace('selState_', '');
        districtId = '#selDistrict_' + uid;
    }

    $(districtId).html('<option value="">Select District</option>');

    commonAjax.getDistrictList(stateId, '', districtId);

});

    function renderDropdown(selector, items = [], selected = '') {
        let options = '<option value="">Select</option>';
        $.each(items, function(index, item) {
            options += `<option value="${item.annexture_value}"
            ${selected == item.annexture_value ? 'selected' : ''}>
            ${item.annexture_name}
        </option>`;
        });

        $(selector).html(options).trigger('change');
    }

    $(document).on('click', '.btn-add-runtime', function() {

        let uid = new Date().getTime();

        let html = `
            <div class="address-card card shadow-sm border-1 mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <strong><i class="fa fa-map-marker-alt"></i> Address</strong>

                    <button type="button"
                        class="btn btn-sm btn-light text-danger btn-remove-runtime">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-lg-4">
                            Address Type
                            <select class="form-select form-select-sm"
                                id="addressType_${uid}"
                                name="address_type[]">
                            </select>
                        </div>

                    </div>

                    <div class="row g-3">

                        <div class="col-6">
                            Address Line 1
                            <input class="form-control form-control-sm"
                                name="address1[]">
                        </div>

                        <div class="col-6">
                            Address Line 2
                            <input class="form-control form-control-sm"
                                name="address2[]">
                        </div>

                        <div class="col-lg-3">
                            Country
                            <select class="form-select form-select-sm"
                                name="country_id[]">
                                <option value="1">India</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            State
                            <select class="form-select form-select-sm"
                                id="selState_${uid}"
                                name="state_id[]">
                            </select>
                        </div>

                        <div class="col-md-3">
                            District
                            <select class="form-select form-select-sm"
                                id="selDistrict_${uid}"
                                name="district_id[]">
                            </select>
                        </div>

                        <div class="col-md-3">
                            City
                            <select class="form-select form-select-sm"
                                id="selCity_${uid}"
                                name="city_id[]">
                            </select>
                        </div>

                        <div class="col-lg-3">
                            Pincode
                            <input class="form-control form-control-sm"
                                name="pincode[]">
                        </div>

                        <div class="col-lg-3">
                            Landmark
                            <input class="form-control form-control-sm"
                                name="landmark[]">
                        </div>

                        <div class="col-md-3">
                            Latitude
                            <input class="form-control form-control-sm"
                                name="latitude[]">
                        </div>

                        <div class="col-md-3">
                            Longitude
                            <input class="form-control form-control-sm"
                                name="longitude[]">
                        </div>

                    </div>

                </div>
            </div>
            `;

        $('#addressContainer').append(html);

        commonAjax.initSelect2('#addressType_' + uid, 'Select Address Type');
        commonAjax.initSelect2('#selState_' + uid, 'Select State');
        commonAjax.initSelect2('#selDistrict_' + uid, 'Select District');
        commonAjax.initSelect2('#selCity_' + uid, 'Select City');

        renderDropdown('#addressType_' + uid, addressTypeList, '');

        commonAjax.loadStateList(0, '#selState_' + uid);
        commonAjax.loadCityList(0, '#selCity_' + uid);
        setTimeout(function() {
            console.log($('#selCity_' + uid).html());
        }, 1000);

    });

    $(document).on('click', '.btn-remove-runtime', function() {
        $(this).closest('.address-card').remove();
    });

    $(document).on('click', '.btn-remove-runtime', function() {

        if ($('.address-card').length > 1) {
            $(this).closest('.address-card').remove();
        }

    });
</script>
@endpush