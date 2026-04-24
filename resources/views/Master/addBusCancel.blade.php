    @extends('admin.layouts.master')
    @section('page_title', 'Bus Cancel')
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
            <a href="{{ route('bus-cancel.index') }}" class="btn btn-success btn-sm">
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
                            <div class="card-body">
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

                                                <!-- FORM -->
                                                <div class="p-3 border rounded bg-white">

                                                    <div class="mb-2">
                                                        <label for="operator">Operator <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm"
                                                            id="operator" name="operator"></select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="bus">Bus <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm"
                                                            id="bus"></select>

                                                        <div id="selectedBusWrapper" class="mt-2"
                                                            style="display:none;">
                                                            <div id="selectedBuses"></div>
                                                        </div>

                                                        <input type="hidden" name="bus" id="bus_ids">
                                                    </div>

                                                    <div class="mb-2">
                                                        <div class="row">
                                                            <div class="col-xl-6">
                                                                <label for="year">Year <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm"
                                                                    id="year" name="year">
                                                                    <option value="">Select Year</option>
                                                                    @for ($i = date('Y'); $i <= date('Y') + 1; $i++)
                                                                        <option value="{{ $i }}">
                                                                        {{ $i }}</option>
                                                                        @endfor
                                                                </select>
                                                            </div>

                                                            <div class="col-xl-6">
                                                                <label for="month">Month <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm"
                                                                    id="month" name="month">
                                                                    <option value="">Select Month</option>
                                                                    @for ($m = 1; $m <= 12; $m++)
                                                                        <option value="{{ $m }}">
                                                                        {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                                                                        </option>
                                                                        @endfor
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="reason">Reason <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm annexture"
                                                            id="reason" name="reason"></select>
                                                    </div>

                                                    <div class="mb-2" id="otherReasonWrapper"
                                                        style="display:none;">
                                                        <label for="other_reason">Other Reason <span
                                                                class="text-danger">*</span></label>
                                                        <input type="hidden" name="removed_dates"
                                                            id="removed_dates">
                                                        <input type="text"
                                                            class="form-control form-control-sm"
                                                            id="other_reason" name="other_reason"
                                                            placeholder="Enter reason">
                                                    </div>

                                                </div>

                                                <!--  CANCELLED TABLE-->
                                                <div class="p-3 border rounded bg-white mt-2"
                                                    id="cancelledTableWrapper" style="display:none;">

                                                    <table
                                                        class="table table-hover table-bordered align-middle table-sm">
                                                        <thead class="table-secondary">
                                                            <tr>
                                                                <th>Sl No.</th>
                                                                <th>Bus Name/No</th>
                                                                <th>Bus Cancelled Dates</th>
                                                                <th>Reason</th>
                                                                <th>Cancelled By</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="cancelledTableBody"></tbody>
                                                    </table>

                                                </div>

                                            </div>


                                            <!-- RIGHT COLUMN -->
                                            <div class="col-md-7">
                                                <div class="border rounded bg-white">
                                                    <div class="card-header">
                                                        <strong>Date Schedule List</strong>
                                                    </div>

                                                    <div class="card-body" id="scheduleContainer">
                                                        <div class="text-center text-muted">
                                                            Please select operator, bus, year and month
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
                                            <button class="btn btn-secondary btn-sm" id="btnReset"
                                                type="button">
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
        .selected-tag {
            display: inline-flex;
            align-items: center;
            background: #ffc107;
            padding: 5px 10px;
            border-radius: 20px;
            margin: 3px;
        }

        .selected-tag .remove {
            margin-left: 6px;
            cursor: pointer;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
    @endsection

    @push('scripts')
    <script type="module">
        let oldData = {
            operator: "{{ old('operator') }}",
            buses: "{{ old('bus') }}",
            year: "{{ old('year') }}",
            month: "{{ old('month') }}"
        };

        let editData = {
            operator: "{{ $data['row']->bus_operator_id ?? '' }}",
            bus: "{{ $data['row']->bus_id ?? '' }}",
            year: "{{ $data['row']->year ?? '' }}",
            month: "{{ $data['row']->month ?? '' }}",
            reason: "{{ $data['row']->reason ?? '' }}",
            other_reason: "{{ $data['row']->other_reason ?? '' }}",
            loaded: false
        };


        let existing_reason_id = <?php echo isset($data['row']->reason) ? json_encode($data['row']->reason) : 'null'; ?>;
        let selectedBuses = [];
        let pageInitializing = true;

        let cancelledDatesMap = {};
        let removedCancelledDates = [];

        $(document).ready(function() {

            commonAjax.initSelect2('#operator', 'Select Operator');
            commonAjax.initSelect2('#bus', 'Select Bus');
            commonAjax.initSelect2('#reason', 'Select Reason');

            commonAjax.loadBusOperatorDropdown('');

            setTimeout(function() {
                $('#operator').val(operatorId).trigger('change');
            }, 300);
            // commonAjax.loadAnnextureList('REASON', editData.reason, '#reason');
            commonAjax.loadAnnextureList('REASON', editData.reason);

            let operatorId = String(editData.operator).trim();
            let busId = String(editData.bus).trim();


            setTimeout(function() {

                let option = $('#bus option[value="' + busId + '"]');

                if (option.length > 0) {

                    $('#bus').val(busId);

                    selectedBuses = [{
                        id: busId,
                        text: option.text()
                    }];

                    if (selectedBuses) {

                        renderBuses();
                    }

                    $('#bus_ids').val(busId);
                }

                $('#year').val(editData.year);
                $('#month').val(editData.month);
                $('#reason').val(editData.reason);

                if (editData.reason == 7) {
                    $('#otherReasonWrapper').show();
                    console.log('sahil', editData.other_reason);
                    $('#other_reason').val(editData.other_reason);
                }

                editData.loaded = true;
                pageInitializing = false;
                refreshAllData();
            }, 1000); // increase to 1500 if slow ajax
        });



        $('#operator').on('change', function() {

            let operator_id = $(this).val();
            if (!operator_id) return;

            commonAjax.loadBusListByOperator('#bus', operator_id, function() {

                if (!pageInitializing) {
                    selectedBuses = [];
                    renderBuses();
                    $('#bus_ids').val('');
                }

            });

        });


        $('#bus').on('change', function() {

            let id = $(this).val();
            let text = $("#bus option:selected").text();

            if (!id) return;

            if (selectedBuses.some(b => b.id == id)) return;

            selectedBuses.push({
                id,
                text
            });

            renderBuses();

            $(this).val('').trigger('change');

            refreshAllData();
        });

        $(document).on('click', '#selectedBuses .remove', function() {

            let index = $(this).closest('.selected-tag').data('index');

            selectedBuses.splice(index, 1);

            renderBuses();

            refreshAllData();
        });

        function renderBuses() {

            let html = '';

            selectedBuses.forEach((bus, index) => {
                html += `
                <span class="selected-tag" data-index="${index}">
                    ${bus.text}
                    <span class="remove">×</span>
                </span>
                `;
            });

            $('#selectedBuses').html(html);

            $('#bus_ids').val(selectedBuses.map(b => b.id).join(','));

            $('#selectedBusWrapper').toggle(selectedBuses.length > 0);
        }

        function loadBusSchedules() {

            let operator = $('#operator').val();
            let bus_ids = $('#bus_ids').val();
            let year = $('#year').val();
            let month = $('#month').val();

            if (!operator || !bus_ids || !year || !month) {
                $('#scheduleContainer').html(`
                <div class="text-center text-muted">
                    Please select operator, bus, year and month
                </div>
                `);
                return;
            }

            $('#scheduleContainer').html(`
            <div class="text-center p-4">
                <div class="spinner-border text-primary"></div>
                <p>Loading schedules...</p>
            </div>
            `);

            $.ajax({
                type: "POST",
                url: "/admin/get-bus-schedule-by-month",
                data: {
                    operator_id: operator,
                    bus_ids: bus_ids,
                    year: year,
                    month: month,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(res) {

                    if (!res.status || !res.data) {
                        $('#scheduleContainer').html(`<div class="text-danger">No data found</div>`);
                        return;
                    }

                    renderSchedule(res.data);
                }
            });
        }

        function renderSchedule(data) {

            let html = '';

            let operatorName = $("#operator option:selected").text();

            html += `<div class="mb-3">
                    <h5>${operatorName}</h5>
                </div>`;

            Object.keys(data).forEach(bus_id => {

                let bus = data[bus_id];

                html += `
                        <div class="mb-4">
                            <div class="mb-2">
                                <strong>${bus.bus_name} | ${bus.bus_number}</strong>
                            </div>
                            <div class="row">
                                `;

                bus.dates.forEach(date => {

                    let today = new Date();
                    today.setHours(0, 0, 0, 0);

                    let currentDate = new Date(date);
                    currentDate.setHours(0, 0, 0, 0);

                    let isPast = currentDate < today;
                    let isCancelled = cancelledDatesMap[bus_id]?.includes(date);

                    html += `
                            <div class="col-md-4 mb-2">
                                <label class="w-100 border rounded p-2 text-center
                                    ${isPast ? 'bg-light text-muted' : ''}">

                                    <input type="checkbox"
                                        name="dates[]"
                                        value="${date}"
                                        class="schedule-checkbox"
                                        data-bus="${bus_id}"
                                        data-date="${date}"
                                        ${isCancelled ? 'checked' : ''}
                                        ${isPast ? 'disabled' : ''}>

                                    ${formatDate(date)}
                                </label>
                            </div>
                        `;
                });

                html += `
                    </div>
                </div>`;
            });

            $('#scheduleContainer').html(html);
        }

        function formatDate(dateStr) {
            let d = new Date(dateStr);
            return d.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        let refreshTimer;

        $('#year, #month').on('change', function() {

            if (pageInitializing) return;

            clearTimeout(refreshTimer);

            refreshTimer = setTimeout(function() {
                refreshAllData();
            }, 300);

        });

        $('#reason').on('change', function() {

            let val = $(this).val();

            if (val == 7) {
                $('#otherReasonWrapper').show();
            } else {
                $('#otherReasonWrapper').hide();
                $('#other_reason').val('');
            }
        });

        $('#backoffice-form').on('submit', function(e) {

            e.preventDefault();

            let operator = $('#operator').val();
            let buses = $('#bus_ids').val();
            let year = $('#year').val();
            let month = $('#month').val();
            let reason = $('#reason').val();
            let otherReason = $('#other_reason').val() || '';

            if (!operator) return commonAjax.viewAlert("Please select operator", "warning");
            if (!buses) return commonAjax.viewAlert("Please select at least one bus", "warning");
            if (!year) return commonAjax.viewAlert("Please select year", "warning");
            if (!month) return commonAjax.viewAlert("Please select month", "warning");
            if (!reason) return commonAjax.viewAlert("Please select reason", "warning");

            if (reason == 7 && !otherReason.trim()) {
                return commonAjax.viewAlert("Please enter other reason", "warning");
            }

            commonAjax.confirmAlert('Are you sure to proceed!');

            let form = this;

            $('#btnConfirmOk').one('click', function() {

                $('#removed_dates').val(JSON.stringify(removedCancelledDates));

                form.submit();
            });

        });

        function loadCancelledData() {

            let operator = $('#operator').val();
            let bus_ids = $('#bus_ids').val();
            let year = $('#year').val();
            let month = $('#month').val();

            if (!operator || !bus_ids || !year || !month) {
                $('#cancelledTableBody').html('');
                return;
            }

            $.ajax({
                type: "POST",
                url: "/admin/get-cancelled-bus-data",
                data: {
                    operator_id: operator,
                    bus_ids: bus_ids,
                    year: year,
                    month: month,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(res) {

                    console.log("Cancelled Data:", res);

                    if (!res.status || Object.keys(res.data).length === 0) {
                        $('#cancelledTableBody').html('');
                        $('#cancelledTableWrapper').hide();
                        return;
                    }

                    cancelledDatesMap = {};

                    Object.keys(res.data).forEach(bus_id => {
                        cancelledDatesMap[bus_id] = res.data[bus_id].dates;
                    });

                    renderCancelledTable(res.data);
                    $('#cancelledTableWrapper').show();

                }
            });
        }


        function refreshAllData() {
            loadCancelledData();
            loadBusSchedules();
        }

        function renderCancelledTable(data) {

            let html = '';
            let i = 1;

            Object.keys(data).forEach(bus_id => {

                let bus = data[bus_id];

                html += `
                    <tr>
                        <td>${i++}</td>
                        <td>${bus.bus_name} | ${bus.bus_number}</td>
                        <td>${bus.dates.map(d => formatDate(d)).join('<br>')}</td>
                        <td>${bus.reason}</td>
                        <td>
                            ${formatDateTime(bus.created_at)}
                        </td>
                    </tr>
                    `;
            });

            $('#cancelledTableBody').html(html);
        }

        function formatDateTime(dateStr) {
            let d = new Date(dateStr);
            return d.toLocaleString('en-GB');
        }

        function restoreOldBuses() {

            if (!oldData.buses) return;

            selectedBuses = [];

            oldData.buses.split(',').forEach(function(id) {

                let option = $('#bus option[value="' + id + '"]');

                if (option.length) {
                    selectedBuses.push({
                        id: id,
                        text: option.text()
                    });
                }
            });

            renderBuses();

            $('#year').val(oldData.year);
            $('#month').val(oldData.month);

            refreshAllData();
        }
        if (oldData.operator) {

            $('#operator').val(oldData.operator);

            // restore will happen after bus loads
        }

        $(document).on('change', '.schedule-checkbox', function() {

            let bus_id = $(this).data('bus');
            let date = $(this).data('date');
            let isChecked = $(this).is(':checked');

            let today = new Date();
            today.setHours(0, 0, 0, 0);

            let selectedDate = new Date(date);
            selectedDate.setHours(0, 0, 0, 0);

            if (selectedDate < today) return;

            let isAlreadyCancelled = cancelledDatesMap[bus_id]?.includes(date);

            if (isAlreadyCancelled && !isChecked) {
                removedCancelledDates.push({
                    bus_id: bus_id,
                    date: date
                });
            }

            // remove if rechecked
            if (isAlreadyCancelled && isChecked) {
                removedCancelledDates = removedCancelledDates.filter(
                    d => !(d.bus_id == bus_id && d.date == date)
                );
            }

        });
    </script>
    @endpush