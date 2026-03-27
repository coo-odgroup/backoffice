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
        <a href="{{ route('campaign.index') }}" class="btn btn-success btn-sm">
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
                                <!-- <div class="col-12">
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
                                                <option value="1"
                                                    {{ (isset($data['row']) && $data['row']->offer_type == 1) ? 'selected' : '' }}>
                                                    Percentage
                                                </option>
                                                <option value="0"
                                                    {{ (isset($data['row']) && $data['row']->offer_type == 0) ? 'selected' : '' }}>
                                                    Flat
                                                </option>
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
                                                <option value="1"
                                                    {{ (isset($data['row']) && $data['row']->services == 1) ? 'selected' : '' }}>
                                                    Yes
                                                </option>
                                                <option value="0"
                                                    {{ (isset($data['row']) && $data['row']->services == 0) ? 'selected' : '' }}>
                                                    No
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="auto_renewal">Auto Renewal</label>
                                            <select class="form-select" id="auto_renewal" name="auto_renewal">
                                                <option disabled selected>Select Auto Renewal</option>
                                                <option value="1"
                                                    {{ (isset($data['row']) && $data['row']->auto_renewal == 1) ? 'selected' : '' }}>
                                                    Yes
                                                </option>
                                                <option value="0"
                                                    {{ (isset($data['row']) && $data['row']->auto_renewal == 0) ? 'selected' : '' }}>
                                                    No
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="validity_type">Validity Type</label>
                                            <select class="form-select" id="validity_type" name="validity_type">
                                                <option disabled selected>Select Validity Type</option>
                                                <option value="DATE_RANGE"
                                                    {{ (isset($data['row']) && $data['row']->validity_type == 'DATE_RANGE') ? 'selected' : '' }}>
                                                    DATE RANGE
                                                </option>
                                                <option value="DURATION"
                                                    {{ (isset($data['row']) && $data['row']->validity_type == 'DURATION') ? 'selected' : '' }}>
                                                    DURATION
                                                </option>
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
                                                <option value="DAY"
                                                    {{ (isset($data['row']) && $data['row']->duration_unit == 'DAY') ? 'selected' : '' }}>
                                                    DAY
                                                </option>
                                                <option value="WEEk"
                                                    {{ (isset($data['row']) && $data['row']->duration_unit == 'WEEk') ? 'selected' : '' }}>
                                                    WEEk
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label for="source">Source</label>
                                            <select class="form-select selCity" id="source" name="src_id">
                                                <option disabled selected>Select Source</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="destination">Destination</label>
                                            <select class="form-select selCity" id="destination" name="dest_id">
                                                <option disabled selected>Select Destination</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="bus">Bus</label>
                                            <select class="form-select" id="bus" name="bus_id">
                                                <option disabled selected>Select Bus</option>
                                                <option value="1">Static Test Bus</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label for="excluded_date">Excluded Date</label>
                                            <input type="date" class="form-control" id="excluded_date" name="excluded_date" value="{{ $data['row']->excluded_date ?? old('excluded_date') }}" placeholder="Enter Excluded Date">
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label for="day_of_week">Active Days</label>
                                            <input type="text" class="form-control" id="day_of_week" name="day_of_week" value="{{ $data['row']->day_of_week ?? old('day_of_week') }}" placeholder="Enter Active Days">
                                        </div>
                                    </div>
                                </div> -->

                                <div class="row">

                                    <!-- Campaign Master -->
                                    <div class="col-md-4 mb-3">
                                        <label>Campaign Master *</label>
                                        <select class="form-select form-select-sm">
                                            <option>Select Campaign</option>
                                        </select>
                                    </div>

                                    <!-- Offer Type -->
                                    <div class="col-md-4 mb-3">
                                        <label>Offer Type</label>
                                        <div class="d-flex gap-2">
                                            <label class="radio-box">
                                                <input type="radio" name="offer_type" value="PERCENTAGE">
                                                <div class="box">% Percentage</div>
                                            </label>

                                            <label class="radio-box">
                                                <input type="radio" name="offer_type" value="FLAT">
                                                <div class="box">₹ Flat</div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <div id="offerValuesContainer" class="d-flex flex-wrap gap-2"></div>
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label>Offer Value</label>
                                        <input type="text" name="offer_value" class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label>Min Ticket Value</label>
                                        <input type="text" class="form-control form-control-sm" value="300">
                                    </div>

                                    <!-- Coupon Type -->
                                    <div class="col-12 mb-3">
                                        <label>Coupon Type</label>
                                        <div class="d-flex gap-3 flex-wrap">
                                            <label class="radio-box">
                                                <input type="radio" name="coupon_type" value="OPERATOR">
                                                <div class="box">Operator</div>
                                            </label>

                                            <label class="radio-box">
                                                <input type="radio" name="coupon_type" value="ROUTE">
                                                <div class="box">Route</div>
                                            </label>

                                            <label class="radio-box">
                                                <input type="radio" name="coupon_type" value="OPERATOR_ROUTE">
                                                <div class="box">Operator + Route</div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Operator Section -->
                                    <div id="operatorSection" class="col-12 d-none mb-3">
                                        <label>Operators</label>
                                        <select class="form-select mb-2" multiple>
                                            <option>VRL Travels</option>
                                            <option>SRS Travels</option>
                                        </select>

                                        <label>Buses</label>
                                        <select class="form-select" multiple>
                                            <option>Bus 1</option>
                                            <option>Bus 2</option>
                                        </select>
                                    </div>

                                    <!-- Route Section -->
                                    <div id="routeSection" class="col-12 d-none mb-3 row">
                                        <div class="col-md-6">
                                            <label>Source</label>
                                            <select class="form-select">
                                                <option>Chennai</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Destination</label>
                                            <select class="form-select">
                                                <option>Bangalore</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Validity -->
                                    <div class="col-12 mb-3">
                                        <label>Validity</label>
                                        <div id="validityContainer" class="d-flex gap-2 flex-wrap"></div>
                                    </div>

                                    <!-- Date Range -->
                                    <div id="dateRange" class="row d-none mb-3">
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Active Days</label>
                                            <div id="activeDaysContainer" class="d-flex flex-wrap gap-2"></div>
                                        </div>
                                    </div>

                                    <!-- Exclude Dates -->
                                    <div class="col-md-4 mb-3">
                                        <label>Exclude Dates</label>
                                        <input type="date" id="excludeDate" class="form-control">
                                        <div id="excludeList" class="mt-2"></div>
                                    </div>

                                    <!-- Active Days -->
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">Active Days</label>

                                        <div class="d-flex flex-wrap gap-2">

                                            <label class="day-box">
                                                <input type="checkbox" name="days[]" value="Sun">
                                                <div class="box">Sun</div>
                                            </label>

                                            <label class="day-box">
                                                <input type="checkbox" name="days[]" value="Mon">
                                                <div class="box">Mon</div>
                                            </label>

                                            <label class="day-box">
                                                <input type="checkbox" name="days[]" value="Tue">
                                                <div class="box">Tue</div>
                                            </label>

                                            <label class="day-box">
                                                <input type="checkbox" name="days[]" value="Wed">
                                                <div class="box">Wed</div>
                                            </label>

                                            <label class="day-box">
                                                <input type="checkbox" name="days[]" value="Thu">
                                                <div class="box">Thu</div>
                                            </label>

                                            <label class="day-box">
                                                <input type="checkbox" name="days[]" value="Fri">
                                                <div class="box">Fri</div>
                                            </label>

                                            <label class="day-box">
                                                <input type="checkbox" name="days[]" value="Sat">
                                                <div class="box">Sat</div>
                                            </label>

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
    const container = document.getElementById("offerValuesContainer");
    const offerInput = document.querySelector('input[name="offer_value"]') || document.querySelector('input[type="text"]');


    window.loadDynamicOptions = function(annexture_type, containerId, type = '') {

        let container = document.getElementById(containerId);
        container.innerHTML = '';

        $.ajax({
            type: "POST",
            url: "{{ url('admin/get-annexture-list') }}",
            data: {
                annexture_type: annexture_type,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function(response) {

                if (response.status && response.data.length > 0) {

                    response.data.forEach(item => {

                        let label = document.createElement('label');
                        label.className = (type === 'checkbox') ? 'day-box' : 'radio-box';

                        let input = document.createElement('input');

                        if (type === 'checkbox') {
                            input.type = 'checkbox';
                            input.name = 'days[]';
                        } else {
                            input.type = 'radio';
                            input.name = 'validity';
                        }

                        input.value = item.annexture_name;

                        let div = document.createElement('div');
                        div.className = 'box';
                        div.innerText = item.annexture_name;

                        // special case: DATE RANGE
                        if (item.annexture_name === 'Date Range' || item.annexture_name === 'DATE') {
                            input.value = 'DATE';
                        }

                        label.appendChild(input);
                        label.appendChild(div);

                        container.appendChild(label);
                    });

                } else {
                    container.innerHTML = '<p>No Data Found</p>';
                }
            }
        });
    };
    // Handle Offer Type Change
    document.querySelectorAll('[name="offer_type"]').forEach(el => {
        el.addEventListener('change', function() {

            let type = this.value;

            if (type === 'PERCENTAGE') {
                loadAnnextureList('CAMPAIGN_PERCENTAGE', 'PERCENTAGE');
            } else if (type === 'FLAT') {
                loadAnnextureList('CAMPAIGN_FLAT', 'FLAT');
            }

        });
    });

    document.addEventListener('DOMContentLoaded', function() {

        // Load Validity
        loadDynamicOptions('CAMPAIGN_VALIDITY', 'validityContainer');

        // Load Active Days
        loadDynamicOptions('CAMPAIGN_ACTIVE_DAYS', 'activeDaysContainer', 'checkbox');
    });

    window.loadAnnextureList = function(annexture_type = '', type = '') {

    

        let container = document.getElementById("offerValuesContainer");
        container.innerHTML = '';

        $.ajax({
            type: "POST",
            url: "{{ url('admin/get-annexture-list') }}",
            data: {
                annexture_type: annexture_type,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function(response) {

                if (response.status && response.data.length > 0) {

                    response.data.forEach(item => {

                        let div = document.createElement('div');
                        div.className = 'offer-chip';

                        div.innerText = (type === 'PERCENTAGE') ?
                            item.annexture_name :
                            '₹' + item.annexture_name;

                        div.onclick = function() {

                            document.querySelectorAll('.offer-chip')
                                .forEach(c => c.classList.remove('active'));

                            div.classList.add('active');

                            document.querySelector('[name="offer_value"]').value = item.annexture_name;
                        };

                        container.appendChild(div);
                    });

                } else {
                    container.innerHTML = '<p>No Data Found</p>';
                }
            }
        });
    };



    document.querySelectorAll('[name="coupon_type"]').forEach(el => {
        el.addEventListener('change', function() {

            operatorSection.classList.add('d-none');
            routeSection.classList.add('d-none');

            if (this.value === 'OPERATOR') {
                operatorSection.classList.remove('d-none');
            }
            if (this.value === 'ROUTE') {
                routeSection.classList.remove('d-none');
            }
            if (this.value === 'OPERATOR_ROUTE') {
                operatorSection.classList.remove('d-none');
                routeSection.classList.remove('d-none');
            }
        });
    });

    // Validity Toggle
    document.addEventListener('change', function(e) {
        if (e.target.name === 'validity') {

            let val = e.target.value;

            document.getElementById('dateRange')
                .classList.toggle('d-none', val !== 'DATE');
        }
    });

    // Exclude Dates
    document.getElementById('excludeDate').addEventListener('change', function() {
        let val = this.value;
        let tag = document.createElement('span');
        tag.className = 'badge bg-warning text-dark me-1';
        tag.innerHTML = val;
        document.getElementById('excludeList').appendChild(tag);
    });
    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $(document).ready(function() {

        commonAjax.initSelect2('#campaignMaster', 'Select Campaign Master');
        commonAjax.initSelect2('.selCity', 'Select Location');

        let campaign_master_id = <?= $data['row']->campaign_master_id ?? '0' ?>;
        let src_id = <?= $data['row']->src_id ?? '0' ?>;
        let dest_id = <?= $data['row']->dest_id ?? '0' ?>;

        commonAjax.loadCampaignMasterList(campaign_master_id);
        commonAjax.loadCityList(src_id);
        commonAjax.loadCityList(dest_id);
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

        function toggleValiditySection() {
            const type = $('#validity_type').val();

            if (type === 'DATE_RANGE') {
                $('.dateSec').show();
                $('.durationSec').hide();
            } else if (type === 'DURATION') {
                $('.dateSec').hide();
                $('.durationSec').show();
            } else {
                // ADD CASE (no selection yet)
                $('.dateSec').show();
                $('.durationSec').hide();
            }
        }

        // Run on load (handles edit + add)
        toggleValiditySection();

        // Run on change
        $('#validity_type').on('change', function() {
            toggleValiditySection();
        });

    });

    function isNumeric(value) {
        return /^[0-9]+(\.[0-9]+)?$/.test(value);
    }
</script>
@endpush