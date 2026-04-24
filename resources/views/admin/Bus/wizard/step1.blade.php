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
        <a href="{{ route('bus.index') }}" class="btn btn-success btn-sm">
            View Bus List
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
                                                            <select class="form-select form-select-sm users" name="bus_operator_id" id="busOperator">
                                                                <option value="0">Select Bus Operator</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="busName">Bus Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Bus Name" name="name" id="busName" maxlength="100" value="{{@$step1Res->name}}">
                                                            <small class="text-muted char-counter float-end"></small>
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="busNumber">Bus Number <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Bus Number" name="bus_number" id="busNumber" maxlength="20" value="{{@$step1Res->bus_number}}">
                                                            <small class="text-muted char-counter float-end"></small>
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="via">Via</label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Via" name="via" id="via" maxlength="50" value="{{@$step1Res->via}}">
                                                            <small class="text-muted char-counter float-end"></small>
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label for="maxSeat">Max Seat Booked <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm clearable" placeholder="Max Seat" name="max_seat_book" id="maxSeat" value="{{@$step1Res->max_seat_book ? $step1Res->max_seat_book : 6 }}">
                                                        </div>

                                                    </div>
                                                    <div class="card mt-2">
                                                        <div class="card-body">
                                                            <label class="form-label fw-bold">Bus Type <span class="text-danger">*</span></label>
                                                            <!-- SECOND SECTION -->
                                                            <div class="row">

                                                                <div class="col-md-3 mb-2">
                                                                    <label for="brand">Brand</label>
                                                                    <select class="form-select form-select-sm onSelect" id="brand" name="brand_id">
                                                                        <option value="0">Select Brand</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-3 mb-2">
                                                                    <label for="busModel">Model</label>
                                                                    <select class="form-select form-select-sm onSelect" id="busModel" name="model_id">
                                                                        <option value="0">Select Model</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-3 mb-2">
                                                                    <label for="axleType">Axle Type</label>
                                                                    <select class="form-select form-select-sm onSelect" id="axleType" name="axle_type_id">
                                                                        <option value="0">Select Axle Type</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-3 mb-2">
                                                                    <label for="busService">Bus Service</label>
                                                                    <select class="form-select form-select-sm onSelect" id="busService" name="service_id">
                                                                        <option value="0">Select Bus Service</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2 mb-1">
                                                                    <label for="acType">AC Type <span class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm onSelect annexture" id="acType" name="ac_type_id">
                                                                        <option value="0">Select AC Type</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2 mb-1">
                                                                    <label for="seatType">Seat Type <span class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm onSelect" id="seatType" name="seat_type_id">
                                                                        <option value="0">Select Seat Type</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2 mb-1">
                                                                    <label for="seatLayout">Seat Layout <span class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm onSelect" id="seatLayout" name="seat_layout_type_id">
                                                                        <option value="0">Select Seat Layout</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6 mb-1">
                                                                    <label for="busType">Bus Type<span class="text-danger">*</span></label>
                                                                    <span id="busType">{{ @$step1Res->gen_bus_type ?? '' }}</span>
                                                                    <input type="hidden" name="gen_bus_type" id="busTypeVal" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card mt-2">
                                                        <div class="card-body">
                                                            <label class="form-label fw-bold">Amenities <span class="text-danger">*</span></label>

                                                            <!-- Search -->
                                                            <input type="text" id="amenitySearch" class="form-control mb-3 clearable"
                                                                placeholder="Search amenities...">

                                                            <!-- Accordion -->
                                                            <div class="accordion" id="amenityAccordion">
                                                                @foreach($data['categories'] as $catIndex => $category)

                                                                @php
                                                                $collapseId = 'cat' . $category->id . '_' . $catIndex;
                                                                @endphp

                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header">
                                                                        <button class="accordion-button collapsed" type="button"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#{{ $collapseId }}">
                                                                            {{ $category->category_name }}
                                                                        </button>
                                                                    </h2>

                                                                    <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                                                                        data-bs-parent="#amenityAccordion">

                                                                        <div class="accordion-body">
                                                                            <div class="row g-1">

                                                                                @foreach($category->amenities as $amenity)
                                                                                <div class="col-md-3">
                                                                                    <label class="amenity-chip">
                                                                                        <input type="checkbox" class="amenity-checkbox" name="amenities_id[]"
                                                                                            value="{{ $amenity->id }}" {{ in_array($amenity->id, $selectedAmenities ?? []) ? 'checked' : '' }}>

                                                                                        <span class="amenity-label">
                                                                                            {{ $amenity->amenity_name }}
                                                                                        </span>
                                                                                    </label>
                                                                                </div>
                                                                                @endforeach

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @endforeach
                                                            </div>

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

                                    </div>

                                    <!-- BUTTON -->
                                    <div class="text-center mt-4">
                                        <input type="hidden" name="bus_id" value="{{$data['bus_id']}}">
                                        <input type="hidden" name="param" value="{{$data['param']}}">
                                        <input type="hidden" name="param2" value="{{$data['param2']}}">
                                        @if ($data['param2'] == 'edit')
                                        <button type="submit" class="btn btn-success px-5 rounded-pill">Update & Continue →</button>
                                        @else
                                        @if ($data['bus_id']!=0)
                                        <a href="{{ url($createBusUrl.'step2/'.$data['enc_bus_id'].'/save') }}" class="btn btn-warning px-5 rounded-pill me-3">
                                            Continue →
                                        </a>
                                        @endif
                                        <button type="submit" class="btn btn-success px-5 rounded-pill">Save & Continue →</button>
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
    </div>
