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
                                            <div class="col-md-8">
                                                <div class="p-3 border rounded bg-white">
                                                    <div class="row">

                                                        <div class="col-md-6 mb-2">
                                                            <label for="busOperator">Bus Operator <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm" name="bus_operator_id" id="busOperator">
                                                                <option value="0">Select Bus Operator</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="busName">Bus Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Bus Name" name="name" id="busName">
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="busNumber">Bus Number <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Bus Number" name="bus_number" id="busNumber">
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="via">Via</label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Via" name="via" id="via">
                                                        </div>

                                                        <!-- <div class="col-md-6 mb-2">
                                                            <label for="selAmenity">Amenities <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm" name="amenities_id[]" id="selAmenity" multiple>
                                                                <option>Select Amenities</option>
                                                            </select>
                                                        </div> -->

                                                        <div class="col-md-6 mb-2">
                                                            <label for="maxSeat">Max Seat Booked <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Max Seat" name="max_seat_book" id="maxSeat" value="6">
                                                        </div>

                                                    </div>
                                                    <div class="card mt-2">
                                                        <div class="card-body">
                                                            <label class="form-label fw-bold">Bus Type <span class="text-danger">*</span></label>
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
                                                                        <option value="0">Select Model</option>
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
                                                                    <input type="hidden" name="type" id="busTypeVal" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card mt-2">
                                                        <div class="card-body">
                                                            <label class="form-label fw-bold">Amenities <span class="text-danger">*</span></label>

                                                            <!-- Search -->
                                                            <input type="text" id="amenitySearch" class="form-control mb-3"
                                                                placeholder="Search amenities...">

                                                            <!-- Accordion -->
                                                            <div class="accordion" id="amenityAccordion"></div>

                                                            <!-- Selected Amenities -->
                                                            <div class="mt-3">
                                                                <label class="form-label">Selected Amenities:</label>
                                                                <div id="selectedAmenities" class="d-flex flex-wrap gap-2"></div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <!-- RIGHT SIDE (4 columns) -->
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded bg-white">
                                                    <div class="mb-2">
                                                        <label for="slab">Cancellation Slab<span class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm" id="slab" name="slab">
                                                            <option>Select Cancellation Slab</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Slab Details -->
                                                <div class="mt-3" id="slabDetails" style="display:none;">
                                                    <table class="table table-bordered table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Hours Before Departure</th>
                                                                <th>Cancellation Charges (%)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="slabTableBody"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- THIRD SECTION -->
                                    <div class="row">

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Is IRCTC Module <span class="text-danger">*</span></label>

                                            <div class="d-flex gap-3">

                                                <!-- YES -->
                                                <label class="radio-box">
                                                    <input type="radio" name="irctc_module" value="1">
                                                    <div class="box">
                                                        Yes
                                                    </div>
                                                </label>

                                                <!-- NO -->
                                                <label class="radio-box">
                                                    <input type="radio" name="irctc_module" value="0" checked>
                                                    <div class="box">
                                                        No
                                                    </div>
                                                </label>

                                            </div>
                                        </div>

                                        <!-- <div class="col-md-4 mb-2 d-flex align-items-center">
                                                <input type="checkbox" class="me-2">
                                                <label class="mb-0">Has IRCTC Module</label>
                                            </div> -->

                                    </div>

                                    <!-- BUTTON -->
                                    <div class="text-center mt-4">
                                        <!-- <button type="button" class="btn btn-warning px-5 rounded-pill" onclick="nextStep()">Next →</button> -->
                                        <button type="submit" class="btn btn-warning px-5 rounded-pill">Next →</button>
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
@endsection
@push('scripts')

