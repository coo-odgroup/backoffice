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
                                                            <label for="operator">Notification Name <span
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
                                                                    <label for="type">Type <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm"
                                                                        id="type" name="type">
                                                                        <option value="">Select Type</option>

                                                                    </select>
                                                                </div>

                                                                <div class="col-xl-6">
                                                                    <label for="category">Category <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm"
                                                                        id="category" name="category">
                                                                        <option value="">Select Category</option>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="trigger">Event Trigger<span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm"
                                                                id="trigger" name="trigger">
                                                                <option value="">Select Event Trigger</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-2">
                                                            <label for="operator">Allowd Placeholders<span
                                                                    class="text-danger">*</span></label>
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
                                                            <div id="defaultMessage" class="text-center text-muted py-5">
                                                                <strong>Select Notification Type</strong>
                                                            </div>

                                                            <!-- EMAIL -->
                                                            <div id="emailFields">
                                                                <div class="mb-2">
                                                                    <label for="operator">Subject<span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" id="email_subject" class="form-control form-control-sm clearable" name="email_subject" value="{{ $data['row']->subject ?? '' }}" placeholder="Enter Subject" maxlength="100">
                                                                </div>

                                                                <div class="mb-2">
                                                                    <label for="operator">Email Content<span
                                                                            class="text-danger">*</span></label>
                                                                    <textarea id="email_content" class="form-control  form-control-sm clearable" rows="5" name="emailContent" placeholder="Email Conetent">{{ $data['row']->body ?? '' }}</textarea>
                                                                </div>
                                                            </div>
                                                            <!-- PUSH -->
                                                            <div id="pushFields">
                                                                <div class="mb-2">
                                                                    <label for="operator">Title<span
                                                                            class="text-danger">*</span></label>
                                                                    <input id="push_title" type="text" class="form-control form-control-sm clearable" name="title" value="{{ $data['row']->title ?? '' }}" placeholder="Enter Notification Name" maxlength="100">
                                                                </div>

                                                                <div class="mb-2">
                                                                    <label for="operator">Body<span
                                                                            class="text-danger">*</span></label>
                                                                    <textarea id="push_body" class="form-control form-control-sm clearable" rows="5" name="body" placeholder="Enter Body">{{ $data['row']->body ?? '' }}</textarea>
                                                                </div>
                                                            </div>

                                                            <!-- SMS -->
                                                            <div id="smsFields">
                                                                <div class="mb-2">
                                                                    <label for="operator">SMS Content<span
                                                                            class="text-danger">*</span></label>
                                                                    <textarea id="sms_content" class="form-control form-control-sm clearable" rows="5" name="smsContent" placeholder="Enter SMS Content">{{ $data['row']->body ?? '' }}</textarea>
                                                                </div>
                                                            </div>

                                                            <!-- WHATSAPP -->
                                                            <div class="mb-2">
                                                                <div id="whatsappFields">
                                                                    <label for="operator">WhatsApp Content<span
                                                                            class="text-danger">*</span></label>
                                                                    <textarea id="whatsapp_content" class="form-control form-control-sm clearable" rows="5" name="whatsappContent" placeholder="Enter WhatsApp Content">{{ $data['row']->body ?? '' }}</textarea>
                                                                </div>
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
            function hideAllSections() {
                $('#emailFields, #pushFields, #smsFields, #whatsappFields').hide();
                $('#defaultMessage').show();
            }

          $(document).ready(function() {

    commonAjax.initSelect2('#type', 'Select Type');
    commonAjax.initSelect2('#category', 'Select Category');
    commonAjax.initSelect2('#trigger', 'Select Event Trigger');

    hideAllSections();

    let typeVal = "{{ $data['row']->type ?? '' }}";
    let categoryVal = "{{ $data['row']->category ?? '' }}";
    let triggerVal = "{{ $data['row']->event_trigger ?? '' }}";

    // TYPE
    $('#type').addClass('annexture');
    commonAjax.loadAnnextureList('NOTIFICATION_TYPE', typeVal);

    setTimeout(() => {
        $('#type').removeClass('annexture');

        // CATEGORY
        $('#category').addClass('annexture');
        commonAjax.loadAnnextureList('NOTIFICATION_TEMPLATE_CATEGORY', categoryVal);

        setTimeout(() => {
            $('#category').removeClass('annexture');

            // TRIGGER
            $('#trigger').addClass('annexture');
            commonAjax.loadAnnextureList('NOTIFICATION_EVENT_TRIGGER', triggerVal);

            setTimeout(() => {
                $('#trigger').removeClass('annexture');

                if (typeVal) {
                    $('#type').val(typeVal).trigger('change');
                }

            }, 300);

        }, 300);

    }, 300);

});


            $('#btnReset').click(function() {

                $('#backoffice-form')[0].reset();

                $('#type, #category, #trigger').val('').trigger('change');

                hideAllSections();
            });


            $('#type').on('change', function() {

                let type = parseInt($(this).val());

                hideAllSections();

                if (!type) {
                    $('#rightPanel').hide();
                    $('#defaultMessage').show();
                    return;
                }

                $('#rightPanel').show();
                $('#defaultMessage').hide();

                if (type === 1) {
                    $('#emailFields').show();
                } else if (type === 2) {
                    $('#smsFields').show();
                } else if (type === 3) {
                    $('#pushFields').show();
                } else if (type === 4) {
                    $('#whatsappFields').show();
                }

            });


            $('#backoffice-form').on('submit', function(e) {

                e.preventDefault();

                let errorMsg = "";
                let isValid = true;

                // BASIC VALIDATION
                if (!$('#name').val().trim()) {
                    isValid = false;
                    errorMsg = "Notification Name is required";
                } else if (!$('#slug').val().trim()) {
                    isValid = false;
                    errorMsg = "Slug is required";
                } else if (!$('#type').val()) {
                    isValid = false;
                    errorMsg = "Please select Notification Type";
                } else if (!$('#category').val()) {
                    isValid = false;
                    errorMsg = "Please select Category";
                } else if (!$('#trigger').val()) {
                    isValid = false;
                    errorMsg = "Please select Event Trigger";
                }

                // TYPE BASED VALIDATION
                let type = parseInt($('#type').val());

                if (isValid) {

                    if (type === 1) { // EMAIL

                        if (!$('#email_subject').val().trim()) {
                            isValid = false;
                            errorMsg = "Email Subject is required";
                        } else if (!$('#email_content').val().trim()) {
                            isValid = false;
                            errorMsg = "Email Content is required";
                        }

                    } else if (type === 3) { // PUSH

                        if (!$('#push_title').val().trim()) {
                            isValid = false;
                            errorMsg = "Push Title is required";
                        } else if (!$('#push_body').val().trim()) {
                            isValid = false;
                            errorMsg = "Push Body is required";
                        }

                    } else if (type === 2) { // SMS

                        if (!$('#sms_content').val().trim()) {
                            isValid = false;
                            errorMsg = "SMS Content is required";
                        }

                    } else if (type === 4) { // WHATSAPP

                        if (!$('#whatsapp_content').val().trim()) {
                            isValid = false;
                            errorMsg = "WhatsApp Content is required";
                        }
                    }
                }

                if (!isValid) {
                    commonAjax.viewAlert(errorMsg, "warning");
                    return false;
                }
                commonAjax.confirmAlert('Are you sure to proceed !');

                $('#btnConfirmOk').off('click').on('click', function() {
                    $('#backoffice-form')[0].submit();
                });

            });


            $('#name').on('input', function() {

                let value = $(this).val();

                let slug = value
                    .toLowerCase() // small case
                    .trim() // remove start/end space
                    .replace(/\s+/g, '-') // space → hyphen
                    .replace(/[^a-z0-9\-]/g, '') // remove special chars
                    .replace(/\-+/g, '-'); // multiple hyphen → single

                $('#slug').val(slug);
            });
        </script>
        @endpush
        