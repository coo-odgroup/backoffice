@extends('admin.layouts.master')
@section('page_title', 'BusInfo')
@section('content')

<?php
    $page_name = 'All ' . trim($__env->yieldContent('page_title'));
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
        <a href="{{ route('amenities.index') }}" class="btn btn-success btn-sm">
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
                    <div class="mb-2">
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

                                    <!-- ================= STEP 1 ================= -->
                                    <div id="step1">

                                        <!-- ROW 1 (8 + 4) -->
                                        <div class="row mb-1">

                                            <!-- LEFT SIDE (8 columns) -->
                                            <div class="col-md-9">
                                                <div class="p-3 border rounded bg-white">
                                                    <div class="row">

                                                        <div class="col-md-6 mb-2">
                                                            <label for="selOpeator">Operator <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm" name="selOpeator" id="selOpeator">
                                                                <option value="0">Select Operator</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="busName">Bus Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Bus Name" name="busName" id="busName">
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="busNumber">Bus Number <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Bus Number" name="busNumber" id="busNumber">
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="via">Via</label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Via" name="via" id="via">
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="selAmenity">Amenities <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm" name="selAmenity" id="selAmenity" multiple>
                                                                <option>Select Amenities</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="maxSeat">Max Seat Booked <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Max Seat" name="maxSeat" id="maxSeat" value="6">
                                                        </div>

                                                    </div>
                                                    <hr class="wide">
                                                    <!-- SECOND SECTION -->
                                                    <div class="row">

                                                        <div class="col-md-3 mb-2">
                                                            <label for="brand">Brand</label>
                                                            <select class="form-select form-select-sm onSelect" id="brand" name="brand">
                                                                <option value="0">Select Brand</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3 mb-2">
                                                            <label for="busModel">Model</label>
                                                            <select class="form-select form-select-sm onSelect" id="busModel" name="model">
                                                                <option  value="0">Select Model</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3 mb-2">
                                                            <label for="axleType">Axle Type</label>
                                                            <select class="form-select form-select-sm onSelect" id="axleType" name="axleType">
                                                                <option value="0">Select Axle Type</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3 mb-2">
                                                            <label for="busService">Bus Service</label>
                                                            <select class="form-select form-select-sm onSelect" id="busService" name="busService">
                                                                <option value="0">Select Bus Service</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-2 mb-1">
                                                            <label for="acType">AC Type <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm onSelect annexture" id="acType" name="acType">
                                                                <option value="0">Select AC Type</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-2 mb-1">
                                                            <label for="seatType">Seat Type <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm onSelect" id="seatType" name="seatType">
                                                                <option value="0">Select Seat Type</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-2 mb-1">
                                                            <label for="seatLayout">Seat Layout <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm onSelect" id="seatLayout" name="seatLayout">
                                                                <option value="0">Select Seat Layout</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-1">
                                                            <label for="busType">Bus Type<span class="text-danger">*</span></label>
                                                            <span id="busType"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- RIGHT SIDE (4 columns) -->
                                            <div class="col-md-3">
                                                <div class="p-3 border rounded bg-white">
                                                    <div class="mb-2">
                                                        <label>Cancellation Slab<span class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm">
                                                            <option>Select Slab</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- THIRD SECTION -->
                                        <div class="row">

                                            <div class="col-md-4 mb-2 d-flex align-items-center">
                                                <input type="checkbox" class="me-2">
                                                <label class="mb-0">Has IRCTC Module</label>
                                            </div>

                                        </div>

                                        <!-- BUTTON -->
                                        <div class="text-center mt-4">
                                            <button type="button" class="btn btn-warning px-5 rounded-pill" onclick="nextStep()">Next →</button>
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

<script>
    function nextStep() {
         window.location.href = "/admin/bus/create/step2";
    }
    
    document.addEventListener("click", function(e) {

        if (e.target.classList.contains("addRow")) {

            let row = e.target.closest(".stationRow");

            let newRow = row.cloneNode(true);

            newRow.querySelector(".addRow").innerHTML = "−";

            newRow.querySelector(".addRow").classList.remove("btn-primary");
            newRow.querySelector(".addRow").classList.add("btn-danger");
            newRow.querySelector(".addRow").classList.remove("addRow");
            newRow.querySelector(".addRow").classList.add("removeRow");

            row.parentNode.appendChild(newRow);

        }

        if (e.target.classList.contains("removeRow")) {

            e.target.closest(".stationRow").remove();

        }

    });
