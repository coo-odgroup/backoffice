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
                                    <!-- ================= STEP 2 ================= -->
                                    <div id="step4">

                                        <!-- <h3 class="fw-bold mb-4 border-bottom pb-2">Add Station</h3> -->

                                        <div class="accordion" id="stationAccordion">

                                            <!-- Station 1 -->
                                            <div class="accordion-item">

                                                <h2 class="accordion-header">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#station1">
                                                        1. Baripada
                                                    </button>
                                                </h2>

                                                <div id="station1" class="accordion-collapse collapse show" data-bs-parent="#stationAccordion">

                                                    <div class="accordion-body">

                                                        <div class="stationRows">

                                                            <div class="row stationRow align-items-center">

                                                                <div class="col-md-1">
                                                                    <div class="checkbox">
                                                                        <input type="checkbox">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <select class="form-select form-select-sm">
                                                                        <option value="">Select</option>
                                                                        <option value="">Boarding</option>
                                                                        <option value="">Dropping</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <select class="form-select form-select-sm">
                                                                        <option>Select Station</option>
                                                                        <option>Balasore</option>
                                                                        <option>Soro</option>
                                                                        <option>Bhadrak</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-1">
                                                                    <input type="time" class="form-control form-control-sm" value="00:00">
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <button class="btn btn-primary btn-sm addRow">+</button>
                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>

                                            </div>


                                            <!-- Station 2 -->
                                            <div class="accordion-item">

                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#station2">
                                                        2. Soro
                                                    </button>
                                                </h2>

                                                <div id="station2" class="accordion-collapse collapse" data-bs-parent="#stationAccordion">

                                                    <div class="accordion-body">

                                                        <div class="stationRows">

                                                            <div class="row align-items-center stationRow">

                                                                <div class="col-md-1">
                                                                    <div class="checkbox">
                                                                        <input type="checkbox">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-1">
                                                                    <select class="form-select form-select-sm">
                                                                        <option value="">Select</option>
                                                                        <option value="">Boarding</option>
                                                                        <option value="">Dropping</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <select class="form-select form-select-sm">
                                                                        <option>Select Station</option>
                                                                        <option>Balasore</option>
                                                                        <option>Soro</option>
                                                                        <option>Bhadrak</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-1">
                                                                    <input type="time" class="form-control form-control-sm" value="00:00">
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <button class="btn btn-primary btn-sm addRow">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Station 3 -->
                                            <div class="accordion-item">

                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#station3">
                                                        3. Balasore
                                                    </button>
                                                </h2>

                                                <div id="station3" class="accordion-collapse collapse" data-bs-parent="#stationAccordion">

                                                    <div class="accordion-body">

                                                        <div class="stationRows">
                                                            <div class="row align-items-center stationRow">
                                                                <div class="col-md-1">
                                                                    <div class="checkbox">
                                                                        <input type="checkbox">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-1">
                                                                    <select class="form-select form-select-sm">
                                                                        <option value="">Select</option>
                                                                        <option value="">Boarding</option>
                                                                        <option value="">Dropping</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <select class="form-select form-select-sm">
                                                                        <option>Select Station</option>
                                                                        <option>Balasore</option>
                                                                        <option>Soro</option>
                                                                        <option>Bhadrak</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-1">
                                                                    <input type="time" class="form-control form-control-sm" value="00:00">
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <button class="btn btn-primary btn-sm addRow">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center mt-5">
                                            <button type="button" class="btn btn-warning px-5 rounded-pill me-3" onclick="backStep()">← Back</button>
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
    function backStep() {
        window.location.href = "/admin/bus/create/step4";
    }

    function nextStep() {
        window.location.href = "/admin/bus/create/step5";
    }

    // ADD / REMOVE CITY
    function toggleCity(checkbox) {

        let city = checkbox.value;
        let preview = document.getElementById("previewList");
        let cityId = "city_" + city.replace(/\s/g, '');

        if (checkbox.checked) {

            let div = document.createElement("div");
            div.className = "d-flex mb-2";
            div.id = cityId;
            div.draggable = true;

            div.innerHTML = `
            <input type="text" class="form-control me-2" value="${city}" readonly>
            <button class="btn btn-danger" onclick="removeCity('${city}')">
                <i class="fa fa-trash"></i>
            </button>
        `;

            addDragEvents(div);

            preview.appendChild(div);

        } else {

            let removeDiv = document.getElementById(cityId);
            if (removeDiv) {
                removeDiv.remove();
            }

        }

    }


    // REMOVE CITY BUTTON
    function removeCity(city) {

        let cityId = "city_" + city.replace(/\s/g, '');
        let div = document.getElementById(cityId);

        if (div) {
            div.remove();
        }

        let checkboxes = document.querySelectorAll(".cityCheck");

        checkboxes.forEach(function(cb) {
            if (cb.value === city) {
                cb.checked = false;
            }
        });

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