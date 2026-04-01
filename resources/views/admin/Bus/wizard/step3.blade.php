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


                                    <div id="step3">

                                        <div class="row fw-bold border-bottom pb-1">
                                            <div class="col-md-4">City Name</div>
                                            <div class="col-md-3 text-center">Is Boarding</div>
                                            <div class="col-md-3 text-center">Is Dropping</div>
                                            <div class="col-md-2">Listing Timings</div>
                                        </div>

                                        <!-- City Row -->
                                        <div id="cityContainer"></div>

                                        <!-- Buttons -->
                                        <div class="text-center mt-5">

                                            <input type="hidden" name="bus_id" value="{{$data['bus_id']}}">

                                            <button type="button" class="btn btn-warning px-5 rounded-pill me-3" onclick="backStep2()">
                                                ← Back
                                            </button>
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
@push('scripts')

<script type="module">
    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        let isValid = true;
        let city = "";

        $('#cityContainer > .row').each(function() {

            let $row = $(this);

            let boardingChecked = $row.find('.boarding').is(':checked');
            let droppingChecked = $row.find('.dropping').is(':checked');
            let $time = $row.find('.city-time');
            let cityName = $row.find('.cityName').val();

            if (!boardingChecked && !droppingChecked) {
                isValid = false;
                city = cityName;
                return false;
            }

            if (!$time.val()) {
                isValid = false;
                city = cityName;
                return false;
            }

        });

        if (!isValid) {
            commonAjax.viewAlert('Boarding / Dropping and time for ' + city + ' cannot be left blank');
            return;
        }

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    $(document).ready(function() {

        let cities = JSON.parse(localStorage.getItem("selectedCities") || "[]");

        let html = "";

        cities.forEach(function(city, index) {

            let cityId = city[0];
            let cityName = city[1];

            html += `
            <div class="row align-items-center border-bottom pb-1 pt-1">
                <div class="col-md-4 fw-bold">${index + 1}. ${cityName}</div>
                <input type="hidden" name="cities[${cityId}]" value="${cityName}" class="cityName">

                <div class="col-md-3 text-center align-middle">
                    <div class="checkbox">
                        <input type="checkbox" name="boarding[${cityId}]" class="boarding">
                    </div>
                </div>

                <div class="col-md-3 text-center align-middle">
                    <div class="checkbox">
                        <input type="checkbox" name="dropping[${cityId}]" class="dropping">
                    </div>
                </div>

                <div class="col-md-2 text-center">
                    <input type="time" name="time[${cityId}]" class="form-control form-control-sm city-time" value="">
                </div>
            </div>
            `;
        });

        $("#cityContainer").html(html);
    });
</script>
@endpush