<script type="module">
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

    const selectedContainer = document.getElementById("selectedAmenities");

    // ✅ Handle checkbox change (DYNAMIC)
    document.addEventListener('change', function(e) {
        if (e.target.matches('.amenity-chip input')) {
            renderSelected();
        }
    });

    // ✅ Render selected tags
    function renderSelected() {
        selectedContainer.innerHTML = '';

        document.querySelectorAll('.amenity-chip input:checked')
            .forEach(cb => {
                const tag = document.createElement('div');
                tag.className = 'tag';

                tag.innerHTML = `
                ${cb.nextElementSibling ? cb.nextElementSibling.innerText : cb.parentElement.innerText.trim()}
                <span data-value="${cb.value}" class="remove-tag">&times;</span>
            `;

                selectedContainer.appendChild(tag);
            });
    }

    // ✅ Remove tag (DYNAMIC)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-tag')) {
            const value = e.target.getAttribute('data-value');

            document.querySelectorAll('.amenity-chip input')
                .forEach(cb => {
                    if (cb.value == value) {
                        cb.checked = false;
                    }
                });

            renderSelected();
        }
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('.onSelect').on('change', function() {
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
        $('#busTypeVal').val(fullName);
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

        // Jagan
        commonAjax.initSelect2('#busOperator', 'Select Bus Opeator');
        let bus_operator_id = "{{ $data['row']->bus_operator_id ?? '' }}";
        commonAjax.loadBusOperatorList(bus_operator_id);

        commonAjax.initSelect2('#slab', 'Select Cancellation Slab');
        let slab_id = "{{ $data['row']->slab_id ?? '' }}";
        commonAjax.loadCancellationslabList(slab_id);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.selectDropdown('busOperator', 'Select Bus Operator'))
            return false;

        if (!validator.blankCheck('busName', 'bus Name cannot be left blank'))
            return false;
        if (!validator.maxLength('busName', 100, 'bus Name'))
            return false;

        if (!validator.blankCheck('busNumber', 'bus Number cannot be left blank'))
            return false;
        if (!validator.maxLength('busNumber', 100, 'bus Number'))
            return false;

        if (!validator.selectDropdown('slab', 'Select Cancellation slab'))
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

    let timer;

    // ✅ Store selected amenities globally
    let selectedAmenities = new Set();

    $('#amenitySearch').on('keyup', function() {

        clearTimeout(timer);

        let search = $(this).val();

        timer = setTimeout(function() {

            // ✅ If empty → clear accordion
            if (search.length < 1) {
                $('#amenityAccordion').html('');
                return;
            }

            // ✅ Show loader
            $('#amenityAccordion').html('<p class="text-muted">Searching...</p>');

            $.ajax({
                url: '/admin/search-amenities',
                type: 'GET',
                data: {
                    search: search
                },

                success: function(res) {

                    let html = '';

                    // ✅ Empty state
                    if (res.length === 0) {
                        $('#amenityAccordion').html('<p class="text-muted">No amenities found</p>');
                        return;
                    }

                    // ✅ Build accordion
                    res.forEach(function(category, index) {

                        let collapseId = `cat${category.id}_${index}`;

                        html += `
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#${collapseId}">
                                    ${category.category_name}
                                </button>
                            </h2>

                            <input type="hidden" name="category_id[]" value="${category.id}">

                            <div id="${collapseId}" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <div class="row g-1">
                        `;

                        category.amenities.forEach(function(amenity) {

                            let checked = selectedAmenities.has(String(amenity.id)) ? 'checked' : '';

                            html += `
                                <div class="col-md-3">
                                    <label class="amenity-chip">
                                        <input type="checkbox" name="amenities_id[]" value="${amenity.id}" ${checked}>
                                        <span class="amenity-label">${amenity.amenity_name}</span>
                                    </label>
                                </div>
                            `;
                        });

                        html += `
                                </div>
                            </div>
                        </div>
                    </div>`;
                    });

                    $('#amenityAccordion').html(html);
                },

                error: function() {
                    $('#amenityAccordion').html('<p class="text-danger">Something went wrong</p>');
                }
            });

        }, 300);

    });


    // ✅ Track selections (VERY IMPORTANT)
    $(document).on('change', '.amenity-chip input', function() {

        let val = $(this).val();

        if ($(this).is(':checked')) {
            selectedAmenities.add(val);
        } else {
            selectedAmenities.delete(val);
        }
    });

    $('#slab').on('change', function() {

        let slabId = $(this).val();

        if (!slabId) {
            $('#slabDetails').hide();
            return;
        }

        $.ajax({
            url: '/admin/get-slab-details',
            type: 'GET',
            data: {
                slab_id: slabId
            },
            success: function(res) {

                let tableHtml = '';

                res.forEach((row, i) => {
                    tableHtml += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${row.hours}</td>
                        <td>${row.charge} %</td>
                    </tr>
                `;
                });

                $('#slabTableBody').html(tableHtml);
                $('#slabDetails').show();
            }
        });
    });
</script>
@endpush