</script>

@endsection
@push('scripts')

<script type="module">
    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('.onSelect').on('change', function () {
        generateBusName();
    });

    function generateBusName() {

         let parts = [];

        if ($('#brand').val() != 0) {
            parts.push($('#brand option:selected').text());
        }

        if ($('#busModel').val() != 0) {
            parts.push($('#busModel option:selected').text());
        }

        if ($('#axleType').val() != 0) {
            parts.push($('#axleType option:selected').text());
        }

        if ($('#busService').val() != 0) {
            parts.push($('#busService option:selected').text());
        }

        if ($('.annexture').val() != 0) {
            parts.push($('.annexture option:selected').text());
        }

        if ($('#seatType').val() != 0) {
            parts.push($('#seatType option:selected').text());
        }

        if ($('#seatLayout').val() != 0) {
            parts.push($('#seatLayout option:selected').text());
        }

        // Join with space
        const fullName = parts.join(' ');

        $('#busType').html(fullName);
    }

    $(document).ready(function() {
        commonAjax.initClearableInputs();
        commonAjax.initSelect2('#brand', 'Select Brand');
        commonAjax.initSelect2('#busModel', 'Select Model');
        commonAjax.initSelect2('#axleType', 'Select Axxle Type');
        commonAjax.initSelect2('#busService', 'Select Bus Service');
        commonAjax.initSelect2('.annexture', 'Select AC Type');
        commonAjax.initSelect2('#seatType', 'Select Seat Type');
        commonAjax.initSelect2('#seatLayout', 'Select Seat Layout');
        commonAjax.initSelect2('#selAmenity', 'Select Amenity');

        // commonAjax.initSelect2('#amenityCategory', 'Select Amenity Category');

        // let category_id = <?= $data['row']->category_id ?? '0' ?>

        // commonAjax.loadAmenityCategory(category_id);

        let selectedBrand = "{{ $data['row']->brand_id ?? '' }}";
        commonAjax.loadBrandList(selectedBrand);

        let model_id = "{{ $data['row']->model_id ?? '' }}";
        commonAjax.loadBusModelsList(model_id);

        $('#brand').on('change', function() {

            let brandId = $(this).val();

            // Reset model dropdown
            $('#model').html('<option value="">Select Model</option>');

            if (brandId) {
                // Load models based on selected brand
                commonAjax.loadBusModelsList('', brandId);
            }
        });

        let axle_id = "{{ $data['row']->axle_id ?? '' }}";
        commonAjax.loadAxleTypeList(axle_id);

        let bus_service_id = "{{ $data['row']->bus_service_id ?? '' }}";
        commonAjax.loadBusServicesList(bus_service_id);

        let seat_type_id = "{{ $data['row']->seat_type_id ?? '' }}";
        commonAjax.loadSeatTypeList(seat_type_id);

        let seat_layout_id = "{{ $data['row']->seat_layout_id ?? '' }}";
        commonAjax.loadSeatLayoutList(seat_layout_id);

        let annexture_type_id = "{{ $data['row']->annexture_type_id ?? '' }}";
        commonAjax.loadAnnextureList('AC_TYPE', annexture_type_id);

         commonAjax.loadAmenityList();
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.selectDropdown('amenityCategory', 'Select Amenity Category'))
            return false;

        if (!validator.blankCheck('amenity_name', 'Amenity Name cannot be left blank'))
            return false;
        if (!validator.maxLength('amenity_name', 100, 'Amenity Name'))
            return false;

        if (!validator.blankCheck('icon', 'Icon Class cannot be left blank'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });


    function searchCity() {       

        let city = document.getElementById("citySearch").value;

        $.ajax({
            type: "POST",
            url: ajaxUrl + "get-city-search",
            data: {
                city: city,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",

            success: function(response) {

                let html = "";

                if (response.status && response.data.length > 0) {

                    $.each(response.data, function(index, c) {

                        html += `
                    <div class="form-check mb-2">
                        <input class="form-check-input cityCheck"
                               type="checkbox"
                               value="${c.city_name}"
                               onchange="toggleCity(this)">
                        <label class="form-check-label">${c.city_name}</label>
                    </div>`;
                    });

                } else {

                    html = `<p class="text-danger">No city found</p>`;

                }

                $("#cityList").html(html);

            }

        });

    }
</script>
@endpush