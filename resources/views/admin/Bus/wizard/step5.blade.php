@extends('admin.layouts.master')
@section('page_title', 'City Timings')
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

                                    <div id="step5">

                                        <h3 class="fw-bold mb-4 border-bottom pb-2">Bus Schedule</h3>

                                        <div class="table-responsive">

                                            <table class="table align-middle">

                                                <thead class="border-bottom">
                                                    <tr class="text-left">
                                                        <th></th>
                                                        <th>Source</th>
                                                        <th>Days</th>
                                                        <th>To</th>
                                                        <th>Days</th>
                                                        <th>Seat Fare</th>
                                                        <th>U-Sleeper</th>
                                                        <th>L-Sleeper</th>
                                                        <th>Seize Time</th>
                                                        <th>Close Time</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                    <!-- Row 1 -->
                                                    <tr class="text-center">

                                                        <td>1.</td>

                                                        <td>
                                                            <input type="text" class="form-control" value="Baripada">
                                                        </td>

                                                        <td>
                                                            <select class="form-select">
                                                                <option>0</option>
                                                                <option>1</option>
                                                                <option>2</option>
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <input type="text" class="form-control" value="Baripada">
                                                        </td>

                                                        <td>
                                                            <select class="form-select">
                                                                <option>0</option>
                                                                <option>1</option>
                                                                <option>2</option>
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <input type="number" class="form-control" value="650.00">
                                                        </td>

                                                        <td>
                                                            <input type="number" class="form-control" value="750.00">
                                                        </td>

                                                        <td>
                                                            <input type="number" class="form-control" value="750.00">
                                                        </td>

                                                        <td>
                                                            <input type="number" class="form-control" value="255">
                                                        </td>

                                                        <td>
                                                            <input type="time" class="form-control" value="16:00">
                                                        </td>

                                                        <td>

                                                            <div class="d-flex justify-content-center gap-2">

                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox">
                                                                </div>

                                                                <button class="btn btn-outline-danger btn-sm">
                                                                    ✕
                                                                </button>

                                                            </div>

                                                        </td>

                                                    </tr>


                                                    <!-- Row 2 -->
                                                    <tr class="text-center">

                                                        <td>2.</td>

                                                        <td><input type="text" class="form-control" value="Balasore"></td>

                                                        <td>
                                                            <select class="form-select">
                                                                <option>2</option>
                                                                <option>1</option>
                                                            </select>
                                                        </td>

                                                        <td><input type="text" class="form-control" value="Baripada"></td>

                                                        <td>
                                                            <select class="form-select">
                                                                <option>1</option>
                                                                <option>2</option>
                                                            </select>
                                                        </td>

                                                        <td><input type="number" class="form-control" value="650.00"></td>
                                                        <td><input type="number" class="form-control" value="750.00"></td>
                                                        <td><input type="number" class="form-control" value="750.00"></td>
                                                        <td><input type="number" class="form-control" value="255"></td>
                                                        <td><input type="time" class="form-control" value="16:00"></td>

                                                        <td>

                                                            <div class="d-flex justify-content-center gap-2">

                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox">
                                                                </div>

                                                                <button class="btn btn-outline-danger btn-sm">
                                                                    ✕
                                                                </button>

                                                            </div>

                                                        </td>

                                                    </tr>

                                                </tbody>

                                            </table>

                                        </div>


                                        <!-- Buttons -->
                                        <div class="text-center mt-5">

                                            <button type="button" class="btn btn-warning px-5 rounded-pill me-4" onclick="backStep()">
                                                Back
                                            </button>

                                            <button type="button" class="btn btn-warning px-5 rounded-pill" onclick="nextStep()">
                                                Next
                                            </button>

                                        </div>

                                    </div>

                                </div>

                                <!-- BUTTONS -->
                                <!-- <div class="row mt-4">
                                    <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            {{ $data['strSubmit'] }}
                                        </button>
                                        @if($data['strReset'] == 'Cancel')
                                        <a href="{{ route('amenities.index') }}" class="btn btn-secondary btn-sm">
                                            {{ $data['strReset'] }}
                                        </a>
                                        @else
                                        <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                            {{ $data['strReset'] }}
                                        </button>
                                        @endif
                                    </div>
                                </div> -->
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
        document.getElementById("step1").style.display = "block";
        document.getElementById("step2").style.display = "none";
    }

    function nextStep() {
        window.location.href = "/admin/bus/create/step6";
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
            </button>`;

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


    // DRAG SORT FUNCTION
    let dragItem = null;

    function addDragEvents(element) {

        element.addEventListener("dragstart", function() {
            dragItem = element;
        });

        element.addEventListener("dragover", function(e) {
            e.preventDefault();
        });

        element.addEventListener("drop", function(e) {
            e.preventDefault();

            if (dragItem !== element) {

                let parent = element.parentNode;

                let items = [...parent.children];
                let dragIndex = items.indexOf(dragItem);
                let dropIndex = items.indexOf(element);

                if (dragIndex < dropIndex) {
                    parent.insertBefore(dragItem, element.nextSibling);
                } else {
                    parent.insertBefore(dragItem, element);
                }
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

    $('.onSelect').on('change', function() {
        generateBusName();
    });

    function generateBusName() {

        const brandVal = $('#brand').val();
        const modelVal = $('#busModel').val();
        const axleVal = $('#axleType').val();
        const serviceVal = $('#busService').val();
        const acVal = $('.annexture').val();
        const seatVal = $('#seatType').val();
        // const layoutVal   = $('#seatLayout').val();

        // Check ALL selected
        // if (brandVal && modelVal && axleVal && serviceVal && acVal && seatVal && layoutVal) {
        if (brandVal && modelVal && axleVal && serviceVal && acVal && seatVal) {

            const brand = $('#brand option:selected').text();
            const model = $('#busModel option:selected').text();
            const axle = $('#axleType option:selected').text();
            const service = $('#busService option:selected').text();
            const ac = $('.annexture option:selected').text();
            const seatType = $('#seatType option:selected').text();
            const layout = $('#seatLayout option:selected').text();

            // const fullName = `${brand} ${model} ${axle} ${service} ${ac} ${seatType} ${layout}`;
            const fullName = `${brand} ${model} ${axle} ${service} ${ac} ${seatType}`;

            $('#busType').html(fullName);

        } else {
            // Clear if not all selected
            $('#busType').html('');
        }
    }

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
</script>
@endpush