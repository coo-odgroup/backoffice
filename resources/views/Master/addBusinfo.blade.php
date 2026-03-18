@extends('admin.layouts.master')
@section('page_title', 'BusInfo')
@section('content')

<style>
    #previewList .d-flex {
        cursor: move;
    }
</style>

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

                                    <!-- ================= STEP 1 ================= -->
                                    <div id="step1">

                                        <!-- ROW 1 (8 + 4) -->
                                        <div class="row mb-1">


                                            <!-- LEFT SIDE (8 columns) -->
                                            <div class="col-md-9">
                                                <div class="p-3 border rounded bg-white">
                                                    <div class="row">

                                                        <div class="col-md-6 mb-3">
                                                            <label for="selOpeator">Operator<span class="text-danger">*</span></label>
                                                            <select class="form-select" name="selOpeator" id="selOpeator">
                                                                <option value="0">Select Operator</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="busName">Bus Name<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" placeholder="Bus Name" name="busName" id="busName">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="busNumber">Bus Number<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" placeholder="Bus Number" name="busNumber" id="busNumber">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="via">Via<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" placeholder="Via" name="via" id="via">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="selAmenity">Amenities<span class="text-danger">*</span></label>
                                                            <select class="form-select" name="selAmenity" id="selAmenity">
                                                                <option>Select Amenities</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="maxSeat">Max Seat Booked<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" placeholder="Max Seat" name="maxSeat" id="maxSeat">
                                                        </div>

                                                    </div>
                                                    <hr class="wide">
                                                    <!-- SECOND SECTION -->
                                                    <div class="row">

                                                        <div class="col-md-3 mb-1">
                                                            <label for="brand">Brand</label>
                                                            <select class="form-select onSelect" id="brand" name="brand">
                                                                <option>Select Brand</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3 mb-1">
                                                            <label for="busModel">Model</label>
                                                            <select class="form-select onSelect" id="busModel" name="model">
                                                                <option>Select Model</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3 mb-1">
                                                            <label for="axleType">Axle Type</label>
                                                            <select class="form-select onSelect" id="axleType" name="axleType">
                                                                <option>Select Axle Type</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3 mb-1">
                                                            <label for="busService">Bus Service</label>
                                                            <select class="form-select onSelect" id="busService" name="busService">
                                                                <option>Select Bus Service</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-2 mb-1">
                                                            <label for="acType">AC Type<span class="text-danger">*</span></label>
                                                            <select class="form-select onSelect annexture" id="acType" name="acType">
                                                                <option>Select AC Type</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-2 mb-1">
                                                            <label for="seatType">Seat Type<span class="text-danger">*</span></label>
                                                            <select class="form-select onSelect" id="seatType" name="seatType">
                                                                <option>Select Seat Type</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-2 mb-1">
                                                            <label for="seatLayout">Seat Layout<span class="text-danger">*</span></label>
                                                            <select class="form-select onSelect" id="seatLayout" name="seatLayout">
                                                                <option>Select Seat Layout</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-2 mb-1">
                                                            <label for="busType">Bus Type<span class="text-danger">*</span></label>
                                                            <span id="busType"></span>
                                                        </div>

                                                    </div>

                                                    <hr class="wide">
                                                </div>
                                            </div>

                                            <!-- RIGHT SIDE (4 columns) -->
                                            <div class="col-md-3">
                                                <div class="p-3 border rounded bg-white">
                                                    <div class="mb-3">
                                                        <label>Cancellation Slab<span class="text-danger">*</span></label>
                                                        <select class="form-select">
                                                            <option>Select Slab</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- THIRD SECTION -->
                                        <div class="row">

                                            <div class="col-md-4 mb-3 d-flex align-items-center">
                                                <input type="checkbox" class="me-2">
                                                <label class="mb-0">Has IRCTC Module</label>
                                            </div>

                                        </div>

                                        <!-- BUTTON -->
                                        <div class="text-center mt-4">
                                            <button type="button" class="btn btn-warning px-5 rounded-pill" onclick="nextStep()">Next →</button>
                                        </div>

                                    </div>

                                    <!-- ================= STEP 2 ================= -->

                                    <div id="step2" style="display:none;">

                                        <h3 class="fw-bold mb-4 border-bottom pb-2">City Selection</h4>

                                            <div class="row">

                                                <!-- LEFT SIDE -->
                                                <div class="col-md-6">

                                                    <div class="d-flex mb-3">
                                                        <input type="text" id="citySearch" class="form-control me-2" placeholder="Search By City Name">
                                                        <button class="btn btn-warning" onclick="commonAjax.searchCity()">Search</button>
                                                    </div>

                                                    <div id="cityList"></div>

                                                </div>


                                                <!-- RIGHT SIDE -->
                                                <div class="col-md-6">

                                                    <h6 class="mb-3">Preview</h6>

                                                    <div id="previewList"></div>

                                                </div>

                                            </div>


                                            <!-- STEP 2 BUTTONS -->
                                            <div class="text-center mt-4">

                                                <button type="button" class="btn btn-warning px-5 rounded-pill me-3" onclick="backStep()">← Back</button>

                                                <button type="button" class="btn btn-warning px-5 rounded-pill" onclick="nextStep2()">Next →</button>

                                            </div>

                                    </div>


                                    <div id="step3" style="display:none;">

                                        <h3 class="fw-bold mb-4 border-bottom pb-2">Time Configuration</h3>

                                        <div class="row fw-bold border-bottom pb-2 mb-3">
                                            <div class="col-md-4">City Name</div>
                                            <div class="col-md-4 text-center">Source</div>
                                            <div class="col-md-4 text-center">Destination</div>
                                        </div>

                                        <!-- City Row -->
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-4 fw-bold">1. Baripada</div>

                                            <div class="col-md-4 text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>

                                            <div class="col-md-4 text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-4 fw-bold">2. Balasore</div>

                                            <div class="col-md-4 text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>

                                            <div class="col-md-4 text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-4 fw-bold">3. Soro</div>

                                            <div class="col-md-4 text-center">
                                                <input class="form-check-input red-switch" type="checkbox">
                                            </div>

                                            <div class="col-md-4 text-center">
                                                <input class="form-check-input red-switch" type="checkbox">
                                            </div>
                                        </div>

                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-4 fw-bold">4. Bhadrak</div>

                                            <div class="col-md-4 text-center">
                                                <input class="form-check-input red-switch" type="checkbox">
                                            </div>

                                            <div class="col-md-4 text-center">
                                                <input class="form-check-input red-switch" type="checkbox">
                                            </div>
                                        </div>

                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-4 fw-bold">5. Cuttack</div>

                                            <div class="col-md-4 text-center">
                                                <input class="form-check-input red-switch" type="checkbox">
                                            </div>

                                            <div class="col-md-4 text-center">
                                                <input class="form-check-input red-switch" type="checkbox">
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="text-center mt-5">

                                            <button type="button" class="btn btn-warning px-5 rounded-pill me-3" onclick="backStep2()">
                                                ← Back
                                            </button>

                                            <button type="button" class="btn btn-warning px-5 rounded-pill" onclick="nextStep3()">
                                                Next →
                                            </button>

                                        </div>

                                    </div>

                                    <div id="step4" style="display:none;">

                                        <h3 class="fw-bold mb-4 border-bottom pb-2">Add Station</h3>

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

                                                        <h5 class="mb-3 border-bottom pb-2">Add Station</h5>

                                                        <div class="stationRows">

                                                            <div class="row align-items-center mb-3 stationRow">

                                                                <div class="col-md-1">
                                                                    <input type="checkbox" class="form-check-input">
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <select class="form-select">
                                                                        <option>Select Station</option>
                                                                        <option>Balasore</option>
                                                                        <option>Soro</option>
                                                                        <option>Bhadrak</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <input type="time" class="form-control" value="00:00">
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <button class="btn btn-primary addRow">+</button>
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

                                                        <h5 class="mb-3 border-bottom pb-2">Add Station</h5>

                                                        <div class="stationRows">

                                                            <div class="row align-items-center mb-3 stationRow">

                                                                <div class="col-md-1">
                                                                    <input type="checkbox" class="form-check-input">
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <select class="form-select">
                                                                        <option>Select Station</option>
                                                                        <option>Balasore</option>
                                                                        <option>Soro</option>
                                                                        <option>Bhadrak</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <input type="time" class="form-control" value="00:00">
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <button class="btn btn-primary addRow">+</button>
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

                                                        <h5 class="mb-3 border-bottom pb-2">Add Station</h5>

                                                        <div class="stationRows">

                                                            <div class="row align-items-center mb-3 stationRow">

                                                                <div class="col-md-1">
                                                                    <input type="checkbox" class="form-check-input">
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <select class="form-select">
                                                                        <option>Select Station</option>
                                                                        <option>Balasore</option>
                                                                        <option>Soro</option>
                                                                        <option>Bhadrak</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <input type="time" class="form-control" value="00:00">
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <button class="btn btn-primary addRow">+</button>
                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>

                                            </div>

                                        </div>


                                        <div class="text-center mt-5">

                                            <button type="button" class="btn btn-warning px-5 rounded-pill me-3" onclick="backStep3()">← Back</button>

                                            <button type="button" class="btn btn-warning px-5 rounded-pill" onclick="nextStep4()">Next →</button>

                                        </div>

                                    </div>


                                    <div id="step5" style="display:none;">

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

                                            <button type="button" class="btn btn-warning px-5 rounded-pill me-4" onclick="backStep4()">
                                                Back
                                            </button>

                                            <button type="button" class="btn btn-warning px-5 rounded-pill">
                                                Next
                                            </button>

                                        </div>

                                    </div>

                                    <div id="step6" style="display:none;">

                                        <h3 class="fw-bold mb-4 border-bottom pb-2">Update Contact Info</h3>

                                        <div class="row mb-4">

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Bus Number</label>
                                                <input type="text" class="form-control" placeholder="Bus Number">
                                            </div>

                                        </div>


                                        <!-- Conductor Row -->
                                        <div class="row align-items-center mb-4">

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Conductor Number</label>
                                                <input type="text" class="form-control" placeholder="Input Number">
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">SMS On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>

                                            <div class="col-md-3 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>

                                        </div>


                                        <!-- Manager Row -->
                                        <div class="row align-items-center mb-4">

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Manager Number</label>
                                                <input type="text" class="form-control" placeholder="Input Number">
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">SMS On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>

                                            <div class="col-md-3 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>

                                        </div>


                                        <!-- Owner Row -->
                                        <div class="row align-items-center mb-4">

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Owner Number</label>
                                                <input type="text" class="form-control" placeholder="Input Number">
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">SMS On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>

                                            <div class="col-md-3 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox">
                                                </div>
                                            </div>

                                        </div>


                                        <!-- Buttons -->
                                        <div class="text-center mt-5">

                                            <button class="btn btn-warning px-5 rounded-pill me-3">
                                                Back
                                            </button>

                                            <button class="btn btn-warning px-5 rounded-pill">
                                                Preview
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
    function nextStep() {
        document.getElementById("step1").style.display = "none";
        document.getElementById("step2").style.display = "block";
    }

    function backStep() {
        document.getElementById("step1").style.display = "block";
        document.getElementById("step2").style.display = "none";
    }

    function nextStep2() {
        document.getElementById("step2").style.display = "none";
        document.getElementById("step3").style.display = "block";
    }

    function backStep2() {
        document.getElementById("step3").style.display = "none";
        document.getElementById("step2").style.display = "block";
    }

    function nextStep3() {
        document.getElementById("step3").style.display = "none";
        document.getElementById("step4").style.display = "block";
    }

    function backStep3() {
        document.getElementById("step4").style.display = "none";
        document.getElementById("step3").style.display = "block";
    }

    function nextStep4() {
        document.getElementById("step4").style.display = "none";
        document.getElementById("step5").style.display = "block";
    }

    function backStep4() {
        document.getElementById("step5").style.display = "none";
        document.getElementById("step4").style.display = "block";
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

    $('.onSelect').on('change', function () {
        generateBusName();
    });

    function generateBusName() {

        const brandVal    = $('#brand').val();
        const modelVal    = $('#busModel').val();
        const axleVal     = $('#axleType').val();
        const serviceVal  = $('#busService').val();
        const acVal       = $('.annexture').val();
        const seatVal     = $('#seatType').val();
        // const layoutVal   = $('#seatLayout').val();

        // Check ALL selected
        // if (brandVal && modelVal && axleVal && serviceVal && acVal && seatVal && layoutVal) {
        if (brandVal && modelVal && axleVal && serviceVal && acVal && seatVal) {

            const brand    = $('#brand option:selected').text();
            const model    = $('#busModel option:selected').text();
            const axle     = $('#axleType option:selected').text();
            const service  = $('#busService option:selected').text();
            const ac       = $('.annexture option:selected').text();
            const seatType = $('#seatType option:selected').text();
            const layout   = $('#seatLayout option:selected').text();

            // const fullName = `${brand} ${model} ${axle} ${service} ${ac} ${seatType} ${layout}`;
            const fullName = `${brand} ${model} ${axle} ${service} ${ac} ${seatType}`;

            $('#busType').html(fullName);

        } else {
            // Clear if not all selected
            $('#busType').html('');
        }
    }

    $(document).ready(function() {

        commonAjax.initSelect2('#brand', 'Select Brand');
        commonAjax.initSelect2('#busModel', 'Select Model');
        commonAjax.initSelect2('#axleType', 'Select Axxle Type');
        commonAjax.initSelect2('#busService', 'Select Bus Service');
        commonAjax.initSelect2('.annexture', 'Select AC Type');
        commonAjax.initSelect2('#seatType', 'Select Seat Type');
        commonAjax.initSelect2('#seatLayout', 'Select Seat Layout');

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