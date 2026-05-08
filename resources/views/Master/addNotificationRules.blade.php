        @extends('admin.layouts.master')
        @section('page_title', 'Notification Rules')
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
                <a href="{{ route('notification-rules.index') }}" class="btn btn-success btn-sm">
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
                                                <div class="col-md-7">
                                                    <div class="p-3 border rounded bg-white">
                                                        <div class="row">

                                                            <div class="col-md-6 mb-2">
                                                                <label for="cron_name">
                                                                    Cron Name <span class="text-danger">*</span>
                                                                </label>

                                                                <select class="form-select form-select-sm"
                                                                    id="cron_name"
                                                                    name="cron_name">

                                                                    <option value="">Select</option>

                                                                </select>
                                                            </div>

                                                            <div class="col-md-6 mb-2">
                                                                <label for="channel">
                                                                    Channel <span class="text-danger">*</span>
                                                                </label>

                                                                <select class="form-select form-select-sm"
                                                                    id="channel"
                                                                    name="channel">

                                                                    <option value="">Select</option>

                                                                </select>
                                                            </div>


                                                            <div class="col-md-6 mb-2">
                                                                <label for="template">Template <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm"
                                                                    id="template" name="template">
                                                                    <option value="">Select</option>

                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                                <label for="status_condition">Status Condition<span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm"
                                                                    id="status_condition" name="status_condition">
                                                                    <option value="">Select</option>

                                                                </select>
                                                            </div>
                                                            <div class=" mb-2 mt-3">
                                                                <label for="recipient">Recipient Type<span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm"
                                                                    id="recipient" name="recipient">
                                                                    <option value="">Select</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="p-3  border rounded bg-white mt-3" id="recipientConfigSection">
                                                            <div class=" mb-2" id="rolesSection">
                                                                <label for="roles">Role Type <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm"
                                                                    id="roles" name="roles">
                                                                    <option value="">Select</option>

                                                                </select>
                                                            </div>
                                                            <div class="mb-2" id="recipientSection">
                                                                <label for="manual_recipient">Recipient (Manual)</label>
                                                                <input type="text" class="form-control form-control-sm clearable" id="manual_recipient" name="manual_recipient" value="{{ $data['row']->manual_recipient ?? '' }}" placeholder="Enter Recipient" maxlength="100">
                                                            </div>
                                                            <div class="mb-2" id="dynamicVariableSection">
                                                                <label for="dynamic_variable">Dynamic Variable</label>
                                                                <input type="text" class="form-control form-control-sm clearable" id="dynamic_variable" name="dynamic_variable" value="{{ $data['row']->dynamic_variable ?? '' }}" placeholder="Enter Dynamic Variable" maxlength="100">
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>


                                                <!-- RIGHT COLUMN -->
                                                <div class="col-md-5">
                                                    <div class="p-3 border rounded bg-white">
                                                        <div class="p-3 border rounded schedule-card">

                                                            <div class="cron-info-card">

                                                                <div class="info-row">
                                                                    <span class="info-label">
                                                                        Cron Name:
                                                                    </span>

                                                                    <span class="info-value" id="cronNameText">
                                                                        --
                                                                    </span>
                                                                </div>

                                                                <div class="info-row">
                                                                    <span class="info-label">
                                                                        Cron Type:
                                                                    </span>

                                                                    <span class="badge bg-primary" id="cronTypeText">
                                                                        --
                                                                    </span>
                                                                </div>

                                                                <div class="info-row align-items-start">
                                                                    <span class="info-label">
                                                                        Scheduler Type:
                                                                    </span>

                                                                    <div class="scheduler-box">

                                                                        <div>
                                                                            <strong>Interval Minutes:</strong>
                                                                            <span id="intervalMinutesText">--</span>
                                                                        </div>

                                                                        <hr class="my-1">

                                                                        <div>
                                                                            <strong>Run Times:</strong>
                                                                        </div>

                                                                        <ul class="mb-0 ps-3" id="runTimesText">
                                                                            <li>--</li>
                                                                        </ul>

                                                                    </div>
                                                                </div>

                                                                <div class="info-row align-items-start">
                                                                    <span class="info-label">
                                                                        Execution Type
                                                                    </span>

                                                                    <div class="execution-box">

                                                                        <div>
                                                                            <strong id="executionLabel">
                                                                                --
                                                                            </strong>
                                                                        </div>

                                                                        <hr class="my-1">

                                                                        <div class="small text-break"
                                                                            id="executionValue">
                                                                            --
                                                                        </div>

                                                                    </div>
                                                                </div>

                                                                <div class="info-row">
                                                                    <span class="info-label">
                                                                        Status
                                                                    </span>

                                                                    <span class="badge bg-success"
                                                                        id="cronStatusText">
                                                                        --
                                                                    </span>
                                                                </div>

                                                            </div>
                                                        </div>

                                                        <div class="p-3 border rounded schedule-card mt-4">

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

        <style>
            .cron-info-card {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .info-row {

                display: grid;

                grid-template-columns: 180px 1fr;

                align-items: start;

                gap: 20px;

                border-bottom: 1px dashed #dcdcdc;

                padding-bottom: 12px;
            }

            .info-label {
                font-weight: 600;
                color: #444;
                min-width: 130px;
            }

            .info-value {
                font-weight: 500;
                color: #111;
            }

            .scheduler-box,
            .execution-box {

                background: #f8f9fa;

                border: 1px solid #dfe3e7;

                border-radius: 8px;

                padding: 10px 14px;

                min-width: 220px;

                font-size: 13px;

                color: #212529;
            }

            .scheduler-box hr,
            .execution-box hr {
                border-color: rgba(255, 255, 255, 0.2);
            }
        </style>

        @endsection

        @push('scripts')
        <script type="module">
            $(document).ready(function() {

                commonAjax.initSelect2('#cron_name', 'Select Cron Name');
                commonAjax.initSelect2('#template', 'Select Notification Template');
                commonAjax.initSelect2('#channel', 'Select Channel');
                commonAjax.initSelect2('#status_condition', 'Select Status Condition');
                commonAjax.initSelect2('#recipient', 'Select Recipient Type');
                commonAjax.initSelect2('#roles', 'Select Role');

                let cronNameVal = "{{ $data['row']->cron_name ?? '' }}";
                let notificationTemplateVal = "{{ $data['row']->notification_template ?? '' }}";
                let channelVal = "{{ $data['row']->channel ?? '' }}";
                let statusConditionVal = "{{ $data['row']->status_condition ?? '' }}";
                let rolesVal = "{{ $data['row']->roles ?? '' }}";

                let recipientVal =
                    "{{ $data['row']->recipient ?? '' }}";

                commonAjax.loadCronJobDropdown('#cron_name', cronNameVal);
                commonAjax.getNotificationTemplateDropdown('#template', notificationTemplateVal);
                commonAjax.getRolesDropdown('#roles', rolesVal);
                commonAjax.loadAnnextureList([

                    'NOTIFICATION_TYPE',
                    'STATUS_CONDITION',
                    'RECEIPTENT_TYPE'

                ], function(data) {

                    renderDropdown('#channel', data.NOTIFICATION_TYPE || [], channelVal);
                    renderDropdown('#status_condition', data.STATUS_CONDITION || [], statusConditionVal);
                    renderDropdown('#recipient', data.RECEIPTENT_TYPE || [], recipientVal);

                    if (channelVal) {
                        $('#channel')
                            .val(channelVal)
                            .trigger('change');
                    }

                    if (statusConditionVal) {
                        $('#status_condition')
                            .val(statusConditionVal)
                            .trigger('change');
                    }

                    if (recipientVal) {
                        $('#recipient')
                            .val(recipientVal)
                            .trigger('change');
                    }
                    toggleRecipientSections();
                });



                $('#recipient').on('change', function() {

                    toggleRecipientSections();

                });

                $('#cron_name').on('change', function() {

                    let id = $(this).val();

                    if (!id) {
                        return;
                    }

                    loadCronJobDetails(id);

                });


            });

            function renderDropdown(
                selector,
                data,
                selectedValue = ''
            ) {

                let options =
                    '<option value="">Select</option>';

                $.each(data, function(index, item) {

                    let selected =
                        selectedValue == item.annexture_name ?
                        'selected' :
                        '';

                    options += `
                        <option
                            value="${item.annexture_name}"
                            ${selected}
                        >
                            ${item.annexture_name}
                        </option>
                    `;
                });

                $(selector)
                    .html(options)
                    .trigger('change');

            }


            function loadCronJobDetails(id) {

                $.ajax({

                    type: "POST",

                    url: ajaxUrl + "get-cron-job-details",

                    data: {

                        id: id,

                        _token: $('meta[name="csrf-token"]').attr("content"),
                    },

                    dataType: "json",

                    success: function(response) {

                        if (
                            !response.status ||
                            !response.data
                        ) {
                            return;
                        }

                        let row = response.data;

                        // NAME
                        $('#cronNameText')
                            .text(row.name ?? '--');

                        // TYPE
                        $('#cronTypeText')
                            .text(row.type ?? '--');

                        // STATUS
                        let status =
                            row.active_status == 1 ?
                            'Active' :
                            'Inactive';

                        $('#cronStatusText')
                            .text(status);

                        // INTERVAL
                        $('#intervalMinutesText')
                            .text(
                                row.interval_minutes ?? '--'
                            );

                        // RUN TIMES
                        let runTimesHtml = '<li>--</li>';

                        if (row.run_times_json) {

                            try {

                                let times =
                                    JSON.parse(row.run_times_json);

                                runTimesHtml = '';

                                $.each(times, function(i, val) {

                                    runTimesHtml += `
                            <li>${val}</li>
                        `;
                                });

                            } catch (e) {}
                        }

                        $('#runTimesText')
                            .html(runTimesHtml);

                        // EXECUTION
                        let executionLabel =
                            row.execution_type == 'job' ?
                            'Job Class' :
                            'Command Name';

                        let executionValue =
                            row.execution_type == 'job' ?
                            row.job_class :
                            row.command_name;

                        $('#executionLabel')
                            .text(executionLabel);

                        $('#executionValue')
                            .text(executionValue ?? '--');
                    },

                    error: function() {

                        console.log(
                            'Error loading cron job details'
                        );
                    }
                });
            }

            function toggleRecipientSections() {

                let recipientType =
                    ($('#recipient').val() || '')
                    .toLowerCase()
                    .trim()
                    .replace(/\s+/g, '');

                // HIDE ALL
                $('#recipientConfigSection').hide();
                $('#rolesSection').hide();
                $('#recipientSection').hide();
                $('#dynamicVariableSection').hide();

                // ROLE BASED
                if (
                    recipientType.includes('role')
                ) {

                    $('#recipientConfigSection').show();
                    $('#rolesSection').show();
                }

                // MANUAL SELECTION
                else if (
                    recipientType.includes('manual')
                ) {

                    $('#recipientConfigSection').show();
                    $('#recipientSection').show();
                }

                // DYNAMIC VARIABLE
                else if (
                    recipientType.includes('dynamic')
                ) {

                    $('#recipientConfigSection').show();
                    $('#dynamicVariableSection').show();
                }
            }
        </script>

        @endpush