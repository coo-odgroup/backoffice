        @extends('admin.layouts.master')
        @section('page_title', 'Cron Job')
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
                <a href="{{ route('cron-job.index') }}" class="btn btn-success btn-sm">
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
                                                <div class="col-md-6">
                                                    <div class="p-3 border rounded bg-white">
                                                        <div class="mb-2">
                                                            <label for="cronName">Cron Name<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm clearable" id="cronName" name="cronName" value="{{ $data['row']->name ?? '' }}" placeholder="Enter Cron Name" maxlength="100">
                                                        </div>

                                                        <div class="mb-2">
                                                            <label for="slug">Slug<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm clearable" id="slug" name="slug" value="{{ $data['row']->slug ?? '' }}" placeholder="Enter Slug" maxlength="100">
                                                        </div>
                                                        <div class="mb-2">
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <label for="type">Type <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm"
                                                                        id="type" name="type">
                                                                        <option value="">Select Type</option>
                                                                        <option value="Auto">Auto</option>
                                                                        <option value="Manual">Manual</option>

                                                                    </select>
                                                                </div>

                                                                <div class="col-xl-6">
                                                                    <label for="scheduler">Scheduler Type <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm"
                                                                        id="scheduler" name="scheduler">
                                                                        <option value="">Select</option>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="interval">Interval Minutes</label>
                                                            <input type="text" class="form-control form-control-sm clearable" id="interval" name="interval" value="{{ $data['row']->interval_minutes ?? '' }}" placeholder="Enter Interval Minutes" maxlength="2">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="runTime">Run Time Json</label>
                                                            <input type="text" class="form-control form-control-sm clearable" id="runTime" name="runTime" value="{{ $data['row']->run_times_json ?? '' }}" placeholder="Enter Run Time in Minutes">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="cron">Cron Expression</label>
                                                            <input type="text" class="form-control form-control-sm clearable" id="cron" name="cron" value="{{ $data['row']->cron_expression ?? '' }}" placeholder="Enter Cron Expression">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- RIGHT COLUMN -->
                                                <div class="col-md-6">
                                                    <div class="border rounded schedule-card">
                                                        <!-- <div class="card-header schedule-header">
                                                            <strong>Date Schedule List</strong>
                                                        </div> -->
                                                        <div class="card-body" id="scheduleContainer">

                                                            <div class="mb-2">
                                                                <label for="execution">Execution Type</label>
                                                                <select class="form-select form-select-sm"
                                                                    id="execution" name="execution">
                                                                    <option value="">Select Execution Type</option>
                                                                    <option value="job">Job</option>
                                                                    <option value="command">Command</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-2">
                                                                <label for="job">Job Class</label>
                                                                <input type="text" class="form-control form-control-sm clearable" id="job" name="job" value="{{ $data['row']->job_class ?? '' }}" placeholder="Enter Job Class" maxlength="100">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label for="command">Command Name</label>
                                                                <input type="text" class="form-control form-control-sm clearable" id="command" name="command" value="{{ $data['row']->command_name ?? '' }}" placeholder="Enter Command Name" maxlength="100">
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



        @endsection

        @push('scripts')
        <script type="module">
            $(document).ready(function() {

                commonAjax.initSelect2('#type', 'Select Type');
                commonAjax.initSelect2('#scheduler', 'Select Scheduler Type');
                commonAjax.initSelect2('#execution', 'Select Execution Type');

                commonAjax.loadAnnextureList([
                    'SCHEDULER_TYPE'
                ], function(data) {

                    renderDropdown(
                        '#scheduler',
                        data.SCHEDULER_TYPE || [],
                        "{{ trim($data['row']->schedule_type ?? old('scheduler')) }}"
                    );

                });

                $('#type')
                    .val("{{ $data['row']->type ?? old('type') }}")
                    .trigger('change');

                setTimeout(function() {

                    $('#scheduler')
                        .val("{{ $data['row']->schedule_type ?? old('scheduler') }}")
                        .trigger('change');

                    $('#execution')
                        .val("{{ $data['row']->execution_type ?? old('execution') }}")
                        .trigger('change');

                    toggleExecutionFields();

                }, 100);

                $('#execution').on('change', function() {
                    toggleExecutionFields();
                });

                $('#btnReset').click(function() {

                    $('#backoffice-form')[0].reset();

                    $('.form-select').val('').trigger('change');

                    $('.clearable').val('');

                    toggleExecutionFields();

                });
                $('#backoffice-form').on('submit', function(e) {

                    if (!validator.blankCheck('cronName', 'Cron Name cannot be left blank')) {
                        e.preventDefault();
                        return;
                    }

                    if (!validator.maxLength('cronName', 100, 'Cron Name')) {
                        e.preventDefault();
                        return;
                    }

                    if (!validator.blankCheck('slug', 'Slug cannot be left blank')) {
                        e.preventDefault();
                        return;
                    }

                    if (!validator.maxLength('slug', 100, 'Slug')) {
                        e.preventDefault();
                        return;
                    }

                    if (!validator.selectDropdown('type', 'Please select type')) {
                        e.preventDefault();
                        return;
                    }

                    if (!validator.selectDropdown('scheduler', 'Please select scheduler type')) {
                        e.preventDefault();
                        return;
                    }

                    if (!validator.selectDropdown('execution', 'Please select execution type')) {
                        e.preventDefault();
                        return;
                    }

                    if ($('#interval').val() != '') {

                        if (!validator.validNumber('interval', 'Interval Minutes')) {
                            e.preventDefault();
                            return;
                        }

                        let interval = parseInt($('#interval').val());

                        if (interval < 1 || interval > 999) {

                            commonAjax.viewAlert(
                                "Interval Minutes must be between 1 and 59",
                                "warning"
                            );

                            $('#interval').focus();

                            e.preventDefault();

                            return;
                        }
                    }

                    // JOB
                    if ($('#execution').val() == 'job') {

                        if (!validator.blankCheck('job', 'Job Class cannot be left blank')) {
                            e.preventDefault();
                            return;
                        }

                        if (!validator.maxLength('job', 255, 'Job Class')) {
                            e.preventDefault();
                            return;
                        }
                    }

                    // COMMAND
                    if ($('#execution').val() == 'command') {

                        if (!validator.blankCheck('command', 'Command Name cannot be left blank')) {
                            e.preventDefault();
                            return;
                        }

                        if (!validator.maxLength('command', 255, 'Command Name')) {
                            e.preventDefault();
                            return;
                        }
                    }

                });

                commonAjax.initClearableInputs();

            });

            $('#execution').on('change', function() {
                toggleExecutionFields();
            });


            $('#cronName').on('keyup', function() {

                let slug = $(this).val()
                    .toLowerCase()
                    .trim()
                    .replace(/\s+/g, '-')
                    .replace(/[^a-z0-9-]/g, '');

                $('#slug').val(slug);

            });

            function renderDropdown(selector, items = [], selected = '') {

                let options = '<option value="">Select Option</option>';

                $.each(items, function(index, item) {

                    let isSelected =
                        selected == item.annexture_value ?
                        'selected' :
                        '';

                    options += `
                <option value="${item.annexture_value}" ${isSelected}>
                    ${item.annexture_name}
                </option>
            `;
                });

                $(selector).html(options).trigger('change');
            }

            function toggleExecutionFields() {

                let execution = $('#execution').val();

                if (execution === 'job') {

                    $('#job').closest('.mb-2').show();
                    $('#command').closest('.mb-2').hide();

                } else if (execution === 'command') {

                    $('#job').closest('.mb-2').hide();
                    $('#command').closest('.mb-2').show();

                } else {

                    $('#job').closest('.mb-2').hide();
                    $('#command').closest('.mb-2').hide();
                }
            }
        </script>

        @endpush