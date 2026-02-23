@extends('admin.layouts.master')
@section('page_title', 'Amenities')
@section('content')

<?php
$page_name = 'All '.trim($__env->yieldContent('page_title'));
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
                                    <div class="row mb-3">
                                        <div class="col-md-3 mb-3">
                                            <label for="amenityCategory">Amenity Category<span class="text-danger important">*</span></label>
                                            <select class="form-select" id="amenityCategory" name="amenityCategory">
                                                <option value="0">Select Amenity Category</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="amenity_name">Amenity Name<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control" id="amenity_name" name="amenity_name" value="{{ $data['row']->amenity_name ?? '' }}" placeholder="Enter Amenity Name">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="icon">Icon<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control" id="icon" name="icon" value="{{ $data['row']->icon ?? '' }}" placeholder="Icon Class Name Only">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="is_paid">Is Paid</label>
                                            <select class="form-select" id="is_paid" name="is_paid">
                                                <option disabled selected>Select Is Paid</option>
                                                <option value="1"
                                                    {{ (isset($data['row']) && $data['row']->is_paid == 1) ? 'selected' : '' }}>
                                                    Yes
                                                </option>
                                                <option value="2"
                                                    {{ (isset($data['row']) && $data['row']->is_paid == 2) ? 'selected' : '' }}>
                                                    No
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="is_seat_specific">Is Seat Specific</label>
                                            <select class="form-select" id="is_seat_specific" name="is_seat_specific">
                                                <option disabled selected>Select Is Seat Specific</option>
                                                <option value="1"
                                                    {{ (isset($data['row']) && $data['row']->is_seat_specific == 1) ? 'selected' : '' }}>
                                                    Yes
                                                </option>
                                                <option value="2"
                                                    {{ (isset($data['row']) && $data['row']->is_seat_specific == 2) ? 'selected' : '' }}>
                                                    No
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" id="description" name="description">{{ $data['row']->description ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- BUTTONS -->
                                <div class="row mt-4">
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

    $(document).ready(function() {

        commonAjax.initSelect2('#amenityCategory', 'Select Amenity Category');

        let category_id = <?= $data['row']->category_id ?? '0' ?>

        commonAjax.loadAmenityCategory(category_id);
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
</script>
@endpush
