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
                                                                        id="cron_name" name="cron_name">

                                                                        <option value="">Select</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label for="channel">
                                                                        Channel <span class="text-danger">*</span>
                                                                    </label>

                                                                    <select class="form-select form-select-sm"
                                                                        id="channel" name="channel">
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
                                                            <div class="p-3  border rounded bg-white mt-3"
                                                                id="recipientConfigSection">
                                                                <div class=" mb-2" id="rolesSection">
                                                                    <label for="roles">Role Type <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm"
                                                                        id="roles" name="roles">
                                                                        <option value="">Select</option>

                                                                    </select>
                                                                    <div class="mt-3 d-none" id="roleUsersSection">
                                                                        <label class="fw-semibold mb-2">
                                                                            Select Users
                                                                        </label>
                                                                        <div class="border rounded p-2 bg-light">
                                                                            <div class="form-check mb-2">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    name="selected_users[]"
                                                                                    value="admin@odbus.com"
                                                                                    id="user1">
                                                                                <label class="form-check-label"
                                                                                    for="user1">
                                                                                    admin@odbus.com
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check mb-2">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    value="support@odbus.com"
                                                                                    id="user2">
                                                                                <label class="form-check-label"
                                                                                    for="user2">
                                                                                    support@odbus.com
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check mb-2">

                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    value="booking@odbus.com"
                                                                                    id="user3">
                                                                                <label class="form-check-label"
                                                                                    for="user3">
                                                                                    booking@odbus.com
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check mb-2">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    value="accounts@odbus.com"
                                                                                    id="user4">
                                                                                <label class="form-check-label"
                                                                                    for="user4">
                                                                                    accounts@odbus.com
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    value="operations@odbus.com"
                                                                                    id="user5">
                                                                                <label class="form-check-label"
                                                                                    for="user5">
                                                                                    operations@odbus.com
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-2" id="recipientSection">
                                                                    <label for="manual_recipient">Recipient
                                                                        (Manual)</label>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm clearable"
                                                                        id="manual_recipient" name="manual_recipient"
                                                                        value="{{ $data['row']->manual_recipient ?? '' }}"
                                                                        placeholder="Enter Recipient" maxlength="100">
                                                                </div>
                                                                <div class="mb-2" id="dynamicVariableSection">
                                                                    <label for="dynamic_variable">Dynamic
                                                                        Variable</label>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm clearable"
                                                                        id="dynamic_variable" name="dynamic_variable"
                                                                        value="{{ $data['row']->dynamic_variable ?? '' }}"
                                                                        placeholder="Enter Dynamic Variable"
                                                                        maxlength="100">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- PAGE HERO -->
                                                        <div class="notification-page-header mb-4">

                                                        </div>

                                                        <!-- STATIC PREVIEW CARD -->
                                                        <div class="cron-summary-card mb-4 d-none"
                                                            id="cronSummaryWrapper">
                                                            <div class="row g-2 align-items-center">
                                                                <div class="col-md-2">
                                                                    <div class="summary-item">
                                                                        <div class="summary-label">
                                                                            Cron Name
                                                                        </div>

                                                                        <div class="summary-value"
                                                                            id="summaryCronName">
                                                                            --
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <div class="summary-item">
                                                                        <div class="summary-label">
                                                                            Cron Type
                                                                        </div>

                                                                        <div class="summary-value"
                                                                            id="summaryCronType">
                                                                            --
                                                                        </div>
                                                                    </div>
                                                                </div>



                                                                <div class="col-md-2">
                                                                    <div class="summary-item">
                                                                        <div class="summary-label">
                                                                            Execution Type
                                                                        </div>

                                                                        <div class="summary-value"
                                                                            id="summaryExecutionType">
                                                                            --
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="summary-item">
                                                                        <div class="summary-label">
                                                                            Schedule Type
                                                                        </div>

                                                                        <div class="summary-value"
                                                                            id="summaryScheduleType">
                                                                            --
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="summary-item">
                                                                        <div class="summary-label">
                                                                            Scheduled Time
                                                                        </div>

                                                                        <div class="summary-value"
                                                                            id="summaryScheduler">
                                                                            --
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <div class="summary-item">
                                                                        <div class="summary-label">
                                                                            Status
                                                                        </div>

                                                                        <div class="summary-value"
                                                                            id="summaryStatus">
                                                                            --
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>

                                                        <!-- STATIC RULE TABLE -->
                                                        <div class="notification-rule-table-card mb-4 d-none"
                                                            id="notificationRuleTableWrapper">
                                                            <div class="table-responsive">
                                                                <table class="table rule-preview-table align-middle mb-0">
                                                                    <thead class="table-secondary">
                                                                        <tr>
                                                                            <th>Sl No</th>
                                                                            <th>Channel</th>
                                                                            <th>Template</th>
                                                                            <th>Status Condition</th>
                                                                            <th>Recipient Type</th>
                                                                            <th>Recipient</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="notificationRulePreviewBody">
                                                                        <tr>
                                                                            <td colspan="8"
                                                                                class="text-center text-muted py-4">
                                                                                Select Cron Name To Preview Rules
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- RIGHT COLUMN -->
                                                    <div class="col-md-5">
                                                        <div id="cronDetailsContainer"></div>
                                                        <div class="mt-3" id="notificationPreviewWrapper">
                                                            <div class="p-3 border rounded schedule-card">
                                                                <div
                                                                    class="d-flex align-items-center  justify-content-between mb-3">
                                                                    <h6 class="mb-0">
                                                                        Notification Template Preview
                                                                    </h6>
                                                                </div>
                                                                <div id="notificationDetailsContainer">
                                                                    <div class="text-center text-muted py-5">
                                                                        Select Template To Preview
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

                        <!-- CRON DETAILS MODAL -->
                        <div class="modal fade" id="cronDetailsModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            Cron Job Details
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                                        </button>
                                    </div>
                                    <div class="modal-body" id="cronDetailsBody">
                                    </div>
                                </div>
                            </div>
                        </div>
            </form>

            <style>
                #notificationPreviewWrapper {
                    display: none;
                }

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

                /* TOP SUMMARY BLOCK */
                /* TOP SUMMARY BLOCK */
                .cron-summary-card {

                    background: #fff;

                    border: 1px solid #dfe3e8;

                    border-radius: 10px;

                    padding: 16px 18px;

                    box-shadow: 0 1px 4px rgba(16, 24, 40, .04);
                }

                .summary-item {

                    padding: 4px 10px;
                }

                .summary-label {

                    font-size: 13px;

                    font-weight: 600;

                    color: #475467;

                    margin-bottom: 2px;
                }

                .summary-value {

                    font-size: 14px;

                    color: #101828;

                    font-weight: 500;
                }

                /* LOWER TABLE BLOCK */
                .notification-rule-table-card {

                    background: #fff;

                    border: 1px solid #dfe3e8;

                    border-radius: 10px;

                    padding: 14px;

                    box-shadow: 0 1px 4px rgba(16, 24, 40, .04);
                }

                /* TABLE */
                .rule-preview-table {

                    border: 1px solid #e4e7ec;

                    border-radius: 8px;

                    overflow: hidden;
                }

                .rule-preview-table thead th {

                    background: #f8fafc;

                    border-bottom: 1px solid #dfe3e8;

                    border-right: 1px solid #e4e7ec;

                    font-size: 13px;

                    font-weight: 600;

                    color: #344054;

                    padding: 12px;
                }

                .rule-preview-table thead th:last-child {

                    border-right: none;
                }

                .rule-preview-table tbody td {

                    padding: 12px;

                    border-bottom: 1px solid #e9edf2;

                    border-right: 1px solid #eef1f4;

                    font-size: 13px;

                    vertical-align: middle;

                    background: #fff;
                }

                .rule-preview-table tbody td:last-child {

                    border-right: none;
                }

                .rule-preview-table tbody tr:last-child td {

                    border-bottom: none;
                }

                .rule-preview-table tbody tr:hover {

                    background: #f8fbff;
                }
            </style>

            @endsection

            @push('scripts')
            <script type="module">
                $('#roleUsersSection').addClass('d-none');

                $(document).ready(function() {

                    commonAjax.initSelect2('#cron_name', 'Select Cron Name');
                    commonAjax.initSelect2('#template', 'Select Notification Template');
                    commonAjax.initSelect2('#channel', 'Select Channel');
                    commonAjax.initSelect2('#status_condition', 'Select Status Condition');
                    commonAjax.initSelect2('#recipient', 'Select Recipient Type');

                    commonAjax.initSelect2('#roles', 'Select Role');

                    let cronNameVal = "{{ $data['row']->cron_job_id ?? '' }}";
                    let notificationTemplateVal = "{{ $data['row']->template_id ?? '' }}";
                    let channelVal = '';

                    switch ("{{ $data['row']->channel ?? '' }}") {

                        case '1':
                            channelVal = 'Email';
                            break;

                        case '2':
                            channelVal = 'SMS';
                            break;

                        case '3':
                            channelVal = 'Push Notification';
                            break;

                        case '4':
                            channelVal = 'Whatsapp';
                            break;
                    }
                    let statusConditionVal = "{{ $data['row']->status_condition ?? '' }}";
                    let rolesVal = "{{ $data['row']->role_type ?? '' }}";
                    let recipientVal = '';
                    let recipientValue =
                        "{{ $data['row']->recipient_value ?? '' }}";

                    switch ("{{ $data['row']->reciptent_type ?? '' }}") {

                        case '1':
                            recipientVal = 'Manual';
                            break;

                        case '2':
                            recipientVal = 'Role Based';
                            break;

                        case '3':
                            recipientVal = 'Dynamic Variable';
                            break;
                    }

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


                        setTimeout(function() {

                            // CHANNEL
                            if (channelVal) {

                                $('#channel')
                                    .val(channelVal)
                                    .trigger('change');
                            }

                            // STATUS CONDITION
                            if (statusConditionVal) {

                                $('#status_condition')
                                    .val(statusConditionVal)
                                    .trigger('change');
                            }

                            // RECIPIENT
                            if (recipientVal) {

                                $('#recipient')
                                    .val(recipientVal)
                                    .trigger('change');

                                toggleRecipientSections();
                            }

                        }, 1000);

                        if (
                            recipientVal.toLowerCase() == 'manual'
                        ) {

                            $('#manual_recipient')
                                .val(recipientValue);
                        }

                        if (
                            recipientVal.toLowerCase() ==
                            'dynamic variable'
                        ) {

                            $('#dynamic_variable')
                                .val(recipientValue);
                        }
                    });

                     let selectedUsers = {!!json_encode(json_decode($data['row'] -> recipient_value ?? '[]')) !!};

                    if (selectedUsers && selectedUsers.length > 0) {
                        selectedUsers.forEach(function(user) {
                            $(`input[value="${user}"]`).prop('checked', true);
                        });
                    }

                    $('#recipient').on('change', function() {

                        toggleRecipientSections();

                    });

                    $(document).on('change', '#cron_name', function() {

                        let id = $(this).val();

                        console.log('SELECTED ID => ', id);

                        if (!id) {

                            $('#cronDetailsContainer').html('');

                            $('#cronSummaryWrapper')
                                .addClass('d-none');

                            $('#notificationRuleTableWrapper')
                                .addClass('d-none');

                            return;
                        }

                        $('#cronSummaryWrapper')
                            .removeClass('d-none');

                        $('#notificationRuleTableWrapper')
                            .removeClass('d-none');

                        loadCronJobDetails(id);
                    });


                    $(document).on('change', '#template', function() {

                        let id = $(this).val();

                        if (!id) {

                            $('#notificationPreviewWrapper').hide();

                            $('#notificationDetailsContainer').html('');

                            return;
                        }

                        $('#notificationPreviewWrapper').show();

                        loadNotificationDetails(id);
                    });
                    // ✅ RESTORE EDIT VALUES (Seat Block style)

                    // CRON
                    waitForOptions('#cron_name', function() {

                        if (cronNameVal) {
                            $('#cron_name').val(cronNameVal).trigger('change');

                            // load cron details also
                            loadCronJobDetails(cronNameVal);
                        }

                    });



                    // TEMPLATE (IMPORTANT — depends on channel)
                    waitForOptions('#template', function() {

                        if (notificationTemplateVal) {
                            $('#template').val(notificationTemplateVal).trigger('change');

                            // show preview also
                            loadNotificationDetails(notificationTemplateVal);
                        }

                    });

                    // ROLES
                    waitForOptions('#roles', function() {

                        if (rolesVal) {
                            $('#roles').val(rolesVal).trigger('change');
                        }

                    });
                    toggleRecipientSections();
                });


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

                function loadNotificationDetails(id) {

                    $('#notificationDetailsContainer').html(`

                            <div class="text-center p-4">
                                <div class="spinner-border text-primary"></div>
                                <p class="mt-2">
                                    Loading Notification...
                                </p>
                            </div>

                        `);

                    $.ajax({

                        type: "POST",

                        url: "/admin/get-notification-details",

                        data: {

                            id: id,

                            _token: $('meta[name="csrf-token"]').attr('content')
                        },

                        success: function(response) {

                            $('#notificationDetailsContainer')
                                .html(response);
                        },

                        error: function(xhr) {

                            console.log(xhr);

                            $('#notificationDetailsContainer').html(`

                                <div class="alert alert-danger">
                                    Failed to Load Notification
                                </div>

                            `);
                        }
                    });
                }



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

                    $(selector).html(options);

                }


                $('#channel').on('change', function() {

                    let channel = $(this).val();

                    let existingTemplate =
                        "{{ $data['row']->template_id ?? '' }}";

                    let isEditMode =
                        existingTemplate != '';

                    // RESET ONLY IN ADD MODE
                    if (!isEditMode) {

                        $('#template')
                            .val('')
                            .trigger('change');

                        $('#notificationPreviewWrapper')
                            .hide();

                        $('#notificationDetailsContainer')
                            .html(`
                <div class="text-center text-muted py-5">
                    Select Template To Preview
                </div>
            `);
                    }

                    let type = '';

                    if (
                        channel.toLowerCase() == 'email'
                    ) {

                        type = 1;

                    } else if (
                        channel.toLowerCase() == 'sms'
                    ) {

                        type = 2;

                    } else if (
                        channel.toLowerCase() == 'push notification'
                    ) {

                        type = 3;

                    } else if (
                        channel.toLowerCase() == 'whatsapp'
                    ) {

                        type = 4;
                    }

                    commonAjax.getNotificationTemplateDropdown(
                        '#template',
                        existingTemplate,
                        type
                    );

                    // RESELECT TEMPLATE AFTER LOAD
                    setTimeout(function() {

                        if (existingTemplate) {

                            $('#template')
                                .val(existingTemplate)
                                .trigger('change');
                        }

                    }, 500);
                });


                function loadCronJobDetails(id) {

                    $.ajax({

                        type: "POST",

                        url: "/admin/get-cron-job-details",

                        data: {

                            id: id,

                            _token: $('meta[name="csrf-token"]').attr('content')
                        },

                        dataType: "json",

                        success: function(response) {

                            console.log(response);

                            if (
                                response.status == false ||
                                response.data == null
                            ) {

                                $('#cronDetailsContainer').html(`
                                    <div class="alert alert-danger">
                                        No Cron Details Found
                                    </div>
                                `);

                                return;
                            }

                            let row = response.data;

                            // STATUS
                            let statusBadge =
                                row.active_status == 1 ?
                                `<span class="badge bg-success">
                                    Active
                                </span>` :
                                `<span class="badge bg-danger">
                                    Inactive
                                </span>`;

                            // RUN TIMES
                            let runTimesHtml = '<li>--</li>';

                            if (row.run_times_json) {

                                try {

                                    let times =
                                        JSON.parse(row.run_times_json);

                                    if (
                                        Array.isArray(times) &&
                                        times.length > 0
                                    ) {

                                        runTimesHtml = '';

                                        $.each(times, function(index, time) {

                                            runTimesHtml += `
                                    <li>${time}</li>
                                `;
                                        });
                                    }

                                } catch (e) {

                                    console.log(e);
                                }
                            }

                            // EXECUTION
                            let executionLabel =
                                row.execution_type == 'job' ?
                                'Job Class' :
                                'Command Name';

                            let executionValue =
                                row.execution_type == 'job' ?
                                row.job_class :
                                row.command_name;

                            // FINAL HTML
                            let html = `

                                            <div class="p-3 border rounded bg-white">

                                                <div class="p-3  schedule-card">

                                                    <div class="cron-info-card">

                                                        <div class="info-row">

                                                            <span class="info-label">
                                                                Cron Name:
                                                            </span>

                                                            <span class="info-value">
                                                                ${row.name ?? '--'}
                                                            </span>

                                                        </div>

                                                        <div class="info-row">

                                                            <span class="info-label">
                                                                Cron Type:
                                                            </span>

                                                            <span class="badge bg-primary">
                                                                ${row.type ?? '--'}
                                                            </span>

                                                        </div>

                                                        <div class="info-row">

                                                            <span class="info-label">
                                                                Schedule Type:
                                                            </span>

                                                            <span class="info-value">
                                                                ${row.schedule_type ?? '--'}
                                                            </span>

                                                        </div>

                                                        <div class="info-row align-items-start">

                                                            <span class="info-label">
                                                                Scheduler:
                                                            </span>

                                                            <div class="scheduler-box">

                                                                <div>

                                                                    <strong>
                                                                        Interval Minutes:
                                                                    </strong>

                                                                    ${row.interval_minutes ?? '--'}

                                                                </div>

                                                                <hr class="my-1">

                                                                <div>

                                                                    <strong>
                                                                        Run Times:
                                                                    </strong>

                                                                    <ul class="mb-0 ps-3">
                                                                        ${runTimesHtml}
                                                                    </ul>

                                                                </div>

                                                            </div>

                                                        </div>

                                                        <div class="info-row align-items-start">

                                                            <span class="info-label">
                                                                Execution:
                                                            </span>

                                                            <div class="execution-box">

                                                                <div>

                                                                    <strong>
                                                                        ${executionLabel}
                                                                    </strong>

                                                                </div>

                                                                <hr class="my-1">

                                                                <div class="small text-break">

                                                                    ${executionValue ?? '--'}

                                                                </div>

                                                            </div>

                                                        </div>

                                                        <div class="info-row">

                                                            <span class="info-label">
                                                                Status:
                                                            </span>

                                                            <span>
                                                                ${statusBadge}
                                                            </span>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>
                                        `;

                            $('#cronDetailsContainer').html(html);
                        },

                        error: function(xhr) {

                            console.log(xhr);

                            $('#cronDetailsContainer').html(`

                    <div class="alert alert-danger">
                        Failed to Load Cron Details
                    </div>

                `);
                        }
                    });
                }

                function toggleRecipientSections() {

                    let recipientType =
                        ($('#recipient').val() || '')
                        .toLowerCase()
                        .trim();

                    // HIDE WHOLE SECTION FIRST
                    $('#recipientConfigSection').hide();

                    // HIDE INNER SECTIONS
                    $('#rolesSection').hide();

                    $('#recipientSection').hide();

                    $('#dynamicVariableSection').hide();

                    $('#roleUsersSection').addClass('d-none');

                    // NOTHING SELECTED
                    if (!recipientType) {

                        return;
                    }

                    // SHOW MAIN CONTAINER
                    $('#recipientConfigSection').show();

                    // ROLE BASED
                    if (
                        recipientType == 'role based'
                    ) {

                        $('#rolesSection').show();

                        $('#roleUsersSection')
                            .removeClass('d-none');
                    }

                    // MANUAL
                    else if (
                        recipientType == 'manual'
                    ) {

                        $('#recipientSection').show();
                    }

                    // DYNAMIC VARIABLE
                    else if (
                        recipientType == 'dynamic variable'
                    ) {

                        $('#dynamicVariableSection').show();
                    }
                }
                $(document).on('change', '#cron_name', function() {

                    let cronId = $(this).val();
                    if (!cronId) {
                        $('#notificationRulePreviewBody')
                            .html(`
                                <tr>
                                    <td colspan="8"
                                        class="text-center text-muted py-4">
                                        Select Cron Name To Preview Rules
                                    </td>
                                </tr>
                            `);
                        return;
                    }
                    $.ajax({

                        type: "POST",

                        url: "/admin/get-cron-summary",

                        data: {

                            cron_id: cronId,

                            _token: $('meta[name="csrf-token"]')
                                .attr("content")
                        },

                        success: function(response) {

                            if (!response.status) {

                                return;
                            }

                            let row = response.data;

                            $('#summaryCronName')
                                .html(row.name ?? '--');

                            $('#summaryCronType')
                                .html(row.cron_type ?? '--');

                            $('#summaryExecutionType')
                                .html(row.execution_type ?? '--');

                            $('#summaryScheduleType')
                                .html(row.schedule_type ?? '--');

                            $('#summaryScheduler')
                                .html(row.scheduler ?? '--');

                            $('#summaryStatus')
                                .html(
                                    row.active_status == 1 ?
                                    `<span class="badge bg-success">
            Active
        </span>` :
                                    `<span class="badge bg-danger">
            Inactive
        </span>`
                                );
                        }
                    });


                    $.ajax({
                        type: "POST",
                        url: "/admin/get-cron-notification-rules",
                        data: {
                            cron_id: cronId,

                            _token: $('meta[name="csrf-token"]')
                                .attr("content")
                        },

                        success: function(response) {
                            $('#notificationRulePreviewBody')
                                .html(response.html);
                        }
                    });
                });

                function resetCronCard() {

                    $('#cronNameText').text('--');

                    $('#cronTypeText').text('--');

                    $('#intervalMinutesText').text('--');

                    $('#runTimesText').html('<li>--</li>');

                    $('#executionLabel').text('--');

                    $('#executionValue').text('--');

                    $('#cronStatusText')
                        .removeClass('bg-success bg-danger')
                        .text('--');
                }
            </script>
            @endpush