@extends('admin.layouts.master')
@section('page_title', 'Add Stations')
@section('content')

<style>
    #previewList .d-flex {
        cursor: move;
    }
</style>

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
                                    <div id="step4">

                                        <div class="accordion" id="stationAccordion"></div>

                                        <div class="text-center mt-5">
                                            <input type="hidden" name="bus_id" value="{{$data['bus_id']}}">
                                            <input type="hidden" name="param" value="{{$data['param']}}">
                                            <input type="hidden" name="param2" value="{{$data['param2']}}">
                                            @php
                                            $isSave = ($data['param'] ?? null) === 'save';
                                            $isBack = ($data['param2'] ?? null) === 'back';
                                            @endphp

                                            @if ($isSave)
                                            <a href="{{ url($createBusUrl.'step3/'.$data['enc_bus_id'].'/save/back') }}"
                                                class="btn btn-secondary px-5 rounded-pill me-3">
                                                ← Back
                                            </a>
                                            @endif

                                            @if ($isSave && $isBack)
                                            <a href="{{ url($createBusUrl.'step5/'.$data['enc_bus_id'].'/save') }}"
                                                class="btn btn-warning px-5 rounded-pill me-3">
                                                Continue →
                                            </a>
                                            @endif

                                            <button type="submit" class="btn btn-success px-5 rounded-pill">
                                                Save & Continue →
                                            </button>
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
    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        let isValid = true;

        // Type validation
        $('.typeSelect').each(function() {
            if (!$(this).val()) {
                commonAjax.viewAlert('Type cannot be left blank');
                isValid = false;
                return false;
            }
        });
        if (!isValid) return false;

        // Station validation
        $('.stationSelect').each(function() {
            if (!$(this).val()) {
                commonAjax.viewAlert('Station cannot be left blank');
                isValid = false;
                return false;
            }
        });
        if (!isValid) return false;

        // Time validation
        $('.time').each(function() {
            if (!$(this).val()) {
                commonAjax.viewAlert('Time cannot be left blank');
                isValid = false;
                return false;
            }
        });
        if (!isValid) return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    function generateOptions(data) {
        let html = '';

        data.forEach(function(item) {
            html += `<option value="${item.id}">${item.brd_drp_point}</option>`;
        });

        return html;
    }

    function renderStations(data, step4Res) {

        // console.log(step4Res);

        if (!data.length) {
            $("#stationAccordion").html('<div class="alert alert-warning">No stations found</div>');
            return;
        }

        let html = '';

        data.forEach((station, index) => {

            let id = station[0]; // station id
            let name = station[1];
            let collapseId = 'station' + (index + 1);

            let cityData = step4Res.filter(item => item.city_id == id);

            let rowsHtml = '';

            if (cityData.length > 0) {

                cityData.forEach((item, i) => {

                    console.log(item);

                    rowsHtml += `
                    <div class="row stationRow align-items-center mb-2">

                        <div class="col-md-1">
                            <input type="checkbox" name="stations[${id}][${i}][checked]"
                                ${item.active_status == 1 ? 'checked' : ''}>
                        </div>

                        <div class="col-md-2">
                            <select class="form-select form-select-sm typeSelect" name="stations[${id}][${i}][type]">

                                <option value="">Select Type</option>
                                <option value="1" ${item.type == 1 ? 'selected' : ''}>Boarding</option>
                                <option value="2" ${item.type == 2 ? 'selected' : ''}>Dropping</option>

                            </select>
                        </div>

                        <div class="col-md-4">
                            <select class="form-select form-select-sm stationSelect" name="stations[${id}][${i}][stop_id]">

                                <option value="">Select Station</option>
                                <option value="${item.stop_id}" ${item.stop && item.stop.id == item.stop_id ? 'selected' : ''}>
                                    ${item.stop ? item.stop.city_name : ''}
                                </option>

                            </select>
                        </div>

                        <div class="col-md-2">
                            <input type="time" name="stations[${id}][${i}][time]" value="${item.timing.substring(0,5)}" class="form-control form-control-sm time">
                        </div>

                        <div class="col-md-2">
                            <input type="hidden" value="${id}" class="cityId">
                            ${ (cityData.length === 1 || i === 0) ? `
                                <button class="btn btn-primary btn-sm addRow" data-station="${id}" type="button">+</button>
                            ` : `
                                <button class="btn btn-danger btn-sm removeRow" type="button">-</button>
                            `}
                        </div>

                    </div>
                    `;
                });

            } else {

                rowsHtml = `
                <div class="row stationRow align-items-center mb-2">

                    <div class="col-md-1">
                        <input type="checkbox" name="stations[${id}][0][checked]">
                    </div>

                    <div class="col-md-2">
                        <select class="form-select form-select-sm typeSelect" name="stations[${id}][0][type]">
                            <option value="">Select Type</option>
                            <option value="1">Boarding</option>
                            <option value="2">Dropping</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <select class="form-select form-select-sm stationSelect" name="stations[${id}][0][stop_id]">
                            <option>Select Station</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <input type="time" name="stations[${id}][0][time]" class="form-control form-control-sm time">
                    </div>

                    <div class="col-md-2">
                        <input type="hidden" value="${id}" class="cityId">
                        <button class="btn btn-primary btn-sm addRow" data-station="${id}" type="button">+</button>
                    </div>

                </div>
                `;
            }

            html += `
            <div class="accordion-item">

                <h2 class="accordion-header">
                    <button class="accordion-button ${index !== 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                        ${index + 1}. ${name}
                    </button>
                </h2>

                <div id="${collapseId}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" data-bs-parent="#stationAccordion">

                    <div class="accordion-body">

                        <div class="stationRows">

                            ${rowsHtml}

                        </div>

                    </div>
                </div>
            </div>
            `;
        });

        $("#stationAccordion").html(html);
    }

    // ✅ Add Row
    $(document).on("click", ".addRow", function() {

        let container = $(this).closest(".stationRows");
        let stationId = $(this).data("station");

        let rowIndex = container.find(".stationRow").length;
        let data = JSON.parse(localStorage.getItem("selCities") || "[]");

        let newRow = `
        <div class="row stationRow align-items-center mb-2">

            <div class="col-md-1">
                <input type="checkbox" name="stations[${stationId}][${rowIndex}][checked]">
            </div>

            <div class="col-md-2">
                <select class="form-select form-select-sm typeSelect" name="stations[${stationId}][${rowIndex}][type]">
                    <option value="">Select Type</option>
                    <option value="1">Boarding</option>
                    <option value="2">Dropping</option>
                </select>
            </div>

            <div class="col-md-4">
                <select class="form-select form-select-sm stationSelect" name="stations[${stationId}][${rowIndex}][stop_id]">
                    <option>Select Station</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="time" name="stations[${stationId}][${rowIndex}][time]" class="form-control form-control-sm time">
            </div>

            <div class="col-md-2">
                <input type="hidden" value="${stationId}" class="cityId">
                <button class="btn btn-danger btn-sm removeRow" type="button">-</button>
            </div>

        </div>
        `;

        container.append(newRow);
    });

    // ✅ Remove Row
    $(document).on("click", ".removeRow", function() {
        $(this).closest(".stationRow").remove();
    });

    // ✅ Load from localStorage ONLY
    $(document).ready(function() {

        let selCities = JSON.parse(localStorage.getItem("selCities") || "[]");
        let step4Res = <?= json_encode($step4Res) ?>;

        renderStations(selCities, step4Res);
    });

    $(document).on("change", ".typeSelect", function() {

        let type = $(this).val();

        let row = $(this).closest(".stationRow");
        let checkbox = row.find('input[type="checkbox"]');
        let stationDropdown = row.find(".stationSelect");
        let city_id = row.find(".cityId").val();

        if (type === "1" || type === "2") {
            checkbox.prop("checked", true);
        } else {
            checkbox.prop("checked", false);
        }

        stationDropdown.html('<option value="">Select Station</option>');

        if (type !== "") {

            stationDropdown.html('<option value="">Loading...</option>');

            $.ajax({
                url: '/admin/get-boarding-dropping',
                type: 'GET',
                data: {
                    type: type,
                    city_id: city_id
                },

                success: function(response) {

                    let options = '<option value="">Select Station</option>';
                    options += generateOptions(response);

                    stationDropdown.html(options);
                },

                error: function() {
                    stationDropdown.html('<option value="">Error loading data</option>');
                }
            });
        }
    });
</script>
@endpush
