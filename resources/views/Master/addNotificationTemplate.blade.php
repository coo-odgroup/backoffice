        @extends('admin.layouts.master')
        @section('page_title', 'Notification Template')
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
                <a href="{{ route('notification-template.index') }}" class="btn btn-success btn-sm">
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
                                                           <input type="text" class="form-control form-control-sm clearable" id="name" name="name" value="{{ $data['row']->name ?? '' }}" placeholder="Enter Notification Name" maxlength="100">
                                                        </div>

                                                        <div class="mb-2">
                                                            <label for="bus">Slug<span
                                                                    class="text-danger">*</span></label>
                                                           <input type="text" class="form-control form-control-sm clearable" id="slug" name="slug" value="{{ $data['row']->slug ?? '' }}" placeholder="Enter Slug" maxlength="100">
                                                        </div>
                                                          <div class="mb-2">
                                                        <div class="row">
                                                            <div class="col-xl-6">
                                                                <label for="year">Type <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm"
                                                                    id="year" name="year">
                                                                    <option value="">Select Type</option>
                                                                   
                                                                </select>
                                                            </div>

                                                            <div class="col-xl-6">
                                                                <label for="month">Category <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm"
                                                                    id="month" name="month">
                                                                    <option value="">Select Category</option>
                                                                  
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        <div class="mb-2">
                                                            <label for="operator">Event Trigger<span
                                                                    class="text-danger">*</span></label>
                                                           <select class="form-select form-select-sm"
                                                                    id="month" name="month">
                                                                    <option value="">Select Event Trigger</option>
                                                           </select>
                                                        </div>

                                                         <div class="mb-2">
                                                                <label for="operator">Allowd Placeholders</label>
                                                                <textarea class="form-control form-control-sm clearable" rows="5" id="description" name="description" placeholder="Enter Allowd Placeholders"></textarea>
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

                                                             <div class="mb-2">
                                                                <label for="operator">Subject</label>
                                                                <input type="text" class="form-control form-control-sm clearable" id="name" name="name" value="{{ $data['row']->name ?? '' }}" placeholder="Enter Subject" maxlength="100">
                                                             </div>

                                                             <div class="mb-2">
                                                                <label for="operator">Email Content</label>
                                                                <textarea class="form-control form-control-sm clearable" rows="5" id="description" name="description" placeholder="Email Conetent"></textarea>
                                                             </div>
                                                             <div class="mb-2">
                                                                <label for="operator">Title</label>
                                                                <input type="text" class="form-control form-control-sm clearable" id="name" name="name" value="{{ $data['row']->name ?? '' }}" placeholder="Enter Notification Name" maxlength="100">
                                                             </div>

                                                             <div class="mb-2">
                                                                <label for="operator">Body</label>
                                                                <textarea class="form-control form-control-sm clearable" rows="5" id="description" name="description" placeholder="Enter Body"></textarea>
                                                             </div>

                                                              <div class="mb-2">
                                                                <label for="operator">SMS Content</label>
                                                                <textarea class="form-control form-control-sm clearable" rows="5" id="description" name="description" placeholder="Enter SMS Content"></textarea>
                                                             </div>
                                                              <div class="mb-2">
                                                                <label for="operator">WhatsApp Content</label>
                                                                <textarea class="form-control form-control-sm clearable" rows="5" id="description" name="description" placeholder="Enter WhatsApp Content"></textarea>
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

                commonAjax.loadBusOperatorDropdown();

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

            // Operator change load buses
            $('#operator').on('change', function() {

                let operator_id = $(this).val();
                if (!operator_id || isRestoring) return;

                if ($('#pageLoader').length === 0) {
                    $('body').append(`
            <div id="pageLoader" style="
                position:fixed;
                top:0;
                left:0;
                width:100%;
                height:100%;
                background:rgba(255,255,255,0.75);
                z-index:99999;
                display:flex;
                align-items:center;
                justify-content:center;
                flex-direction:column;
            ">
                <div class="spinner-border text-primary"></div>
                <div class="mt-2 fw-semibold">Loading buses...</div>
            </div>
        `);
                }

                commonAjax.loadBusListByOperator('#bus', operator_id);
                let checkBusLoaded = setInterval(function() {

                    if ($('#bus option').length > 1) {
                        clearInterval(checkBusLoaded);
                        $('#pageLoader').remove();
                    }
                }, 300);

                // fail-safe remove after 5 sec
                setTimeout(function() {
                    clearInterval(checkBusLoaded);
                    $('#pageLoader').remove();
                }, 5000);

            });


            // Bus change → load schedule
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
                        $('#operator').prop('disabled', true).trigger('change.select2');
                        $('#bus').prop('disabled', true).trigger('change.select2');
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
        