@extends('admin.layouts.master')
@section('page_title', 'Add Price Plan')
@section('content')

<?php
$page_name = 'All ' . trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">@yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('pricingPlan.index') }}" class="btn btn-success btn-sm">
            View Price Plan
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

                                    <!-- Row 1 -->
                                    <div class="row mb-3">

                                        <div class="col-md-4">
                                            <label for="placement">Placement<span class="text-danger important">*</span></label>
                                            <select class="form-select" id="placement" name="placement">
                                                <option value="">Select Placement</option>
                                            </select>
                                        </div>
                                        <input type="hidden" id="selectedPlacement" value="{{ $data['row']->placement_id ?? '' }}">

                                        <div class="col-md-4">
                                            <label for="defaultModel">Default Model<span class="text-danger important">*</span></label>

                                            <select class="form-select" id="defaultModel" name="defaultModel">
                                                <option value="">Select Model</option>

                                                <option value="CPM"
                                                    {{ (isset($data['row']) && $data['row']->model == 'CPM') ? 'selected' : '' }}>
                                                    CPM
                                                </option>

                                                <option value="CPC"
                                                    {{ (isset($data['row']) && $data['row']->model == 'CPC') ? 'selected' : '' }}>
                                                    CPC
                                                </option>

                                                <option value="FIXED"
                                                    {{ (isset($data['row']) && $data['row']->model == 'FIXED') ? 'selected' : '' }}>
                                                    FIXED
                                                </option>

                                            </select>
                                        </div>

                                    </div>


                                    <!-- Row 2 -->
                                    <div class="row mb-3">

                                        <div class="col-md-4">
                                            <label for="planName">Plan Name<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control"
                                                id="planName"
                                                name="planName"
                                                value="{{ $data['row']->plan_name ?? '' }}"
                                                placeholder="Enter Plan"
                                                maxlength="100">
                                            <small class="text-muted char-counter float-end"></small>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="Price">Price<span class="text-danger important">*</span></label>

                                            <input type="text"
                                                class="form-control"
                                                id="Price"
                                                name="Price"
                                                value="{{ $data['row']->price ?? '' }}"
                                                placeholder="Enter Price"
                                                maxlength="10"
                                                oninput="this.value=this.value.replace(/[^0-9]/g,'')">

                                            <small class="text-muted char-counter float-end"></small>
                                        </div>

                                    </div>


                                    <!-- Row 3 -->
                                    <div class="row mb-3">

                                        <div class="col-md-4">
                                            <label for="duration">Time Duration (Days)<span class="text-danger important">*</span></label>

                                            <input type="text"
                                                class="form-control"
                                                id="duration"
                                                name="duration"
                                                value="{{ $data['row']->duration_days ?? '' }}"
                                                placeholder="Enter Time Duration"
                                                maxlength="2"
                                                oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                        </div>

                                    </div>

                                    <!-- Buttons -->
                                    <div class="row mt-4">
                                        <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                            <button class="btn btn-primary btn-sm" type="submit">
                                                {{ $data['strSubmit'] }}
                                            </button>

                                            @if($data['strReset'] == 'Cancel')
                                            <a href="{{ route('AdPlacement.index') }}" class="btn btn-secondary btn-sm">
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



        // if (placement.length < 3) {
        //     commonAjax.viewAlert('Placement must contain at least 3 letters');
        //     return false;
        // }

        // if (placement.length > 100) {
        //     commonAjax.viewAlert('Placement cannot be more than 100 characters');
        //     return false;
        // }

        // if (!validator.blankCheck('slug', 'Slug cannot be left blank'))
        //     return false;
        let placement = $('#placement').val();
        let model = $('#defaultModel').val();

        if (!placement) {
            commonAjax.viewAlert('Placement must be selected');
            return false;
        }

        if (!model) {
            commonAjax.viewAlert('Default Model must be selected');
            return false;
        }

        if (!validator.blankCheck('planName', 'Plan Name cannot be left blank'))
            return false;

        if (!validator.maxLength('planName', 100, 'Plan Name cannot be more than 100 characters'))
            return false;

        if (!validator.blankCheck('Price', 'Price cannot be left blank'))
            return false;

        if (!validator.maxLength('Price', 8, 'Price cannot be more than 8 characters'))
            return false;

        let duration = parseInt($('#duration').val());

        if (isNaN(duration) || duration < 1 || duration > 90) {
            commonAjax.viewAlert('Time Duration must be between 1 and 90 Days');
            return false;
        }

        // if ($('#defaultModel').val() == '') {
        //     commonAjax.viewAlert('Default Model cannot be left blank');

        //     $('#defaultModel').focus();
        //     return false;
        // }

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').off('click').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {

        commonAjax.initCharCounter(['planName', 'Price']);

        commonAjax.initSelect2('#placement', 'Select Placement');

        commonAjax.loadPlacementList();

        // set selected placement after dropdown loads
        let selectedPlacement = $('#selectedPlacement').val();

        if (selectedPlacement) {

            setTimeout(function() {

                $('#placement').val(selectedPlacement).trigger('change');

            }, 500);

        }

    });
</script>
@endpush