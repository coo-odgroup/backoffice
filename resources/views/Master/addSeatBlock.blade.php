    @extends('admin.layouts.master')
    @section('page_title', 'Seat Block')
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

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 id="page_title">@yield('page_title')</h5>
        <div>
            <a href="{{ route('seat-block.index') }}" class="btn btn-success btn-sm">
                View @yield('page_title')
            </a>
        </div>
    </div>

    <form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form">
        {{csrf_field()}}

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="mb-3">
                            <div class="card-body">
                                <div class="row">

                                    <!-- Alerts -->
                                    @if (session('message'))
                                    <div class="alert alert-{{ session('level') ?? 'success' }} alert-dismissible fade show">
                                        {{ session('message') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    @endif

                                    @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    @endif

                                    <div class="col-12">
                                        <div class="row">
                                            <input type="hidden" name="seat_operations" id="seat_operations">

                                            <!-- LEFT COLUMN -->
                                            <div class="col-md-5">
                                                <div class="p-3 border rounded bg-white">
                                                    <div class="mb-2">
                                                        <label for="operator">Operator <span class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm" id="operator" name="operator"></select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="bus">Bus <span class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm" id="bus" name="bus"></select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="reason">Reason <span class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm" id="reason" name="reason"></select>
                                                    </div>
                                                </div>

                                                <div class="p-3 border rounded bg-white mt-2">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <strong>Blocked Seat History</strong>
                                                    </div>

                                                    <div id="blockedSeatHistoryContainer">
                                                        <div class="text-center text-muted">
                                                            Please select Operator and Bus
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-7">
                                                <div class="border rounded bg-white">
                                                    <div class="card-header">
                                                        <strong>Schedule Date List</strong>
                                                    </div>

                                                    <div class="card-body" id="scheduleContainer">
                                                        <div class="text-center text-muted">
                                                            Please select Operator and Bus
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="border rounded bg-white mt-2">
                                                    <div class="card-header">
                                                        <strong id="layoutTitle">Seat Layout</strong>
                                                    </div>

                                                    <div class=" rounded bg-white mt-2">

                                                        <!-- STATIC LEGEND -->
                                                        <div class="px-3 py-2  bg-light">
                                                            <div class="seat-legend d-flex flex-wrap gap-3">

                                                                <div class="legend-item">
                                                                    <span class="legend-box" style="background:#001a57;"></span>
                                                                    <small>Active Seat</small>
                                                                </div>

                                                                <div class="legend-item">
                                                                    <span class="legend-box legend-red"></span>
                                                                    <small>Blocked Seat</small>
                                                                </div>

                                                                <div class="legend-item">
                                                                    <span class="legend-box legend-grey"></span>
                                                                    <small>Unavailable</small>
                                                                </div>

                                                            </div>
                                                        </div>

                                                        <!-- AJAX ONLY CHANGES THIS -->
                                                        <div class="card-body" id="seatLayoutContainer">
                                                            <div class="text-center text-muted">
                                                                Please select bus
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="row mt-2">
                                        <div class="col-12 d-flex gap-2">
                                            <button class="btn btn-primary btn-sm" type="submit">
                                                {{ $data['strSubmit'] }}
                                            </button>
                                            <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                                {{ $data['strReset'] }}
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
    </form>
    <style>
        #seatLayoutContainer {
            overflow-x: auto;
            padding: 10px;
        }

        .seat-item,
        .seat-wrap,
        label.seat-box {
            position: relative;
            display: inline-block;
            text-align: center;
            margin: 4px;
            cursor: pointer;
            vertical-align: top;
        }

        .seat-checkbox {
            display: none !important;
        }

        .seat-img {
            display: block;
            background-repeat: no-repeat;
            background-size: contain;
            background-position: center;
        }

        .seater {
            width: 42px;
            height: 42px;
        }

        .sleeper {
            width: 72px;
            height: 34px;
        }

        .sleeper-vertical {
            width: 38px;
            height: 72px;
        }

        .selected-seat.seater {
            background-image: url('/assets/seats/Seat_layout_blue.png') !important;
        }

        .selected-seat.sleeper {
            background-image: url('/assets/seats/Sleeper_layout_blue.png') !important;
        }

        .selected-seat.sleeper-vertical {
            background-image: url('/assets/seats/Sleeper_layout_blue_vertical.png') !important;
        }

        .seater {
            background-image: url('/assets/seats/seat_layout.png');
        }

        .sleeper {
            background-image: url('/assets/seats/sleeper_layout.png');
        }

        .sleeper-vertical {
            background-image: url('/assets/seats/sleeper_layout_vertical.png');
        }

        .seat-no {
            display: block;
            font-size: 10px;
            margin-top: 2px;
            color: #001a57;
            font-weight: 600;
        }

        .berth-label {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 12px;
            font-weight: 700;
            color: #001a57;
            padding: 10px 4px;
        }

        .seat-row {
            white-space: nowrap;
            margin-bottom: 10px;
        }

        .seat-box {
            cursor: pointer;
            transition: 0.2s ease;
        }

        .seat-box.selected-seat {
            filter: none;
            opacity: 1;
        }

        .seat-box.available-seat {
            filter: grayscale(0) brightness(1.12);
            opacity: 1;
        }

        .seat-box.disabled-seat {
            filter: grayscale(100%);
            opacity: .55;
            cursor: not-allowed;
            pointer-events: none;
        }

        .selected-seat {
            opacity: 1 !important;
        }

        .selected-seat.bus-seat,
        .selected-seat.seat-box.bus-seat {
            background-image: url('/assets/seats/Seat_layout_blue.png') !important;
        }

        .selected-seat.bus-sleeper,
        .selected-seat.seat-box.bus-sleeper {
            background-image: url('/assets/seats/Sleeper_layout_blue.png') !important;
        }

        .selected-seat.bus-vertical-sleeper,
        .selected-seat.seat-box.bus-vertical-sleeper {
            background-image: url('/assets/seats/Sleeper_layout_blue_vertical.png') !important;
        }

        .blocked.bus-seat {
            background-image: url('/assets/seats/Seat_layout_red.png') !important;
        }

        .blocked.bus-sleeper {
            background-image: url('/assets/seats/sleeper_layout_red.png') !important;
        }

        .blocked.bus-vertical-sleeper {
            background-image: url('/assets/seats/sleeper_layout_red_vertical.png') !important;
        }

        .seat-box {
            width: 42px;
            height: 24px;
            display: inline-block;
            cursor: pointer;
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
        }

        .seat-box.vertical {
            width: 24px;
            height: 42px;
        }

        .seat-box.sleeper {
            width: 72px;
            height: 28px;
        }

        .seat-box.sleeper.vertical {
            width: 34px;
            height: 72px;
        }


        .seat-box.open.seater {
            background-image: url('/assets/seats/Seat_layout_blue.png');
        }

        .seat-box.open.seater.vertical {
            background-image: url('/assets/seats/Seat_layout_blue_vertical.png');
        }

        .seat-box.open.sleeper {
            background-image: url('/assets/seats/Sleeper_layout_blue.png');
        }

        .seat-box.open.sleeper.vertical {
            background-image: url('/assets/seats/Sleeper_layout_blue_vertical.png');
        }

        .seat-box.blocked.seater {
            background-image: url('/assets/seats/Seat_layout_red.png');
        }

        .seat-box.blocked.seater.vertical {
            background-image: url('/assets/seats/Seat_layout_red_vertical.png');
        }

        .seat-box.blocked.sleeper {
            background-image: url('/assets/seats/sleeper_layout_red.png');
        }

        .seat-box.blocked.sleeper.vertical {
            background-image: url('/assets/seats/sleeper_layout_red_vertical.png');
        }

        .seat-box.disabled {
            cursor: not-allowed;
            pointer-events: none;
        }

        .seat-box.disabled.seater {
            background-image: url('/assets/seats/Seat_layout_grey.png');
        }

        .seat-box.disabled.seater.vertical {
            background-image: url('/assets/seats/Seat_layout_grey_vertical.png');
        }

        .seat-box.disabled.sleeper {
            background-image: url('/assets/seats/sleeper_layout_grey.png');
        }

        .seat-box.disabled.sleeper.vertical {
            background-image: url('/assets/seats/sleeper_layout_grey_vertical.png');
        }

        .selected-seat.bus-seat {
            background-image: url('/assets/seats/Seat_layout_blue.png') !important;
        }








        .bus-seat,
        .bus-sleeper,
        .bus-vertical-sleeper {
            display: inline-block;
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            cursor: pointer;
        }




        .blocked.bus-seat,
        .blocked.bus-sleeper,
        .blocked.bus-vertical-sleeper {
            background-image: url('/assets/seats/Seat_layout_red.png') !important;
        }

        .selected-seat.bus-seat,
        .selected-seat.bus-sleeper,
        .selected-seat.bus-vertical-sleeper {
            background-image: url('/assets/seats/Seat_layout_blue.png') !important;
        }


        /* ===== Final Seat Colors ===== */

        .disabled.bus-seat {
            background-image: url('/assets/seats/Seat_layout_grey.png') !important;
        }

        .disabled.bus-sleeper {
            background-image: url('/assets/seats/sleeper_layout_grey.png') !important;
        }

        .disabled.bus-vertical-sleeper {
            background-image: url('/assets/seats/sleeper_layout_grey_vertical.png') !important;
        }

        .disabled {
            cursor: not-allowed !important;
            opacity: 1 !important;
            pointer-events: none;
        }

        /* ===== Legend ===== */

        .seat-legend {
            align-items: center;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #334155;
        }

        .legend-box {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            display: inline-block;
        }

        .legend-blue {
            background: #0d6efd;
        }

        .legend-red {
            background: #dc3545;
        }

        .legend-grey {
            background: #adb5bd;
        }
    </style>

    @endsection

    @push('scripts')
    <script type="module">
        let isRestoring = false;

        let selectedOperator = "{{ $data['editData']->bus_operator_id ?? (old('operator') ?? '') }}";
        let selectedBus = "{{ $data['editData']->bus_id ?? (old('bus') ?? '') }}";
        let selectedReason = "{{ $data['editData']->reason ?? (old('reason') ?? '') }}";

        let selectedEditDate = "{{ $data['editDate'] ?? '' }}";

        // console.log('Selected Operator:', selectedOperator);
        // console.log('Selected Bus:', selectedBus);
        // console.log('Selected Reason:', selectedReason);



        $(document).ready(function() {

            commonAjax.initSelect2('#operator', 'Select Operator');
            commonAjax.initSelect2('#bus', 'Select Bus');
            commonAjax.initSelect2('#reason', 'Select Reason');

            commonAjax.loadBusOperatorDropdown('');
            commonAjax.loadAnnextureList('REASON', '', '#reason');

            setTimeout(() => {
                restoreSelection();
            }, 1000);

            commonAjax.initClearableInputs();

        });

        function restoreSelection() {

            if (!selectedOperator) return;

            isRestoring = true;

            $('#operator').val(selectedOperator).trigger('change');

            commonAjax.loadBusListByOperator('#bus', selectedOperator, selectedBus);

            setTimeout(() => {

                $('#bus').val(selectedBus).trigger('change');

                $('#reason option').each(function() {
                    if ($(this).text().trim() == selectedReason.trim()) {
                        $('#reason').val($(this).val()).trigger('change');
                    }
                });

                /* LOAD ALL TABLES LIKE NORMAL FLOW */
                loadSeatBlockSchedules(true);
                loadSeatLayoutByBus(selectedEditDate);
                loadBlockedSeatHistory();

                isRestoring = false;

            }, 800);
        }

        function waitForOptions(selector, callback, retry = 0) {

            if ($(selector + ' option').length > 1) {
                callback();
                return;
            }

            if (retry > 30) return;

            setTimeout(function() {
                waitForOptions(selector, callback, retry + 1);
            }, 200);
        }


        $('#operator').on('change', function() {

            let operator_id = $(this).val();

            if (!operator_id || isRestoring) return;

            $('#bus').html('');

            $('#scheduleContainer').html(`
        <div class="text-center text-muted">
            Please select bus
        </div>
    `);

            commonAjax.loadBusListByOperator('#bus', operator_id);

        });


        $('#bus').on('change', function() {

            let bus_id = $(this).val();

            if (!bus_id || isRestoring) return;

            loadSeatBlockSchedules();
            loadSeatLayoutByBus();
            loadBlockedSeatHistory();

        });

        window.toggleSeat = function(el) {

            const $el = $(el);

            if ($el.hasClass('disabled')) return;

            /* BLUE -> RED */
            if ($el.hasClass('selected-seat')) {

                $el.removeClass('selected-seat')
                    .addClass('blocked changed-seat');

            }

            /* RED -> BLUE */
            else if ($el.hasClass('blocked')) {

                $el.removeClass('blocked')
                    .addClass('selected-seat changed-seat');
            }
        }

        function loadSeatBlockSchedules(isEditMode = false) {
            let operator = $('#operator').val();
            let bus = $('#bus').val();

            if (!operator || !bus) {
                $('#scheduleContainer').html(`
                    <div class="text-center text-muted">
                        Please select operator and bus
                    </div>
                `);
                return;
            }

            let today = new Date();
            let year = today.getFullYear();
            let month = today.getMonth() + 1;

            $('#scheduleContainer').html(`
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary"></div>
                        <p>Loading schedules...</p>
                    </div>
                `);


            $.ajax({
                type: 'POST',
                url: '/admin/get-bus-schedule-by-month',
                data: {
                    operator_id: operator,
                    bus_ids: bus,
                    year: year,
                    month: month,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function(res) {
                    if (!res.status || !res.data) {
                        $('#scheduleContainer').html(`
                            <div class="text-danger text-center">
                                No Schedule Found
                            </div>
                        `);
                        return;
                    }

                    renderSchedule(res.data, isEditMode);
                }
            });

        }



        function renderSchedule(data, isEditMode = false) {
            let html = '';

            Object.keys(data).forEach(function(bus_id) {

                let bus = data[bus_id];

                html += `
                        <div class="mb-4">
                            <div class="mb-2">
                                <strong>${bus.bus_name} | ${bus.bus_number}</strong>
                            </div>

                            <div class="row">
                    `;

                bus.dates.forEach(function(date) {

                    html += `
                        <div class="col-md-4 mb-2">
                            <label class="w-100 border rounded p-2 text-center">

                                <input type="checkbox"
                                    name="dates[]"
                                    value="${date}"
                                    class="schedule-checkbox"
                                    data-bus="${bus_id}">

                                ${formatDate(date)}

                            </label>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;

            });

            $('#scheduleContainer').html(html);
            if (isEditMode && selectedEditDate) {

                $('.schedule-checkbox').each(function() {

                    if ($(this).val() == selectedEditDate) {
                        $(this).prop('checked', true);
                    }

                });

            }
        }


        function loadSeatLayoutByBus(operationDate = '') {
            let bus_id = $('#bus').val();

            if (!bus_id) {
                $('#layoutTitle').text('Seat Layout');
                $('#seatLayoutContainer').html(`
                    <div class="text-center text-muted">
                        Please select bus
                    </div>
                `);
                return;
            }

            $('#seatLayoutContainer').html(`
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary"></div>
                        <p>Loading seat layout...</p>
                    </div>
                `);

            $.ajax({
                type: 'POST',
                url: "{{ route('seat-block.layout.by.bus') }}",
                data: {
                    bus_id: bus_id,
                    operation_date: operationDate,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function(res) {
                    if (!res.status) {
                        $('#layoutTitle').text('Seat Layout');

                        $('#seatLayoutContainer').html(`
                            <div class="text-danger text-center">
                                ${res.message}
                            </div>
                        `);

                        return;
                    }

                    $('#layoutTitle').text(
                        'Seat Layout : ' + res.layout_name +
                        ' | ' + res.bus_name +
                        ' | ' + res.bus_number
                    );

                    $('#seatLayoutContainer').html(res.html);
                },

                error: function() {
                    $('#seatLayoutContainer').html(`
                <div class="text-danger text-center">
                    Unable to load layout
                </div>
            `);
                }
            });
        }


        function formatDate(dateStr) {
            let d = new Date(dateStr);

            return d.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        $('#backoffice-form').on('submit', function(e) {

            e.preventDefault();

            let operator = $('#operator').val();
            let bus = $('#bus').val();
            let reason = $('#reason').val();

            let selectedDates = [];
            let seatData = [];

            /* Operator */
            if (!operator || operator == 0) {
                commonAjax.viewAlert('Please select Operator');
                $('#operator').focus();
                return false;
            }

            /* Bus */
            if (!bus || bus == 0) {
                commonAjax.viewAlert('Please select Bus');
                $('#bus').focus();
                return false;
            }

            /* Reason */
            if (!reason || reason == 0) {
                commonAjax.viewAlert('Please select a Reason');
                $('#reason').focus();
                return false;
            }

            /* Dates */
            $('.schedule-checkbox:checked').each(function() {
                selectedDates.push($(this).val());
            });

            if (selectedDates.length === 0) {
                commonAjax.viewAlert('Please select Schedule Date');
                return false;
            }

            /* Seats */
            /* Seats */
            $('.changed-seat').each(function() {

                let seatCode = $(this).closest('label').find('.seat-checkbox').val();
                if (!seatCode) return;

                let busSeatId = $(this).data('busseatid') || 0;
                let layoutId = $(this).data('layout') || 0;

                let category = $(this).hasClass('blocked') ? 2 : 1;

                selectedDates.forEach(function(date) {

                    seatData.push({
                        bus_seat_id: busSeatId,
                        seat_code: seatCode,
                        seat_layout_id: layoutId,
                        operation_date: date,
                        category: category
                    });

                });

            });

            $('#seat_operations').val(JSON.stringify(seatData));

            /* Final confirmation */
            commonAjax.confirmAlert('Are you sure to proceed ?');

            $('#btnConfirmOk').off('click').on('click', function() {
                e.currentTarget.submit();
            });

        });

        function loadBlockedSeatHistory() {

            let operator = $('#operator').val();
            let bus = $('#bus').val();

            if (!operator || !bus) {
                $('#blockedSeatHistoryContainer').html(`
            <div class="text-center text-muted">
                Please select Operator and Bus
            </div>
        `);
                return;
            }

            $('#blockedSeatHistoryContainer').html(`
        <div class="text-center p-3">
            <div class="spinner-border text-primary"></div>
        </div>
    `);

            $.ajax({
                type: 'POST',
                url: "{{ route('seat-block.history') }}",
                data: {
                    bus_id: bus,
                    operation_date: selectedEditDate,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    $('#blockedSeatHistoryContainer').html(res.html);
                },
                error: function() {
                    $('#blockedSeatHistoryContainer').html(`
                <div class="text-danger text-center">
                    Unable to load history
                </div>
            `);
                }
            });
        }
    </script>
    @endpush