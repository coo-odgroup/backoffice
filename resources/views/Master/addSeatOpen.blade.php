    @extends('admin.layouts.master')
    @section('page_title', 'Seat Open')
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
            <a href="{{route('seat-open.index') }}" class="btn btn-success btn-sm">
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
                                                        <select class="form-select form-select-sm annexture" id="reason" name="reason"></select>
                                                    </div>
                                                </div>

                                                <div class="p-3 border rounded bg-white mt-2">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <strong>Opened Seat History</strong>
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

                                                    <div class="card-body" id="seatOpenScheduleContainer">
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
                                                                    <small>Open Seat</small>
                                                                </div>


                                                                <div class="legend-item">
                                                                    <span class="legend-box legend-grey"></span>
                                                                    <small>Unavailable</small>
                                                                </div>


                                                                <div class="legend-item">
                                                                    <span class="legend-box legend-white"></span>
                                                                    <small>Available For Open</small>
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

                                    <div class="row mt-2">
                                        <div class="col-12 d-flex gap-2">
                                            <button class="btn btn-primary btn-sm" type="submit">
                                                {{ $data['strSubmit'] }}
                                            </button>
                                            <button class="btn btn-secondary btn-sm"
                                                id="btnReset"
                                                type="button"
                                                onclick="handlePageCancel()">
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
    @endsection

    @push('scripts')
    <script type="module">
        let isRestoring = false;

        let selectedOperator = "{{ $data['editData']->bus_operator_id ?? (old('operator') ?? '') }}";
        let selectedBus = "{{ $data['editData']->bus_id ?? (old('bus') ?? '') }}";
        let selectedReason = "{{ $data['editData']->reason ?? (old('reason') ?? '') }}";
        let selectedEditDate = "{{ $data['editDate'] ?? '' }}";


        $(document).ready(function() {

            commonAjax.initSelect2('#operator', 'Select Operator');
            commonAjax.initSelect2('#bus', 'Select Bus');
            commonAjax.initSelect2('#reason', 'Select Reason');

            // 🔥 SHOW LOADER FOR OPERATOR
            $('#operator').closest('.mb-2').append(`
                <div id="operatorLoader" class="text-center mt-2">
                    <div class="spinner-border text-primary"></div>
                </div>
            `);

            commonAjax.loadBusOperatorDropdown('');

            // 🔥 WAIT + REMOVE LOADER
            waitForOptions('#operator', function() {
                $('#operatorLoader').remove();
                restoreSelection();
            });

            commonAjax.loadAnnextureList('REASON', '', '#reason');
            commonAjax.initClearableInputs();
        });

        function restoreSelection() {

            if (!selectedOperator) return;

            isRestoring = true;



            $('#operator').val(selectedOperator).trigger('change');

            $('#seatLayoutContainer').html(`
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary"></div>
                        <p>Loading seat layout...</p>
                    </div>
             `);

            commonAjax.loadBusListByOperator('#bus', selectedOperator, selectedBus);

            waitForOptions('#bus', function() {

                $('#bus').val(selectedBus).trigger('change');

                $('#reason option').each(function() {
                    if ($(this).text().trim() == selectedReason.trim()) {
                        $('#reason').val($(this).val()).trigger('change');
                    }
                });

                loadSeatBlockSchedules(true);
                loadSeatLayoutByBus(selectedEditDate);
                loadBlockedSeatHistory();

                /* 🔥 SAME AS SEAT BLOCK */
                $('#operator').next('.select2-container')
                    .css('pointer-events', 'none')
                    .css('opacity', '0.6');

                $('#bus').next('.select2-container')
                    .css('pointer-events', 'none')
                    .css('opacity', '0.6');

                isRestoring = false;
            });
        }

        function waitForOptions(selector, callback, retry = 0) {

            if ($(selector + ' option').length > 1) {
                callback();
                return;
            }

            if (retry > 50) return;

            setTimeout(function() {
                waitForOptions(selector, callback, retry + 1);
            }, 200);
        }


        $('#operator').on('change', function() {

            let operator_id = $(this).val();

            if (!operator_id || isRestoring) return;

            $('#bus').html('');

            $('#bus').closest('.mb-2').append(`
                <div id="busLoader" class="text-center mt-2">
                    <div class="spinner-border text-primary"></div>
                </div>
            `);

            $('#seatOpenScheduleContainer').html(`
                <div class="text-center text-muted">
                    Please select bus
                </div>
            `);

            commonAjax.loadBusListByOperator('#bus', operator_id);

            waitForOptions('#bus', function() {
                $('#busLoader').remove();
            });

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

            // grey locked seats
            if ($el.hasClass('disabled')) return;

            // blue selected -> white
            if ($el.hasClass('selected-seat')) {

                $el.removeClass('selected-seat changed-seat')
                    .addClass('openable');

            }

            // white -> blue selected
            else if ($el.hasClass('openable')) {

                $el.removeClass('openable')
                    .addClass('selected-seat changed-seat');
            }
        }

        function loadSeatBlockSchedules(isEditMode = false) {
            let operator = $('#operator').val();
            let bus = $('#bus').val();

            if (!operator || !bus) {
                $('#seatOpenScheduleContainer').html(`
                    <div class="text-center text-muted">
                        Please select operator and bus
                    </div>
                `);
                return;
            }

            let today = new Date();
            let year = today.getFullYear();
            let month = today.getMonth() + 1;

            $('#seatOpenScheduleContainer').html(`
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
                        $('#seatOpenScheduleContainer').html(`
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


        window.handlePageCancel = function() {

            let isEditPage = @json(request() -> routeIs('seat-open.edit'));

            if (isEditPage) {
                window.location.href = "{{route('seat-open.index') }}";
                return;
            }

            document.getElementById('backoffice-form').reset();

            $('.form-select').val('').trigger('change');
        };


        function renderSchedule(data, isEditMode = false) {

            let operator = $('#operator').val();
            let busId = $('#bus').val();

            $.ajax({
                type: 'POST',
                url: '/admin/get-bus-cancelled-dates',
                data: {
                    operator_id: operator,
                    bus_id: busId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function(cancelRes) {

                    let cancelledDates = cancelRes.data || [];
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

                        /* EDIT MODE = SHOW ONLY ONE DATE */
                        if (isEditMode && selectedEditDate) {

                            html += `
                                <div class="col-md-12 mb-2">
                                    <label class="schedule-tile normal-inline-tile disabled-date-tile">
                                        <input type="checkbox"
                                            checked
                                            disabled 
                                            name="dates[]"
                                            value="${selectedEditDate}"
                                            class="schedule-checkbox"
                                            data-bus="${bus_id}"
                                            data-date="${selectedEditDate}">
                                        <span class="tile-date">${formatDate(selectedEditDate)}</span>
                                    </label>
                                </div>
                            `;
                        }

                        /* ADD MODE = SHOW FULL LIST */
                        else {

                            bus.dates.forEach(function(date) {

                                let isCancelled = cancelledDates.includes(date);

                                let today = new Date();
                                today.setHours(0, 0, 0, 0);

                                let currentDate = new Date(date);
                                currentDate.setHours(0, 0, 0, 0);

                                let isPastDate = currentDate < today;

                                if (isCancelled) {

                                    html += `
                                        <div class="col-md-4 mb-2">
                                            <div class="schedule-tile cancelled-inline-tile">
                                                <span class="cancel-x">X</span>
                                                <span class="tile-date">${formatDate(date)}</span>
                                                <span class="cancel-note">Bus Cancelled</span>
                                            </div>
                                        </div>
                                        `;

                                } else if (isPastDate) {

                                    html += `
                                    <div class="col-md-4 mb-2">
                                        <div class="schedule-tile disabled-date-tile">
                                            <span class="tile-date">${formatDate(date)}</span>
                                        </div>
                                    </div>
                                    `;

                                } else {

                                    html += `
                                    <div class="col-md-4 mb-2">
                                        <label class="schedule-tile normal-inline-tile">
                                            <input type="checkbox"
                                                name="dates[]"
                                                value="${date}"
                                                class="schedule-checkbox"
                                                data-bus="${bus_id}"
                                                data-date="${date}">
                                            <span class="tile-date">${formatDate(date)}</span>
                                        </label>
                                    </div>
                                    `;
                                }

                            });
                        }

                        html += `
                                </div>
                            </div>
                        `;
                    });

                    $('#seatOpenScheduleContainer').html(html);

                    if (isEditMode && selectedEditDate) {

                        $('.schedule-checkbox').each(function() {
                            if ($(this).val() == selectedEditDate) {
                                $(this).prop('checked', true);
                            }
                        });

                    }

                }
            });
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
                url: "{{ route('seat-open.layout.by.bus') }}",
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

            if (!operator || operator == 0) {
                commonAjax.viewAlert('Please select Operator');
                $('#operator').focus();
                return false;
            }

            if (!bus || bus == 0) {
                commonAjax.viewAlert('Please select Bus');
                $('#bus').focus();
                return false;
            }

            if (!reason || reason == 0) {
                commonAjax.viewAlert('Please select a Reason');
                $('#reason').focus();
                return false;
            }

            $('.schedule-checkbox:checked').each(function() {
                selectedDates.push($(this).val());
            });

            if (selectedDates.length === 0) {
                commonAjax.viewAlert('Please select Schedule Date');
                return false;
            }

            console.log(seatData);

            $('.selected-seat').each(function() {

                let seatCode =
                    $(this).data('seatcode') ||
                    $(this).attr('data-seatcode') ||
                    $(this).find('.seat-checkbox').val() ||
                    $(this).closest('label').find('.seat-checkbox').val() ||
                    $(this).next('.seat-no').text().trim() ||
                    $(this).closest('.seat-wrap').find('.seat-no').text().trim();

                if (!seatCode) return;

                let busSeatId = $(this).data('busseatid') || 0;
                let layoutId = $(this).data('layout') || 0;

                selectedDates.forEach(function(date) {
                    seatData.push({
                        bus_seat_id: busSeatId,
                        seat_code: seatCode,
                        seat_layout_id: layoutId,
                        operation_date: date,
                        category: 1
                    });
                });

            });


            console.log(seatData);

            $('#seat_operations').val(JSON.stringify(seatData));

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
                url: "{{ route('seat-open.history') }}",
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