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
        <a href="{{ route('bus.index') }}" class="btn btn-success btn-sm">
            View Bus List
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
                                                    <option value="{{ $layout->id }}" {{ ($data['step7BusRes']==$layout->id) ? 'selected' : '' }}>
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
                                            <input type="hidden" name="existRes" value="{{ $data['existRes'] ?? 0 }}">
                                            @php
                                            $isSave = ($data['param'] ?? null) === 'save';
                                            $isBack = ($data['param2'] ?? null) === 'back';
                                            @endphp

                                            @if ($data['param2'] == 'edit')
                                            <button type="submit" class="btn btn-success px-5 rounded-pill">Update & Preview →</button>
                                            @else

                                            @if ($isSave)
                                            <a href="{{ url($createBusUrl.'step6/'.$data['enc_bus_id'].'/save/back') }}"
                                                class="btn btn-secondary px-5 rounded-pill me-3">
                                                ← Back
                                            </a>
                                            @endif

                                            @if (($isSave && $isBack) || @$data['existRes'] == 1)
                                            <a href="{{ url($createBusUrl.'preview/'.$data['enc_bus_id'].'/save') }}"
                                                class="btn btn-warning px-5 rounded-pill me-3">
                                                Continue →
                                            </a>
                                            @endif

                                            <button type="submit" class="btn btn-success px-5 rounded-pill">Save & Preview →</button>
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

    $(document).ready(function() {

        let selectedSeats = {};

        // edit data
        let editData = <?= json_encode(@$data['step7Res']) ?>;

        if (editData && editData.length > 0) {
            editData.forEach(item => {
                selectedSeats[item.seat_id] = true;
            });
        }

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

                    let fullHTML = generateFullLayout(response, selectedSeats);

                    $('#seatContainer').html(fullHTML);
                }
            });
        });

        if ($('#seatLayout').val()) {
            $('#seatLayout').trigger('change');
        }

    });

    function generateFullLayout(response, selectedSeats = {}) {

        let upperExists = response.layout.UPPER && Object.keys(response.layout.UPPER).length > 0;
        let lowerExists = response.layout.LOWER && Object.keys(response.layout.LOWER).length > 0;

        let upperHTML = '';
        let lowerHTML = '';
        let tabsHTML = '';

        if (upperExists) {
            upperHTML = generateSection(
                response.layout.UPPER,
                response.maxCols.UPPER,
                'UPPER',
                'upper-berth-box',
                true,
                selectedSeats
            );

            tabsHTML += `
                <button type="button" class="seat-tab-btn active" data-target="upper-berth-box">
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
                !upperExists,
                selectedSeats
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

                ${(upperExists || lowerExists) ? `
                    <div class="seat-tabs">
                        ${tabsHTML}
                    </div>
                ` : ''}

                <div class="bus-layout">
                    ${upperHTML}
                    ${lowerHTML}
                </div>

            </div>
        </div>
        `;
    }

    function generateSection(layoutData, maxCols, type, id, isActive, selectedSeats) {

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

                    // Skip logic
                    if (skip[rIndex] && skip[rIndex][cIndex]) return;

                    let checked = selectedSeats[seat.id] ? 'checked' : '';
                    let selected = selectedSeats[seat.id] ? 'selected' : '';

                    // EMPTY
                    if (seat.seat_class == 0 || !seat.seat_text) {

                        html += `<div class="empty-seat"></div>`;

                    }

                    // VERTICAL SLEEPER
                    else if (seat.seat_class == 3) {

                        let text = (seat.seat_text || '').toUpperCase();
                        let iconClass = 'bus-vertical-sleeper';

                        if (text === 'EXIT') iconClass = 'vertical_exit_prv';
                        else if (text === 'TOILET') iconClass = 'vertical_toilet_prv';

                        html += `
                        <label class="seat-wrap vertical-sleeper-wrap">
                            <span class="${iconClass}"></span>
                            <span class="seat-number">${seat.seat_text}</span>
                        </label>
                        `;

                        // Skip next row
                        if (!skip[parseInt(rIndex) + 1]) {
                            skip[parseInt(rIndex) + 1] = {};
                        }
                        skip[parseInt(rIndex) + 1][cIndex] = true;
                    }

                    // SLEEPER
                    else if (seat.seat_class == 2) {

                        html += `
                        <label class="seat-wrap sleeper-wrap">
                            <input type="checkbox" class="seat-checkbox" name="seat_id[]" value="${seat.id}" ${checked}>
                            <input type="checkbox" class="seat-checkbox" name="seat_code[]" value="${seat.seat_text}" ${checked}>
                            <span class="bus-sleeper ${selected}"></span>
                            <span class="seat-number">${seat.seat_text}</span>
                        </label>
                        `;
                    }

                    // NORMAL SEAT
                    else {

                        html += `
                        <label class="seat-wrap">
                            <input type="checkbox" class="seat-checkbox" name="seat_id[]" value="${seat.id}" ${checked}>
                            <input type="checkbox" class="seat-checkbox" name="seat_code[]" value="${seat.seat_text}" ${checked}>
                            <span class="bus-seat ${selected}"></span>
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