</form>
@endsection
@push('scripts')

<script type="module">
    $(document).ready(function() {
        localStorage.removeItem('selAmenities');
        const selAmenities = <?= json_encode(@$step1AmenityRes) ?>;
        if (selAmenities && selAmenities.length > 0) {
            localStorage.setItem('selAmenities', JSON.stringify(selAmenities));
        }

        let on_edit_slab_id = "{{ @$step1Res->cancellationslabs_id ?? '' }}";
        let on_edit_bus_id = "{{ @$step1Res->id ?? '' }}";

        if (on_edit_bus_id && on_edit_slab_id) {
            loadSlabDetails(on_edit_slab_id);
        }
        // commonAjax.initClearableInputs();
        commonAjax.initCharCounter(['busName', 'busNumber', 'via']);
        commonAjax.makeUpperCase(['busName', 'busNumber']); // Ids
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

    $(document).ready(async function() {

        try {
            commonAjax.initClearableInputs();

            commonAjax.initSelect2('.users', 'Select Bus Operator');

            let user_id = "{{ @$step1Res->bus_operator_id ?? '' }}";

            await commonAjax.loadUsersList('OPERATOR', user_id);

            commonAjax.initSelect2('#brand', 'Select Brand');
            commonAjax.initSelect2('#busModel', 'Select Model');
            commonAjax.initSelect2('#axleType', 'Select Axxle Type');
            commonAjax.initSelect2('#busService', 'Select Bus Service');
            commonAjax.initSelect2('.annexture', 'Select AC Type');
            commonAjax.initSelect2('#seatType', 'Select Seat Type');
            commonAjax.initSelect2('#seatLayout', 'Select Seat Layout');
            commonAjax.initSelect2('#selAmenity', 'Select Amenity');

            let selectedBrand = "{{ @$step1Res->brand_id ?? '' }}";
            await commonAjax.loadBrandList(selectedBrand);

            let model_id = "{{ @$step1Res->model_id ?? '' }}";
            await commonAjax.loadBusModelsList(model_id);

            $('#brand').on('change', function() {
                let brandId = $(this).val();
                $('#model').html('<option value="">Select Model</option>');

                if (brandId) {
                    commonAjax.loadBusModelsList('', brandId);
                }
            });

            let axle_type_id = "{{ @$step1Res->axle_type_id ?? '' }}";
            await commonAjax.loadAxleTypeList(axle_type_id);

            let service_id = "{{ @$step1Res->service_id ?? '' }}";
            await commonAjax.loadBusServicesList(service_id);

            let seat_type_id = "{{ @$step1Res->seat_type_id ?? '' }}";
            await commonAjax.loadSeatTypeList(seat_type_id);

            let seat_layout_id = "{{ @$step1Res->seat_layout_type_id ?? '' }}";
            await commonAjax.loadSeatLayoutList(seat_layout_id);

            let annexture_type_id = "{{ @$step1Res->ac_type_id ?? '' }}";
            await commonAjax.loadAnnextureList('AC_TYPE', annexture_type_id);

            await commonAjax.loadAmenityList();

            commonAjax.initSelect2('#slab', 'Select Cancellation Slab');

            let slab_id = "{{ @$step1Res->cancellationslabs_id ?? '' }}";
            await commonAjax.loadCancellationslabList(slab_id);

        } catch (error) {
            console.error('Error loading data:', error);
        }

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
        if (!validator.maxLength('busNumber', 20, 'bus Number'))
            return false;

        if (!validator.selectDropdown('slab', 'Select Cancellation slab'))
            return false;

        if (!validator.selectDropdown('acType', 'Select Ac Type'))
            return false;

        if (!validator.selectDropdown('seatType', 'Select Seat Type'))
            return false;

        if (!validator.selectDropdown('seatLayout', 'Select Seat Layout'))
            return false;

        let selAmenities = [];

        try {
            selAmenities = JSON.parse(localStorage.getItem('selAmenities'));
        } catch (e) {
            selAmenities = [];
        }

        console.log(selAmenities);

        if (selAmenities.length == 0) {
            commonAjax.viewAlert("Please select at least 1 amenities");
            return false;
        }

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    $('#slab').on('change', function() {
        let slabId = $(this).val();
        loadSlabDetails(slabId);
    });

    function loadSlabDetails(slabId) {

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
    }

    let timer;

    let selAmenities = JSON.parse(localStorage.getItem('selAmenities')) || [];

    let selectedAmenities = new Set(selAmenities.map(item => item[0]));

    const selectedContainer = document.getElementById("selectedAmenities");


    // =========================
    // 🔍 SEARCH + ACCORDION
    // =========================
    $('#amenitySearch').on('keyup', function() {

        clearTimeout(timer);

        let search = $(this).val();

        timer = setTimeout(function() {

            // ✅ FIX: don't return, allow empty search
            if (search.length < 1) {
                search = ''; // send empty to backend
            }

            $('#amenityAccordion').html('<p class="text-muted">Searching...</p>');

            $.ajax({
                url: '/admin/search-amenities',
                type: 'GET',
                data: {
                    search: search
                },

                success: function(res) {

                    let html = '';

                    if (res.length === 0) {
                        $('#amenityAccordion').html('<p class="text-muted">No amenities found</p>');
                        return;
                    }

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

                            <div id="${collapseId}" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <div class="row g-1">
                        `;

                        category.amenities.forEach(function(amenity) {

                            let checked = selectedAmenities.has(String(amenity.id)) ? 'checked' : '';

                            html += `
                                <div class="col-md-3">
                                    <label class="amenity-chip">
                                        <input type="checkbox" class="amenity-checkbox"
                                            value="${amenity.id}" ${checked}>
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

    // =========================
    // ✅ CHECKBOX CHANGE
    // =========================
    $(document).on('change', '.amenity-checkbox', function() {

        let id = $(this).val();
        let name = $(this).siblings('.amenity-label').text().trim();

        if ($(this).is(':checked')) {

            selectedAmenities.add(id);

            if (!selAmenities.some(item => item[0] == id)) {
                selAmenities.push([id, name]);
            }

        } else {

            selectedAmenities.delete(id);
            selAmenities = selAmenities.filter(item => item[0] != id);
        }

        // 💾 Save
        localStorage.setItem('selAmenities', JSON.stringify(selAmenities));

        // 🎯 Update tags
        renderSelected();
    });


    // =========================
    // 🏷️ RENDER TAGS
    // =========================
    function renderSelected() {

        selectedContainer.innerHTML = '';

        selAmenities.forEach(item => {

            let tag = document.createElement('div');
            tag.className = 'tag';

            tag.innerHTML = `
            ${item[1]}
            <span data-value="${item[0]}" class="remove-tag">&times;</span>
        `;

            selectedContainer.appendChild(tag);
        });
    }


    // =========================
    // ❌ REMOVE TAG
    // =========================
    document.addEventListener('click', function(e) {

        if (e.target.classList.contains('remove-tag')) {

            let value = e.target.getAttribute('data-value');

            // Remove from memory
            selectedAmenities.delete(value);
            selAmenities = selAmenities.filter(item => item[0] != value);

            // Uncheck checkbox if present
            document.querySelectorAll('.amenity-checkbox').forEach(cb => {
                if (cb.value == value) {
                    cb.checked = false;
                }
            });

            // Save again
            localStorage.setItem('selAmenities', JSON.stringify(selAmenities));

            renderSelected();
        }
    });


    // =========================
    // 🚀 INIT (PAGE LOAD)
    // =========================
    $(document).ready(function() {
        renderSelected();
    });
</script>
@endpush