@extends('admin.layouts.master')
@section('page_title', 'Support Ticket')
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
        <li class="breadcrumb-item active">@yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('supportTicket.index') }}" class="btn btn-success btn-sm">
            View Support Ticket
        </a>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" enctype="multipart/form-data" novalidate class="w-100 add-cities-form">
    {{csrf_field()}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- FILTER -->
                    <div class="mb-3">
                        <div class="card-body">
                            <div class="row">
                                @if (session('message'))

                                <div class="alert alert-{{ session('level') ?? 'success' }} alert-dismissible fade show" role="alert">
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                                @endif
                                @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>

                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @endif

                                <!-- POST FIELDS -->
                                <div class="col-12">
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <!-- ================= Ticket Information ================= -->
                                            <div class="card shadow-sm mb-3">
                                                <div class="card-header  text-black py-2">
                                                    <h6 class="mb-0"><i class="fa fa-ticket me-2"></i>Ticket Information</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">

                                                        <div class="col-md-3">
                                                            <label for="module_type">Module Type <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm module_type" id="module_type" name="module_type">
                                                                <option value="">Select Module</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label for="ticket_code">Ticket Code <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control form-control-sm ticket_code"
                                                                id="ticket_code"
                                                                name="ticket_code"
                                                                value="{{ old('ticket_code', $data['row']->ticket_code ?? '') }}"
                                                                readonly>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label for="title">Title <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control form-control-sm title"
                                                                id="title"
                                                                name="title"
                                                                value="{{ old('title', $data['row']->title ?? '') }}">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="description">
                                                                Description <span class="text-danger">*</span>
                                                            </label>
                                                            <textarea
                                                                class="form-control form-control-sm"
                                                                id="description"
                                                                name="description"
                                                                rows="6">{!! old('description', $data['row']->description ?? '') !!}</textarea>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- ================= Classification ================= -->
                                            <div class="card shadow-sm mb-3">
                                                <div class="card-header text-dark py-2">
                                                    <h6 class="mb-0">
                                                        <i class="fa fa-layer-group me-2"></i>Classification
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md">
                                                            <label for="project">Project</label>
                                                            <select class="form-select form-select-sm project" id="project" name="project">
                                                                <option value="">Select Project</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md">
                                                            <label for="category">Category</label>
                                                            <select class="form-select form-select-sm category" id="category" name="category">
                                                                <option value="">Select Category</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md">
                                                            <label for="severity">Severity</label>
                                                            <select class="form-select form-select-sm severity" id="severity" name="severity">
                                                                <option value="">Select Severity</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md">
                                                            <label for="priority">Priority</label>
                                                            <select class="form-select form-select-sm priority" id="priority" name="priority">
                                                                <option value="">Select Priority</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md">
                                                            <label for="status">Status</label>
                                                            <select class="form-select form-select-sm status" id="status" name="status">
                                                                <option value="">Select Status</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md">
                                                            <label for="environment">Environment</label>
                                                            <select class="form-select form-select-sm environment" id="environment" name="environment">
                                                                <option value="">Select Environment</option>
                                                                <option value="Staging"
                                                                    {{ old('environment', $data['row']->environment ?? '')=='Staging' ? 'selected' : '' }}>
                                                                    Staging
                                                                </option>

                                                                <option value="Production"
                                                                    {{ old('environment', $data['row']->environment ?? '')=='Production' ? 'selected' : '' }}>
                                                                    Production
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ================= Assignment ================= -->
                                            <div class="card shadow-sm mb-3">
                                                <div class="card-header  text-black py-2">
                                                    <h6 class="mb-0">
                                                        <i class="fa fa-users me-2"></i>Assignment Details
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label for="reported_by">Reported By</label>
                                                            <select class="form-select form-select-sm users reported_by"
                                                                id="reported_by"
                                                                name="reported_by">
                                                                <option value="">Select User</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label for="assigned_to">Assigned To</label>
                                                            <select class="form-select form-select-sm users assigned_to"
                                                                id="assigned_to"
                                                                name="assigned_to">
                                                                <option value="">Select User</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label for="assigned_by">Assigned By</label>
                                                            <select class="form-select form-select-sm users assigned_by"
                                                                id="assigned_by"
                                                                name="assigned_by">
                                                                <option value="">Select User</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- ================= Time Tracking ================= -->
                                            <div class="card shadow-sm mb-3">
                                                <div class="card-header text-black py-2">
                                                    <h6 class="mb-0">
                                                        <i class="fa fa-clock me-2"></i>Time Tracking
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label for="due_date">Due Date</label>
                                                            <input type="date"
                                                                class="form-control form-control-sm due_date"
                                                                id="due_date"
                                                                name="due_date"
                                                                value="{{ old('due_date', $data['row']->due_date ?? '') }}">
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label for="estimated_hours">Estimated Hours</label>
                                                            <input type="number"
                                                                step="0.5"
                                                                class="form-control form-control-sm estimated_hours"
                                                                id="estimated_hours"
                                                                name="estimated_hours"
                                                                value="{{ old('estimated_hours', $data['row']->estimated_hours ?? '') }}">
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label for="actual_hours">Actual Hours</label>
                                                            <input type="number"
                                                                step="0.5"
                                                                class="form-control form-control-sm actual_hours"
                                                                id="actual_hours"
                                                                name="actual_hours"
                                                                value="{{ old('actual_hours', $data['row']->actual_hours ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- ================= Environment =|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|= -->
                                            <div class="card shadow-sm mb-3">
                                                <div class="card-header  text-black py-2">
                                                    <h6 class="mb-0">
                                                        <i class="fa fa-desktop me-2"></i>System Details
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">

                                                        <div class="col-md-4">
                                                            <label for="browser">Browser</label>
                                                            <select class="form-select form-select-sm browser" id="browser" name="browser">
                                                                <option value="">Select Browser</option>
                                                                <option value="Google Chrome"
                                                                    {{ old('browser',$data['row']->browser ?? '')=='Google Chrome' ? 'selected':'' }}>
                                                                    Google Chrome
                                                                </option>
                                                                <option value="Mozilla Firefox"
                                                                    {{ old('browser',$data['row']->browser ?? '')=='Mozilla Firefox' ? 'selected':'' }}>
                                                                    Mozilla Firefox
                                                                </option>
                                                                <option value="Microsoft Edge"
                                                                    {{ old('browser',$data['row']->browser ?? '')=='Microsoft Edge' ? 'selected':'' }}>
                                                                    Microsoft Edge
                                                                </option>
                                                                <option value="Safari"
                                                                    {{ old('browser',$data['row']->browser ?? '')=='Safari' ? 'selected':'' }}>
                                                                    Safari
                                                                </option>
                                                                <option value="Other"
                                                                    {{ old('browser',$data['row']->browser ?? '')=='Other' ? 'selected':'' }}>
                                                                    Other
                                                                </option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label for="device">Device</label>
                                                            <select class="form-select form-select-sm device" id="device" name="device">
                                                                <option value="Desktop"
                                                                    {{ old('device',$data['row']->device ?? '')=='Desktop' ? 'selected':'' }}>
                                                                    Desktop
                                                                </option>

                                                                <option value="Mobile"
                                                                    {{ old('device',$data['row']->device ?? '')=='Mobile' ? 'selected':'' }}>
                                                                    Mobile
                                                                </option>

                                                                <option value="Tablet"
                                                                    {{ old('device',$data['row']->device ?? '')=='Tablet' ? 'selected':'' }}>
                                                                    Tablet
                                                                </option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label for="app_version">App Version</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm app_version"
                                                                id="app_version"
                                                                name="app_version"
                                                                value="{{ old('app_version', $data['row']->app_version ?? '') }}">
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 d-flex">
                                            <!-- ================= Attachments ================= -->
                                            <div class="card shadow-sm mb-3 w-100 h-100">
                                                <div class="card-header bg-secondary text-white py-2">
                                                    <h6 class="mb-0">
                                                        <i class="fa fa-paperclip me-2"></i>
                                                        Attachments
                                                    </h6>
                                                </div>

                                                <div class="card-body d-flex flex-column">
                                                    <div id="attachmentContainer">
                                                        <div class="attachment-row border rounded p-3 mb-3">
                                                            <div class="row">
                                                                <!-- Attachment Title -->
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">
                                                                        Attachment Title
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="text"
                                                                        name="attachment_title[]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Enter Attachment Title">
                                                                </div>
                                                                <!-- File Type -->
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">
                                                                        File Type
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select class="form-select form-select-sm" name="attachment_type[]">
                                                                        <option value="">Select File Type</option>

                                                                        <!-- Image Formats -->
                                                                        <optgroup label="Image Files">
                                                                            <option value="jpg">JPG (.jpg)</option>
                                                                            <option value="jpeg">JPEG (.jpeg)</option>
                                                                            <option value="png">PNG (.png)</option>
                                                                            <option value="gif">GIF (.gif)</option>
                                                                            <option value="bmp">BMP (.bmp)</option>
                                                                            <option value="webp">WEBP (.webp)</option>
                                                                            <option value="svg">SVG (.svg)</option>
                                                                        </optgroup>

                                                                        <!-- Video Formats -->
                                                                        <optgroup label="Video Files">
                                                                            <option value="mp4">MP4 (.mp4)</option>
                                                                            <option value="avi">AVI (.avi)</option>
                                                                            <option value="mov">MOV (.mov)</option>
                                                                            <option value="mkv">MKV (.mkv)</option>
                                                                            <option value="wmv">WMV (.wmv)</option>
                                                                            <option value="flv">FLV (.flv)</option>
                                                                            <option value="webm">WEBM (.webm)</option>
                                                                        </optgroup>

                                                                        <!-- Document Formats -->
                                                                        <optgroup label="Document Files">
                                                                            <option value="pdf">PDF (.pdf)</option>
                                                                            <option value="doc">DOC (.doc)</option>
                                                                            <option value="docx">DOCX (.docx)</option>
                                                                            <option value="txt">TXT (.txt)</option>
                                                                            <option value="rtf">RTF (.rtf)</option>
                                                                        </optgroup>

                                                                        <!-- Spreadsheet Formats -->
                                                                        <optgroup label="Spreadsheet Files">
                                                                            <option value="xls">XLS (.xls)</option>
                                                                            <option value="xlsx">XLSX (.xlsx)</option>
                                                                            <option value="csv">CSV (.csv)</option>
                                                                        </optgroup>

                                                                        <!-- Presentation Formats -->
                                                                        <optgroup label="Presentation Files">
                                                                            <option value="ppt">PPT (.ppt)</option>
                                                                            <option value="pptx">PPTX (.pptx)</option>
                                                                        </optgroup>

                                                                        <!-- Compressed Files -->
                                                                        <optgroup label="Compressed Files">
                                                                            <option value="zip">ZIP (.zip)</option>
                                                                            <option value="rar">RAR (.rar)</option>
                                                                            <option value="7z">7Z (.7z)</option>
                                                                        </optgroup>


                                                                        <option value="other">Other</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <!-- File -->
                                                            <div class="mb-3">
                                                                <label class="form-label">
                                                                    Select File
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="file"
                                                                    class="form-control form-control-sm"
                                                                    name="attachment_file[]">
                                                            </div>
                                                            <!-- Buttons -->
                                                            <div class="d-flex justify-content-end gap-2">
                                                                <button type="button"
                                                                    class="btn btn-success btn-sm">
                                                                    <i class="fa fa-upload me-1"></i>
                                                                    Upload
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-primary btn-sm btnAddAttachment">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm btnRemoveAttachment">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Uploaded Files -->
                                                    <!-- <div class="mt-auto">

                                                        <hr>
                                                        <h6 class="text-secondary mb-2">
                                                            <i class="fa fa-folder-open me-1"></i>
                                                            Uploaded Files
                                                        </h6>
                                                        <div class="list-group list-group-flush">
                                                        </div>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>

                                        <!-- BUTTONS -->
                                        <div class="row">
                                            <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                                <button class="btn btn-primary btn-sm" type="submit">
                                                    {{ $data['strSubmit'] }}
                                                </button>
                                                @if($data['strReset'] == 'Cancel')
                                                <a href="{{ route('supportTicket.index') }}" class="btn btn-secondary btn-sm">
                                                    {{ $data['strReset'] }}
                                                </a>
                                                @else
                                                <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                                    {{ $data['strReset'] }}
                                                </button>
                                                @endif
                                            </div>
                                        </div>
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
    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {

        // Initialize Select2
        commonAjax.initSelect2('#category', 'Select Category');
        commonAjax.initSelect2('#module_type', 'Select Module');
        commonAjax.initSelect2('#priority', 'Select Priority');
        commonAjax.initSelect2('#project', 'Select Project');
        commonAjax.initSelect2('#severity', 'Select Severity');
        commonAjax.initSelect2('#status', 'Select Status');
        commonAjax.initSelect2('#reported_by', 'Select User');
        commonAjax.initSelect2('#assigned_to', 'Select User');
        commonAjax.initSelect2('#assigned_by', 'Select User');


        let selectedCategory = "{{ old('category', $data['row']->category ?? '') }}";
        let selectedModule = "{{ old('module_type', $data['row']->module ?? '') }}";
        let selectedPriority = "{{ old('priority', $data['row']->priority ?? '') }}";
        let selectedProject = "{{ old('project', $data['row']->project_id ?? '') }}";
        let selectedSeverity = "{{ old('severity', $data['row']->severity ?? '') }}";
        let selectedStatus = "{{ old('status', $data['row']->status ?? '') }}";

        let selectedReportedBy = "{{ old('reported_by', $data['row']->reported_by ?? '') }}";
        let selectedAssignedTo = "{{ old('assigned_to', $data['row']->assigned_to ?? '') }}";
        let selectedAssignedBy = "{{ old('assigned_by', $data['row']->assigned_by ?? '') }}";

        commonAjax.loadUsersTypeList();
        setTimeout(function() {
            $('#reported_by').val(selectedReportedBy).trigger('change');
            $('#assigned_to').val(selectedAssignedTo).trigger('change');
            $('#assigned_by').val(selectedAssignedBy).trigger('change');
        }, 500);

        commonAjax.loadAnnextureList([
            'SUPPORT_TICKET_CATEGORY',
            'SUPPORT_TICKET_MODULE',
            'SUPPORT_TICKET_PRIORITY',
            'SUPPORT_TICKET_PROJECT',
            'SUPPORT_TICKET_SEVERITY',
            'SUPPORT_TICKET_STATUS'
        ], function(data) {

            renderDropdown('#category', data.SUPPORT_TICKET_CATEGORY || [], selectedCategory);
            renderDropdown('#module_type', data.SUPPORT_TICKET_MODULE || [], selectedModule);
            renderDropdown('#priority', data.SUPPORT_TICKET_PRIORITY || [], selectedPriority);
            renderDropdown('#project', data.SUPPORT_TICKET_PROJECT || [], selectedProject);
            renderDropdown('#severity', data.SUPPORT_TICKET_SEVERITY || [], selectedSeverity);
            renderDropdown('#status', data.SUPPORT_TICKET_STATUS || [], selectedStatus);

        });

        commonAjax.initClearableInputs();

    });

    $(document).ready(function() {

        toggleAttachmentButtons();

        // Add Attachment
        $(document).on('click', '.btnAddAttachment', function() {

            let clone = $('.attachment-row:first').clone();

            // Clear values
            clone.find('input[type="text"]').val('');
            clone.find('input[type="file"]').val('');
            clone.find('select').prop('selectedIndex', 0);

            $('#attachmentContainer').append(clone);

            toggleAttachmentButtons();
        });

        // Remove Attachment
        $(document).on('click', '.btnRemoveAttachment', function() {
            $(this).closest('.attachment-row').remove();
            toggleAttachmentButtons();
        });

        function toggleAttachmentButtons() {

            $('.attachment-row').each(function(index) {
                if (index === 0) {
                    $(this).find('.btnAddAttachment').show();
                    $(this).find('.btnRemoveAttachment').hide();
                } else {
                    $(this).find('.btnAddAttachment').hide();
                    $(this).find('.btnRemoveAttachment').show();
                }
            });
        }
    });

    function renderDropdown(selector, data, selectedValue = '', placeholder = 'Select') {

        let options = `<option value="">${placeholder}</option>`;

        if (Array.isArray(data) && data.length > 0) {
            data.forEach(function(item) {

                let value = item.annexture_value ?? item.value ?? item.id ?? '';
                let text = item.annexture_name ?? item.label ?? item.name ?? '';

                options += `
                    <option value="${value}" ${value == selectedValue ? 'selected' : ''}>
                        ${text}
                    </option>`;
            });
        }

        $(selector).html(options).trigger('change');
    }

    document.addEventListener('DOMContentLoaded', function() {
        initCkEditor('#description');
    });

    $(document).on('change', '#module_type', function() {

        if ($('#ticket_code').val() !== '') {
            return;
        }

        const module = $(this).find('option:selected').text().trim();

        const prefixes = {
            "Website": "WEBS",
            "Admin Console": "ADMN",
            "Operator Panel": "OPER",
            "Agent Panel": "AGNT",
            "API Client Dashboard": "APCL",
            "Outgoing API": "OAPI",
            "Admin API": "AAPI",
            "Website API": "WAPI",
            "Android": "ANDR",
            "IOS": "IOSA"
        };

        const prefix = prefixes[module] || "TICK";

        const unique6Digit = (
            Date.now().toString().slice(-4) +
            Math.floor(Math.random() * 90 + 10)
        ).slice(-6);

        $('#ticket_code').val(prefix + unique6Digit);
    });
</script>
@endpush