@extends('admin.layouts.master')
@section('page_title', 'Campaign')
@section('content')

<?php
$page_name = 'All ' . trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Campaign</li>
        <li class="breadcrumb-item active">{{ $data['strPage'] }} @yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('campaign-master.index') }}" class="btn btn-success btn-sm">
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
                                        <div class="col-md-4 mb-3">
                                            <label for="campaignMaster">Campaign Master<span class="text-danger important">*</span></label>
                                            <select class="form-select" id="campaignMaster" name="campaign_master_id">
                                                <option disabled selected>Select Campaign Master</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="offer_type">Offer Type</label>
                                            <select class="form-select" id="offer_type" name="offer_type">
                                                <option disabled selected>Select Offer Type</option>
                                                <option value="1">Percentage</option>
                                                <option value="0">Flat</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="offer_value">Offer Value</label>
                                            <input type="text" class="form-control" id="offer_value" name="offer_value" value="{{ $data['row']->offer_value ?? old('offer_value') }}" placeholder="Enter Offer Value">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="min_ticket_value">Min Ticket Value</label>
                                            <input type="text" class="form-control" id="min_ticket_value" name="min_ticket_value" value="{{ $data['row']->min_ticket_value ?? old('min_ticket_value') }}" placeholder="Enter Min Ticket Value">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="services">Services</label>
                                            <select class="form-select" id="services" name="services">
                                                <option disabled selected>Select Services</option>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="auto_renewal">Auto Renewal</label>
                                            <select class="form-select" id="auto_renewal" name="auto_renewal">
                                                <option disabled selected>Select Auto Renewal</option>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="validity_type">Validity Type</label>
                                            <select class="form-select" id="validity_type" name="validity_type">
                                                <option disabled selected>Select Validity Type</option>
                                                <option value="DATE_RANGE">DATE RANGE</option>
                                                <option value="DURATION">DURATION</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3 dateSec">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $data['row']->start_date ?? old('start_date') }}" placeholder="Enter Start Date">
                                        </div>
                                        <div class="col-md-2 mb-3 dateSec">
                                            <label for="end_date">End Date</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $data['row']->end_date ?? old('end_date') }}" placeholder="Enter End Date">
                                        </div>
                                        <div class="col-md-2 mb-3 durationSec">
                                            <label for="duration_value">Duration Value</label>
                                            <input type="text" class="form-control" id="duration_value" name="duration_value" value="{{ $data['row']->duration_value ?? old('duration_value') }}" placeholder="Enter Duration Value">
                                        </div>
                                        <div class="col-md-2 mb-3 durationSec">
                                            <label for="duration_unit">Duration Unit</label>
                                            <select class="form-select" id="duration_unit" name="duration_unit">
                                                <option disabled selected>Select Duration Unit</option>
                                                <option value="DAY">DAY</option>
                                                <option value="WEEk">WEEk</option>
                                            </select>
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
                                        <a href="{{ route('campaign-master.index') }}" class="btn btn-secondary btn-sm">
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

        commonAjax.initSelect2('#campaignMaster', 'Select Campaign Master');

        let campaign_master_id = <?= $data['row']->campaign_master_id ?? '0' ?>

        commonAjax.loadCampaignMasterList(campaign_master_id);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        // Required fields
        if (!validator.blankCheck('campaign_master_id', 'Campaign is required'))
            return false;

        if (!validator.blankCheck('offer_type', 'Offer Type is required'))
            return false;

        if (!validator.blankCheck('offer_value', 'Offer Value is required'))
            return false;

        // ✅ Numeric check (manual)
        if (!isNumeric($('#offer_value').val())) {
            commonAjax.viewAlert('Offer Value must be numeric');
            return false;
        }

        // Optional numeric
        if ($('#min_ticket_value').val() !== '') {
            if (!isNumeric($('#min_ticket_value').val())) {
                commonAjax.viewAlert('Min Ticket Value must be numeric');
                return false;
            }
        }

        // Validity Type
        if (!validator.blankCheck('validity_type', 'Validity Type is required'))
            return false;

        let validityType = $('#validity_type').val();

        // ================= DATE RANGE =================
        if (validityType === 'DATE_RANGE') {

            if (!validator.blankCheck('start_date', 'Start Date is required for Date Range'))
                return false;

            if (!validator.blankCheck('end_date', 'End Date is required for Date Range'))
                return false;

            let startDate = new Date($('#start_date').val());
            let endDate = new Date($('#end_date').val());

            if (endDate < startDate) {
                commonAjax.viewAlert('End Date must be greater than or equal to Start Date');
                return false;
            }
        }

        // ================= DURATION =================
        if (validityType === 'DURATION') {

            if (!validator.blankCheck('duration_value', 'Duration Value is required'))
                return false;

            // ✅ Numeric check
            if (!isNumeric($('#duration_value').val())) {
                commonAjax.viewAlert('Duration Value must be numeric');
                return false;
            }

            if (parseInt($('#duration_value').val()) < 1) {
                commonAjax.viewAlert('Duration Value must be at least 1');
                return false;
            }

            if (!validator.blankCheck('duration_unit', 'Duration Unit is required'))
                return false;
        }

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {

        // Default state → show date, hide duration
        $('.dateSec').show();
        $('.durationSec').hide();

        $('#validity_type').on('change', function() {
            const type = $(this).val();

            if (type === 'DATE_RANGE') {
                $('.dateSec').show();
                $('.durationSec').hide();
            } else if (type === 'DURATION') {
                $('.dateSec').hide();
                $('.durationSec').show();
            }
        });

    });

    function isNumeric(value) {
        return /^[0-9]+(\.[0-9]+)?$/.test(value);
    }
</script>
@endpush