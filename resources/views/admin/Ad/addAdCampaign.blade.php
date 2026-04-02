@extends('admin.layouts.master')
@section('page_title', 'Add Campaign')
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
        <a href="{{ route('AdCampaign.index') }}" class="btn btn-success btn-sm">
            View Ad Campaign
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
                                        <input type="hidden" id="selectedPlacement" value="{{ $data['row']->placement_id ?? '' }}">
                                        <input type="hidden" id="selectedVendor" value="{{ $data['row']->vendor_id ?? '' }}">
                                        <input type="hidden" id="selectedPricingPlan" value="{{ $data['row']->pricing_plan_id ?? '' }}">

                                        <div class="col-md-4">
                                            <label for="vendor">Vendor<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm" id="vendor" name="vendor">
                                                <option value="">Select Vendor</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="placement">Placement<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm" id="placement" name="placement">
                                                <option value="">Select Placement</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="pricingPlan">Pricing Plan<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm" id="pricingPlan" name="pricingPlan">
                                                <option value="">Select Pricing Plan</option>
                                            </select>
                                        </div>

                                    </div>


                                    <!-- Row 2 -->
                                    <div class="row mb-3">

                                        <div class="col-md-6">
                                            <label for="title">Title<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control clearable form-control-sm"
                                                id="title"
                                                name="title"
                                                value="{{ $data['row']->title ?? '' }}"
                                                placeholder="Enter Title"
                                                maxlength="100">
                                            <small class="text-muted char-counter float-end"></small>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="startDate">Start Date<span class="text-danger important">*</span></label>

                                            <input type="date"
                                                class="form-control clearable form-control-sm"
                                                id="startDate"
                                                name="startDate"
                                                value="{{ $data['row']->start_date ?? '' }}">

                                        </div>

                                        <div class="col-md-3">
                                            <label for="endDate">End Date<span class="text-danger important">*</span></label>

                                            <input type="date"
                                                class="form-control clearable form-control-sm"
                                                id="endDate"
                                                name="endDate"
                                                value="{{ $data['row']->end_date ?? '' }}">

                                        </div>

                                    </div>


                                    <!-- Row 3 -->
                                    <div class="row mb-3">

                                        <div class="col-md-4">
                                            <label for="duration">Total Budget<span class="text-danger important">*</span></label>

                                            <input type="text"
                                                class="form-control form-control-sm"
                                                id="budget"
                                                name="budget"
                                                value="{{ $data['row']->total_budget ?? '' }}"
                                                placeholder="Enter Time Duration"
                                                maxlength="8"
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

        let vendor = $('#vendor').val();
        let placement = $('#placement').val();
        let pricingPlan = $('#pricingPlan').val();
        let title = $('#title').val();
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();
        let budget = parseInt($('#budget').val());

        if (!vendor) {
            commonAjax.viewAlert('Vendor must be selected');
            $('#vendor').focus();
            return false;
        }

        if (!placement) {
            commonAjax.viewAlert('Placement must be selected');
            $('#placement').focus();
            return false;
        }

        if (!pricingPlan) {
            commonAjax.viewAlert('Pricing Plan must be selected');
            $('#pricingPlan').focus();
            return false;
        }

        if (!validator.blankCheck('title', 'Title cannot be left blank'))
            return false;

        if (!validator.maxLength('title', 100, 'Title cannot be more than 100 characters'))
            return false;

        if (!startDate) {
            commonAjax.viewAlert('Start Date cannot be left blank');
            $('#startDate').focus();
            return false;
        }

        if (!endDate) {
            commonAjax.viewAlert('End Date cannot be left blank');
            $('#endDate').focus();
            return false;
        }

        if (new Date(endDate) < new Date(startDate)) {
            commonAjax.viewAlert('End Date cannot be earlier than Start Date');
            $('#endDate').focus();
            return false;
        }

        if (!validator.blankCheck('budget', 'Budget cannot be left blank'))
            return false;

        if (!validator.maxLength('budget', 8, 'Budget cannot be more than 8 characters'))
            return false;

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

        commonAjax.initSelect2('#vendor', 'Select Vendor');
        commonAjax.loadVendorList();

        commonAjax.initSelect2('#pricingPlan', 'Select Pricing Plan');
        commonAjax.loadPricingPlanList();
        commonAjax.initClearableInputs();

        let selectedPlacement = $('#selectedPlacement').val();
        let selectedVendor = $('#selectedVendor').val();
        let selectedPricingPlan = $('#selectedPricingPlan').val();

        setTimeout(function() {

            if (selectedVendor) {
                $('#vendor').val(selectedVendor).trigger('change');
            }

            if (selectedPlacement) {
                $('#placement').val(selectedPlacement).trigger('change');
            }

            if (selectedPricingPlan) {
                $('#pricingPlan').val(selectedPricingPlan).trigger('change');
            }

        }, 600);

    });
</script>
@endpush