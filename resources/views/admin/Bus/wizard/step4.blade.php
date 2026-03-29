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
                                        </div>

                                        <div class="text-center mt-5">
                                            <button type="button" class="btn btn-warning px-5 rounded-pill me-3" onclick="backStep()">← Back</button>
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

<script>
    function backStep() {
        window.location.href = "/admin/bus/create/step4";
    }

    function nextStep() {
        window.location.href = "/admin/bus/create/step5";
    }
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

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    // ✅ Generate dropdown options
    function generateOptions(data) {
        let options = '';
        data.forEach(item => {
            options += `<option value="${item[0]}">${item[1]}</option>`;
        });
        return options;
    }

    // ✅ Render Accordion
    function renderStations(data) {

        if (!data.length) {
            $("#stationAccordion").html('<div class="alert alert-warning">No stations found in localStorage</div>');
            return;
        }

        let html = '';

        data.forEach((station, index) => {

            let id = station[0];
            let name = station[1];
            let collapseId = 'station' + (index + 1);

            html += `
            <div class="accordion-item">

                <h2 class="accordion-header">
                    <button class="accordion-button ${index !== 0 ? 'collapsed' : ''}" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#${collapseId}">
                        ${index + 1}. ${name}
                    </button>
                </h2>

                <div id="${collapseId}" 
                    class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                    data-bs-parent="#stationAccordion">

                    <div class="accordion-body">

                        <div class="stationRows">

                            <div class="row stationRow align-items-center mb-2">

                                <div class="col-md-1">
                                    <input type="checkbox" class="form-check-input">
                                </div>

                                <div class="col-md-2">
                                    <select class="form-select form-select-sm">
                                        <option value="">Select</option>
                                        <option value="boarding">Boarding</option>
                                        <option value="dropping">Dropping</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <select class="form-select form-select-sm">
                                        <option>Select Station</option>
                                        ${generateOptions(data)}
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <input type="time" class="form-control form-control-sm" value="00:00">
                                </div>

                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary btn-sm addRow">+</button>
                                </div>

                            </div>

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
        let data = JSON.parse(localStorage.getItem("selectedCities") || "[]");

        let newRow = `
        <div class="row stationRow align-items-center mb-2">

            <div class="col-md-1">
                <input type="checkbox" class="form-check-input">
            </div>

            <div class="col-md-2">
                <select class="form-select form-select-sm">
                    <option value="">Select</option>
                    <option value="boarding">Boarding</option>
                    <option value="dropping">Dropping</option>
                </select>
            </div>

            <div class="col-md-4">
                <select class="form-select form-select-sm">
                    <option>Select Station</option>
                    ${generateOptions(data)}
                </select>
            </div>

            <div class="col-md-2">
                <input type="time" class="form-control form-control-sm" value="00:00">
            </div>

            <div class="col-md-2">
                <button class="btn btn-danger btn-sm removeRow">-</button>
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

        let selectedCities = JSON.parse(localStorage.getItem("selectedCities") || "[]");

        renderStations(selectedCities);
    });
</script>
@endpush