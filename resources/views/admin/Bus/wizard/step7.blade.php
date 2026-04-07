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
                                                <select class="form-select form-select-sm" name="seat_id" id="seatLayout">
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
                                            <button class="btn btn-warning px-5 rounded-pill me-3">
                                                Back
                                            </button>
                                            <button type="submit" class="btn btn-warning px-5 rounded-pill">Preview →</button>

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

        let upperHTML = generateSection(response.layout.UPPER, response.maxCols.UPPER, 'U', 'upper-berth-box', true);
        let lowerHTML = generateSection(response.layout.LOWER, response.maxCols.LOWER, 'L', 'lower-berth-box', false);

        return `
        <div class="seat-main-card">

            <div class="seat-left">

                <div class="seat-tabs">
                    <button type="button" class="seat-tab-btn active" data-target="upper-berth-box">Upper Berth</button>
                    <button type="button" class="seat-tab-btn" data-target="lower-berth-box">Lower Berth</button>
                </div>

                <div class="bus-layout">
                    ${upperHTML}
                    ${lowerHTML}
                </div>

            </div>
        </div>
    `;
    }

    function generateSection(layoutData, maxCols, prefix, id, isActive) {

        let html = `
        <div class="berth-row berth-section ${isActive ? 'active' : ''}" id="${id}">
            <div class="berth-label">${prefix === 'U' ? 'Upper Berth' : 'Lower Berth'}</div>
            <div class="layout-box">
    `;

        if (!layoutData || Object.keys(layoutData).length === 0) {
            html += `<div>No seats</div>`;
        } else {

            Object.keys(layoutData).forEach(rowKey => {

                let row = layoutData[rowKey];

                Object.keys(row).forEach(colKey => {

                    let seat = row[colKey];

                    if (seat.seat_class == 1) {

                        let isSleeper = seat.berth_type == 2;

                        html += `
                        <label class="seat-wrap ${isSleeper ? 'sleeper-wrap' : ''}">
                            <input type="checkbox" class="seat-checkbox" 
                                   name="seat_no[]" value="${prefix}${seat.seat_text}">
                            
                            <span class="${isSleeper ? 'sleeper' : 'seat'}" 
                                  data-type="${isSleeper ? 'sleeper' : 'seat'}"></span>
                            
                            <span class="seat-number">
                                ${prefix}${seat.seat_text ?? ''}
                            </span>
                        </label>
                    `;

                    } else {
                        html += `<div class="empty"></div>`;
                    }

                });

            });
        }

        html += `</div></div>`;

        return html;
    }

    $(document).on('click', '.seat-wrap', function() {

        let checkbox = $(this).find('.seat-checkbox');
        let seat = $(this).find('.seat, .sleeper');

        setTimeout(() => {
            if (checkbox.is(':checked')) {
                seat.addClass('selected');
            } else {
                seat.removeClass('selected');
            }
        }, 0);
    });

    $(document).on('click', '.seat-tab-btn', function() {

        if (window.innerWidth <= 767) {

            let target = $(this).data('target');

            $('.seat-tab-btn').removeClass('active');
            $(this).addClass('active');

            $('.berth-section').removeClass('active');
            $('#' + target).addClass('active');
        }
    });
</script>
@endpush