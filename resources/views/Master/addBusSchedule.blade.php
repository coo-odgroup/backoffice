        @extends('admin.layouts.master')
        @section('page_title', 'Bus Schedule')
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
                <a href="{{ route('bus-schedule.index') }}" class="btn btn-success btn-sm">
                    View @yield('page_title')
                </a>
            </div>
        </div>

        <form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form">
            {{ csrf_field() }}

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3">
                                <div>
                                    <div class="row">

                                        <!-- Alerts -->
                                        @if (session('message'))
                                        <div
                                            class="alert alert-{{ session('level') ?? 'success' }} alert-dismissible fade show">
                                            {{ session('message') }}
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="alert"></button>
                                        </div>
                                        @endif

                                        @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="alert"></button>
                                        </div>
                                        @endif

                                        <div class="col-12">
                                            <div class="row">

                                                <!-- LEFT COLUMN -->
                                                <div class="col-md-5">
                                                    <div class="p-3 border rounded bg-white">
                                                        <div class="mb-2">
                                                            <label for="operator">Operator<span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm" id="operator"
                                                                name="operator"></select>
                                                        </div>

                                                        <div class="mb-2">
                                                            <label for="bus">Bus<span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm" id="bus"
                                                                name="bus"></select>
                                                        </div>

                                                        <div class="mb-2">
                                                            <label for="schedule_type">
                                                                Schedule Type
                                                                <span class="text-danger">*</span>  
                                                            </label>
                                                            <select
                                                                class="form-select form-select-sm" id="schedule_type" name="schedule_type">
                                                                <option value="">Select Schedule Type</option>
                                                                <option value="daily">Daily</option>
                                                                <option value="weekly">Weekly</option>
                                                                <option value="custom">Custom</option>
                                                            </select>
                                                        </div>

                                                        <!-- WEEKLY DAYS -->
                                                        <div class="mb-2 d-none" id="weekDaysWrapper">

                                                            <label class="form-label">
                                                                Select Days
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="d-flex flex-wrap gap-2">

                                                                <input type="checkbox"
                                                                    class="btn-check"
                                                                    id="day_sun"
                                                                    name="week_days[]"
                                                                    value="1">
                                                                <label class="btn btn-outline-primary btn-sm"
                                                                    for="day_sun">Sun</label>

                                                                <input type="checkbox"
                                                                    class="btn-check"
                                                                    id="day_mon"
                                                                    name="week_days[]"
                                                                    value="2">
                                                                <label class="btn btn-outline-primary btn-sm"
                                                                    for="day_mon">Mon</label>

                                                                <input type="checkbox"
                                                                    class="btn-check"
                                                                    id="day_tue"
                                                                    name="week_days[]"
                                                                    value="3">

                                                                <label class="btn btn-outline-primary btn-sm"
                                                                    for="day_tue">Tue</label>

                                                                <input type="checkbox"
                                                                    class="btn-check"
                                                                    id="day_wed"
                                                                    name="week_days[]"
                                                                    value="4">

                                                                <label class="btn btn-outline-primary btn-sm"
                                                                    for="day_wed">Wed</label>

                                                                <input type="checkbox"
                                                                    class="btn-check"
                                                                    id="day_thu"
                                                                    name="week_days[]"
                                                                    value="5">

                                                                <label class="btn btn-outline-primary btn-sm"
                                                                    for="day_thu">Thu</label>

                                                                <input type="checkbox"
                                                                    class="btn-check"
                                                                    id="day_fri"
                                                                    name="week_days[]"
                                                                    value="6">

                                                                <label class="btn btn-outline-primary btn-sm"
                                                                    for="day_fri">Fri</label>

                                                                <input type="checkbox"
                                                                    class="btn-check"
                                                                    id="day_sat"
                                                                    name="week_days[]"
                                                                    value="7">

                                                                <label class="btn btn-outline-primary btn-sm"
                                                                    for="day_sat">Sat</label>
                                                            </div>
                                                        </div>

                                                        <!-- CUSTOM DATES -->
                                                        <div class="mb-2 d-none" id="customDatesWrapper">

                                                            <label class="mb-2">
                                                                Custom Dates
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <input
                                                                type="text"
                                                                id="custom_dates_calendar"
                                                                class="form-control form-control-sm">

                                                            <input
                                                                type="hidden"
                                                                id="custom_dates"
                                                                name="custom_dates">

                                                        </div>

                                                        <!-- RUNNING CYCLE -->
                                                        <div class="mb-2 d-none" id="runningCycleWrapper">

                                                            <label for="running_cycle">
                                                                Running Cycle
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <select class="form-select form-select-sm"
                                                                id="running_cycle"
                                                                name="running_cycle">

                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <option value="{{ $i }}">
                                                                    {{ $i }}
                                                                    </option>
                                                                    @endfor

                                                            </select>

                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="from_date">
                                                                From Date
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="date" name="from_date" id="from_date" class="form-control form-control-sm">
                                                        </div>
                                                    </div>

                                                </div>

                                                <!-- RIGHT COLUMN -->
                                                <div class="col-md-7">
                                                    <div class="border rounded schedule-card">
                                                        <div class="card-header schedule-header">
                                                            <strong>Date Schedule List</strong>
                                                        </div>
                                                        <div class="card-body" id="scheduleContainer">

                                                            <div id="scheduleTemplate">

                                                                @if (!empty($data['scheduleDates']) && count($data['scheduleDates']) > 0)

                                                                @php
                                                                $chunkSize = ceil(
                                                                count($data['scheduleDates']) / 3,
                                                                );
                                                                $chunks = array_chunk(
                                                                $data['scheduleDates'],
                                                                $chunkSize,
                                                                );
                                                                @endphp

                                                                <div class="row">
                                                                    @foreach ($data['scheduleDates'] as $date)
                                                                    <div class="col-md-4 mb-2">
                                                                        <div class="date-box text-center p-2 border rounded bg-light">
                                                                            {{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}
                                                                        </div>
                                                                    </div>
                                                                    @endforeach
                                                                </div>
                                                                @else
                                                                <div class="text-center text-muted">
                                                                    Bus is not scheduled
                                                                </div>
                                                                @endif

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="row mt-3">
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
            .date-pill {
                padding: 6px 10px;
                border-radius: 6px;
                border: 1px solid #adb5bd;
                background: #e9ecef;
                font-size: 12px;
                white-space: nowrap;
            }

            .date-box {
                padding: 8px;
                border-radius: 6px;
                border: 1px solid #adb5bd;
                background: #f8f9fa;
                font-size: 13px;
            }

            /* CUSTOM CALENDAR DESIGN */

            #customDatesWrapper .flatpickr-calendar {
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
                border: 1px solid #dee2e6;
                border-radius: 12px;
                padding: 12px;
                background: #fff;
            }

            #customDatesWrapper .flatpickr-months {
                margin-bottom: 10px;
            }

            #customDatesWrapper .flatpickr-current-month {
                font-size: 16px;
                font-weight: 600;
                color: #212529;
            }

            #customDatesWrapper .flatpickr-weekday {
                color: #0d6efd;
                font-weight: 600;
                font-size: 12px;
            }

            #customDatesWrapper .flatpickr-day {
                border-radius: 8px;
                height: 42px;
                line-height: 42px;
                max-width: 42px;
                font-size: 13px;
                transition: all 0.2s ease;
            }

            #customDatesWrapper .flatpickr-day:hover {
                background: #e7f1ff;
                border-color: #0d6efd;
            }

            #customDatesWrapper .flatpickr-day.today {
                border: 1px solid #0d6efd;
                color: #0d6efd;
            }

            #customDatesWrapper .flatpickr-day.selected,
            #customDatesWrapper .flatpickr-day.startRange,
            #customDatesWrapper .flatpickr-day.endRange {
                background: #0d6efd !important;
                border-color: #0d6efd !important;
                color: #fff !important;
            }

            #customDatesWrapper .flatpickr-prev-month,
            #customDatesWrapper .flatpickr-next-month {
                padding: 6px;
                border-radius: 6px;
            }

            #customDatesWrapper .flatpickr-prev-month:hover,
            #customDatesWrapper .flatpickr-next-month:hover {
                background: #f1f3f5;
            }

            #customDatesWrapper .flatpickr-input {
                display: none !important;
            }
        </style>

        @endsection

        @push('scripts')
        <script type="module">
            let isRestoring = false;
            let selectedOperators = [];

            function freezePage() {
                $('body').css({
                    'pointer-events': 'none',
                    'opacity': '0.6'
                });

                if ($('#globalFreezeLoader').length === 0) {
                    $('body').append(`
                    <div id="globalFreezeLoader" style="
                        position:fixed;
                        top:50%;
                        left:50%;
                        transform:translate(-50%, -50%);
                        z-index:9999;
                    ">
                        <div class="spinner-border text-primary"></div>
                    </div>
                `);
                }
            }

            function unfreezePage() {
                $('body').css({
                    'pointer-events': '',
                    'opacity': ''
                });

                $('#globalFreezeLoader').remove();
            }

            $('#backoffice-form').on('submit', function(e) {

                let operator = $('#operator').val();
                let bus = $('#bus').val();
                let scheduleType = $('#schedule_type').val();
                let cycle = $('#running_cycle').val();
                let fromDate = $('#from_date').val();
                let customDates = $('#custom_dates').val();
                let weekDays = $('input[name="week_days[]"]:checked').length;
                let yesterday = new Date();
                yesterday.setDate(yesterday.getDate() - 1);

                let minDate = yesterday.toISOString().split('T')[0];

                if (!operator) {
                    commonAjax.viewAlert(
                        "Please select operator",
                        "warning"
                    );
                    e.preventDefault();
                    return;
                }
                if (!bus) {
                    commonAjax.viewAlert("Please select bus", "warning");
                    e.preventDefault();
                    return;
                }
                if (!scheduleType) {
                    commonAjax.viewAlert("Please select schedule type", "warning");
                    e.preventDefault();
                    return;
                }
                if (scheduleType === 'daily') {
                    if (!cycle) {
                        commonAjax.viewAlert("Please select running cycle", "warning");
                        e.preventDefault();
                        return;
                    }
                }
                if (scheduleType === 'weekly') {

                    if (weekDays <= 0) {
                        commonAjax.viewAlert("Please select at least one weekly day", "warning");
                        e.preventDefault();
                        return;
                    }
                }
                if (scheduleType === 'custom') {

                    if (!customDates) {
                        commonAjax.viewAlert("Please select custom dates", "warning");
                        e.preventDefault();
                        return;
                    }
                }
                if (!fromDate) {
                    commonAjax.viewAlert("Please select from date", "warning");
                    e.preventDefault();
                    return;
                }
                if (fromDate < minDate) {
                    commonAjax.viewAlert("From date cannot be before yesterday", "warning");
                    e.preventDefault();
                    return;
                }
            });

            function waitForOptions(selector, callback, retry = 0) {

                if ($(selector + ' option').length > 1) {
                    callback();
                    return;
                }

                if (retry >= 80) return;

                setTimeout(function() {
                    waitForOptions(selector, callback, retry + 1);
                }, 100);
            }

            $(document).ready(function() {

                // Select2 init
                $('#operator').select2({
                    placeholder: "Select Bus Operator",
                    dropdownParent: $('body')
                });

                $('#bus').select2({
                    placeholder: "Select Bus",
                    dropdownParent: $('body')
                });

                freezePage();

                toggleScheduleFields();

                $('#schedule_type').on('change', function() {

                    toggleScheduleFields();

                });

                commonAjax.loadBusOperatorDropdown();

                waitForOptions('#operator', function() {
                    unfreezePage();
                    restoreSelection();
                });

                setTimeout(unfreezePage, 5000);

                waitForOptions('#operator', function() {
                    restoreSelection();
                });

                commonAjax.initClearableInputs();

                $('#bus').on('focus', function() {
                    let operator_id = $('#operator').val();
                    if (!operator_id) {
                        commonAjax.viewAlert("Please select operator first", "warning");
                        $(this).blur();
                    }
                });

                let today = new Date();
                today.setDate(today.getDate() - 1);
                $('#from_date').attr(
                    'min',
                    today.toISOString().split('T')[0]
                );

                $('#to_date').attr(
                    'min',
                    today.toISOString().split('T')[0]
                );

                $('#btnReset').click(function() {
                    $('#backoffice-form')[0].reset();
                    $('.form-select').val('').trigger('change');
                    selectedOperators = [];
                    renderOperators();
                    $('#scheduleContainer').html('');
                });



            });

            function toggleScheduleFields() {

                let type = $('#schedule_type').val();

                $('#weekDaysWrapper').addClass('d-none');
                $('#customDatesWrapper').addClass('d-none');
                $('#runningCycleWrapper').addClass('d-none');

                if (type === 'weekly') {
                    $('#weekDaysWrapper').removeClass('d-none');

                }

                if (type === 'custom') {

                    $('#customDatesWrapper').removeClass('d-none');
                    setTimeout(function() {

                        if (window.customDatePicker) {
                            window.customDatePicker.redraw();
                        }

                    }, 200);

                }

                if (type === 'daily') {

                    $('#runningCycleWrapper').removeClass('d-none');

                }
            }

            setTimeout(function() {

                window.customDatePicker = flatpickr(
                    "#custom_dates_calendar", {

                        inline: true,
                        static: true,
                        mode: "multiple",
                        dateFormat: "Y-m-d",
                        minDate: "today",
                        monthSelectorType: "static",
                        onChange: function(selectedDates, dateStr) {
                            $('#custom_dates').val(dateStr);

                        }

                    }
                );

            }, 300);

          $('#operator').on('change', function() {

    let operator_id = $(this).val();

    if (!operator_id) return;

    // prevent clearing during edit restore
    if (!isRestoring) {

        $('#bus').html('');

    }

    freezePage();

    commonAjax.loadBusListByOperator(
        '#bus',
        operator_id
    );

    waitForOptions('#bus', function() {

        // edit mode auto select
        if (
            isRestoring &&
            selectedBus
        ) {

            $('#bus')
                .val(String(selectedBus))
                .trigger('change');

        }

        unfreezePage();

    });

    setTimeout(unfreezePage, 5000);
});


            $('#bus').on('change', function() {

                let bus_id = $(this).val();
                if (!bus_id || isRestoring) return;
                loadSchedule(bus_id);
            });

            let scheduleRequest = null;

            function loadSchedule(bus_id) {

                $('#scheduleContainer').html(`
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Loading schedule...</p>
                    </div>
                `);

                if (scheduleRequest) {
                    scheduleRequest.abort();
                }

                scheduleRequest = $.ajax({
                    type: "POST",
                    url: "/admin/get-schedule-dates",
                    data: {
                        bus_id: bus_id,
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {

                        if (typeof response === 'object') {

                            $('#scheduleContainer').html(response.html);


                            if (response.running_cycle) {
                                $('#running_cycle').val(response.running_cycle).trigger('change');
                            }

                            if (response.last_date) {
                                $('#from_date').val(response.last_date);
                            }

                        } else {
                            $('#scheduleContainer').html(response);
                        }
                    }
                });
            }


            let selectedOperator = "{{ $data['row']->operator_id ?? (old('operator') ?? '') }}";
            let selectedBus = "{{ $data['row']->bus_id ?? (old('bus') ?? '') }}";
            let lastScheduleDate = "{{ $data['lastDate'] ?? '' }}";
            let selectedCycle = "{{ $data['row']->running_cycle ?? '' }}";
            let selectedScheduleType = "{{ $data['row']->schedule_type ?? old('schedule_type') ?? '' }}";
            let selectedWeekDays = @json($data['selectedWeekDays'] ?? []);
            let selectedCustomDates = @json($data['selectedCustomDates'] ?? []);


            function restoreSelection() {

                if (!selectedOperator) return;

                isRestoring = true;
                $('#operator').val(String(selectedOperator)).trigger('change.select2');

                freezePage();
                commonAjax.loadBusListByOperator('#bus', selectedOperator);

                let retry = 0;
                let interval = setInterval(function() {

                    retry++;

                    let optionExists =
                        $('#bus option[value="' + selectedBus + '"]').length > 0;

                    if (optionExists) {

                        clearInterval(interval);
                        $('#bus').val(String(selectedBus)).trigger('change');
                        $('#bus').trigger({
                            type: 'select2:select'
                        });
                        setTimeout(function() {

                            $('#bus').val(String(selectedBus));
                            loadSchedule(selectedBus);

                        }, 300);

                        if (selectedScheduleType) {

                            setTimeout(function() {

                                $('#schedule_type')
                                    .val(selectedScheduleType);

                                $('#schedule_type').trigger('change');

                                toggleScheduleFields();

                                // DAILY
                                if (
                                    selectedScheduleType === 'daily' &&
                                    selectedCycle
                                ) {

                                    $('#runningCycleWrapper')
                                        .removeClass('d-none');

                                    $('#running_cycle')
                                        .val(selectedCycle)
                                        .trigger('change');
                                }

                                // WEEKLY
                                if (
                                    selectedScheduleType === 'weekly' &&
                                    selectedWeekDays.length > 0
                                ) {

                                    $('#weekDaysWrapper')
                                        .removeClass('d-none');

                                    $('input[name="week_days[]"]')
                                        .prop('checked', false);

                                    selectedWeekDays.forEach(function(day) {

                                        $('input[name="week_days[]"][value="' + day + '"]')
                                            .prop('checked', true);

                                    });
                                }

                                // CUSTOM
                                if (
                                    selectedScheduleType === 'custom' &&
                                    selectedCustomDates.length > 0
                                ) {

                                    $('#customDatesWrapper')
                                        .removeClass('d-none');

                                    $('#custom_dates')
                                        .val(selectedCustomDates.join(','));

                                    setTimeout(function() {

                                        if (window.customDatePicker) {

                                            window.customDatePicker.setDate(
                                                selectedCustomDates,
                                                true
                                            );
                                        }

                                    }, 500);
                                }

                            }, 300);
                        }




                        if (selectedOperator && lastScheduleDate) {
                            $('#from_date').val(lastScheduleDate);

                        }
                        $('#operator').next('.select2-container').css('pointer-events', 'none').css('opacity', '0.8');
                        $('#bus').next('.select2-container').css('pointer-events', 'none').css('opacity', '0.8');

                        $('#bus').prop('readonly', true);
                        $('#bus').prop('disabled', false);

                        isRestoring = false;
                        unfreezePage();
                    }

                    if (retry > 80) {

                        clearInterval(interval);
                        isRestoring = false;
                        unfreezePage();
                    }

                }, 100);
            }


            @if(session('level') == 'success')

            let bus_id = "{{ old('bus') }}";

            if (bus_id) {
                waitForOptions('#bus', function() {
                    loadSchedule(bus_id);
                });
            }

            @endif

            function renderOperators() {

                let html = '';

                selectedOperators.forEach((op, index) => {
                    html += `<span class="selected-tag" data-index="${index}">
                    ${op.text}
                    <span class="remove">×</span>
                 </span>`;
                });

                $('#selectedOperators').html(html);
                $('#operator_ids').val(selectedOperators.map(op => op.id).join(','));
                $('#selectedOperatorsWrapper').toggle(selectedOperators.length > 0);
            }
        </script>

        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        @endpush