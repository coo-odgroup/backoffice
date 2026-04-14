@extends('admin.layouts.master')
@section('page_title', 'Seat Layout')
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

                                    <div id="step7">

                                        <div class="row mb-4">

                                            <div class="col-md-4 offset-md-4">
                                                <label class="form-label fw-semibold">
                                                    Seat Layout <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select form-select-sm" name="seat_layout_id" id="seatLayout">
                                                    <option value="">Select Seat Layout</option>
                                                    @foreach($data['seat_layout'] as $layout)
                                                    <option value="{{ $layout->id }}">
                                                        {{ $layout->layout_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div id="seatContainer"></div>

                                        </div>

                                        <!-- Buttons -->
                                        <div class="text-center mt-1">
                                            <input type="hidden" name="bus_id" value="{{$data['bus_id']}}">
                                            <input type="hidden" name="param" value="{{$data['param']}}">
                                            <input type="hidden" name="param2" value="{{$data['param2']}}">
                                            <a href="{{ url($createBusUrl.'step6/'.$data['enc_bus_id'].'/back') }}" class="btn btn-secondary px-5 rounded-pill me-3">
                                                ← Back
                                            </a>
                                            <button type="submit" class="btn btn-success px-5 rounded-pill">Save & Preview →</button>
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

        if (!validator.selectDropdown('seatLayout', 'Select Seat layout'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').one('click', function() {
            e.currentTarget.submit();
        });

    });

    $('#seatLayout').on('change', function() {

        let layoutId = $(this).val();

        if (!layoutId) {
            $('#seatContainer').html('');
            return;
        }

        $('#seatContainer').html('Loading...');

        $.ajax({
            url: '/admin/get-seats-by-layout',
            type: 'GET',
            data: {
                layout_id: layoutId
            },

            success: function(response) {

                let fullHTML = generateFullLayout(response);

                $('#seatContainer').html(fullHTML);
            }
        });
    });

    function generateFullLayout(response) {

        let upperExists = response.layout.UPPER && Object.keys(response.layout.UPPER).length > 0;
        let lowerExists = response.layout.LOWER && Object.keys(response.layout.LOWER).length > 0;

        let upperHTML = '';
        let lowerHTML = '';
        let tabsHTML = '';

        // Generate sections only if exists
        if (upperExists) {
            upperHTML = generateSection(
                response.layout.UPPER,
                response.maxCols.UPPER,
                'UPPER',
                'upper-berth-box',
                true
            );

            tabsHTML += `
                <button type="button" class="seat-tab-btn ${!lowerExists ? 'active' : 'active'}" data-target="upper-berth-box">
                    Upper Berth
                </button>
            `;
        }

        if (lowerExists) {
            lowerHTML = generateSection(
                response.layout.LOWER,
                response.maxCols.LOWER,
                'LOWER',
                'lower-berth-box',
                !upperExists
            );

            tabsHTML += `
                <button type="button" class="seat-tab-btn ${!upperExists ? 'active' : ''}" data-target="lower-berth-box">
                    Lower Berth
                </button>
            `;
        }

        return `
        <div class="seat-main-card">

            <div class="seat-left">

                ${ (upperExists || lowerExists) ? `
                    <div class="seat-tabs">
                        ${tabsHTML}
                    </div>
                ` : '' }

                <div class="bus-layout">
                    ${upperHTML}
                    ${lowerHTML}
                </div>

            </div>
        </div>
        `;
    }

    function generateSection(layoutData, maxCols, type, id, isActive) {

        let html = `
        <div class="berth-row berth-section ${isActive ? 'active' : ''}" id="${id}">
            <div class="berth-label">${type === 'UPPER' ? 'Upper Berth' : 'Lower Berth'}</div>
            <div class="layout-box" style="grid-template-columns: repeat(${maxCols}, 42px);">
        `;

        let skip = {};

        if (!layoutData) {
            html += `<div>No seats</div>`;
        } else {

            Object.keys(layoutData).forEach((rIndex) => {

                let row = layoutData[rIndex];

                Object.keys(row).forEach((cIndex) => {

                    let seat = row[cIndex];

                    // Skip logic (for vertical sleeper)
                    if (skip[rIndex] && skip[rIndex][cIndex]) {
                        return;
                    }

                    // EMPTY
                    if (seat.seat_class == 0 || !seat.seat_text) {

                        html += `<div class="empty-seat"></div>`;

                    }

                    // VERTICAL SLEEPER (LOWER)
                    else if (seat.seat_class == 3) {

                        let text = (seat.seat_text || '').toUpperCase();

                        let iconClass = 'bus-vertical-sleeper';

                        if (text === 'EXIT') {
                            iconClass = 'vertical_exit_prv';
                        } else if (text === 'TOILET') {
                            iconClass = 'vertical_toilet_prv';
                        }

                        html += `
                        <label class="seat-wrap vertical-sleeper-wrap">
                            <span class="${iconClass}"></span>
                            <span class="seat-number">${seat.seat_text}</span>
                        </label>
                        `;

                        // Skip next row same column
                        if (!skip[parseInt(rIndex) + 1]) {
                            skip[parseInt(rIndex) + 1] = {};
                        }
                        skip[parseInt(rIndex) + 1][cIndex] = true;
                    }

                    // SLEEPER
                    else if (seat.seat_class == 2) {

                        html += `
                        <label class="seat-wrap sleeper-wrap">
                            <input type="checkbox" class="seat-checkbox" name="seat_id[]" value="${seat.id}">
                            <input type="checkbox" class="seat-checkbox" name="seat_code[]" value="${seat.seat_text}">
                            <span class="bus-sleeper"></span>
                            <span class="seat-number">${seat.seat_text}</span>
                        </label>
                        `;
                    }

                    // NORMAL SEAT
                    else {

                        html += `
                        <label class="seat-wrap">
                            <input type="checkbox" class="seat-checkbox" name="seat_id[]" value="${seat.id}">
                            <input type="checkbox" class="seat-checkbox" name="seat_code[]" value="${seat.seat_text}">
                            <span class="bus-seat"></span>
                            <span class="seat-number">${seat.seat_text}</span>
                        </label>
                        `;
                    }

                });

            });
        }

        html += `</div></div>`;

        return html;
    }

    $(document).on('click', '.seat-wrap', function() {

        console.log("hello");

        const checkbox = $(this).find('.seat-checkbox');
        const seat = $(this).find('.bus-seat, .bus-sleeper');

        if (checkbox.length) {

            checkbox.prop('checked', !checkbox.prop('checked'));

            if (checkbox.is(':checked')) {
                seat.addClass('selected');
            } else {
                seat.removeClass('selected');
            }
        }
    });
</script>
@endpush
