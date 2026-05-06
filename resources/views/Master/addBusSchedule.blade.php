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
                                                            <label for="running_cycle">Running Cycle<span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm"
                                                                id="running_cycle" name="running_cycle">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <option value="{{ $i }}">
                                                                    {{ $i }}</option>
                                                                    @endfor
                                                            </select>
                                                        </div>

                                                        <div class="mb-2">
                                                            <label for="date">Date<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="date" name="date" id="date"
                                                                class="form-control form-control-sm">
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
                let cycle = $('#running_cycle').val();
                let date = $('#date').val();

                let yesterday = new Date();
                yesterday.setDate(yesterday.getDate() - 1);
                let minDate = yesterday.toISOString().split('T')[0];

                if (!operator) {
                    commonAjax.viewAlert("Please select operator", "warning");
                    e.preventDefault();
                    return;
                }

                if (!bus) {
                    commonAjax.viewAlert("Please select bus", "warning");
                    e.preventDefault();
                    return;
                }

                if (!cycle) {
                    commonAjax.viewAlert("Please select running cycle", "warning");
                    e.preventDefault();
                    return;
                }

                if (!date) {
                    commonAjax.viewAlert("Please select date", "warning");
                    e.preventDefault();
                    return;
                }

                if (date < minDate) {
                    commonAjax.viewAlert("Date cannot be before yesterday", "warning");
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
                $('#date').attr('min', today.toISOString().split('T')[0]);

                $('#btnReset').click(function() {
                    $('#backoffice-form')[0].reset();
                    $('.form-select').val('').trigger('change');
                    selectedOperators = [];
                    renderOperators();
                    $('#scheduleContainer').html('');
                });



            });

            $('#operator').on('change', function() {

                let operator_id = $(this).val();
                if (!operator_id || isRestoring) return;

                $('#bus').html('');

                freezePage();

                commonAjax.loadBusListByOperator('#bus', operator_id);

                waitForOptions('#bus', function() {
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
                                $('#date').val(response.last_date);
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


            function restoreSelection() {

                if (!selectedOperator) return;

                isRestoring = true;

                $('#operator').val(selectedOperator).trigger('change.select2');

                commonAjax.loadBusListByOperator('#bus', selectedOperator);

                waitForOptions('#bus', function() {

                    if (selectedBus) {
                        $('#bus').val(selectedBus).trigger('change.select2');
                        loadSchedule(selectedBus);
                    }

                    if (selectedCycle) {
                        $('#running_cycle').val(selectedCycle);
                    }

                    if (lastScheduleDate) {
                        $('#date').val(lastScheduleDate);
                    }

                    if (selectedOperator && selectedBus) {

                        $('#operator').next('.select2-container')
                            .css('pointer-events', 'none')
                            .css('opacity', '0.6')
                            .css('cursor', 'not-allowed');

                        $('#bus').next('.select2-container')
                            .css('pointer-events', 'none')
                            .css('opacity', '0.6')
                            .css('cursor', 'not-allowed');
                    }

                    isRestoring = false;
                });
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
        @endpush