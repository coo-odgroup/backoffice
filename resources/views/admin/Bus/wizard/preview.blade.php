@extends('admin.layouts.master')
@section('page_title', 'BusInfo')
@section('content')

<?php
// $page_name = 'All ' . trim($__env->yieldContent('page_title'));
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">Preview</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">Page Title</h5>
    <div>
        <a href="{{ route('amenities.index') }}" class="btn btn-success btn-sm">
           Preview
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