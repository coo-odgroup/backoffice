@extends('admin.layouts.master')
@section('page_title', 'User Roles')
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

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('user-roles.index') }}" class="btn btn-success btn-sm">
            View @yield('page_title')
        </a>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form">
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
                                    <div class="row mb-2">
                                        <div class="col">
                                            <div class="row">
                                                <div class="col-md-5 mb-2">
                                                    <label for="org">Organization Type<span class="text-danger important">*</span></label>
                                                    <select id="org" name="org" class="form-select form-select-sm selOrg"></select>
                                                </div>

                                                <div class="col-md-5 mb-2">
                                                    <label for="organization">Organization <span class="text-danger important">*</span></label>
                                                    <select id="organization" name="organization" class="form-select form-select-sm selOrg"></select>
                                                </div>

                                                <div class="col-md-5 mb-2">
                                                    <label for="org">Branch<span class="text-danger important">*</span></label>
                                                    <select id="branch_id" name="branch_id" class="form-select form-select-sm"></select>
                                                </div>
                                                <div class="col-md-5 mb-2">
                                                    <label for="org">Department<span class="text-danger important">*</span></label>
                                                    <select id="department_id" name="department_id" class="form-select form-select-sm"></select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col">
                                            <div class="row">
                                                <div class="col-md-5 mb-2">
                                                    <label for="role">Roles<span class="text-danger important">*</span></label>
                                                    <select id="role_id" name="role_id[]" class="form-select form-select-sm" multiple></select>
                                                </div>
                                                <div class="col-md-5 mb-2">
                                                    <label for="org">User<span class="text-danger important">*</span></label>
                                                    <select id="user_id" name="user_id" class="form-select form-select-sm"></select>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-5 mb-2">
                                                    <label for="effectiveFrom">Effective From </label>
                                                    <input type="date"
                                                        class="form-control form-control-sm"
                                                        id="effectiveFrom"
                                                        name="effectiveFrom"
                                                        value="{{ old('effectiveFrom', $data['row']->effective_from ?? '') }}">
                                                </div>
                                                <div class="col-md-5 mb-2">
                                                    <label for="effectiveFrom">Effective To </label>
                                                    <input type="date"
                                                        class="form-control form-control-sm"
                                                        id="effectiveTo"
                                                        name="effectiveTo"
                                                        value="{{ old('effectiveFrom', $data['row']->effective_to ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5 mb-2">
                                                    <label for="org">Assigned By<span class="text-danger important">*</span></label>
                                                    <select id="assigned_by" name="assigned_by" class="form-select form-select-sm"></select>

                                                </div>
                                                <div class="col-md-2 mb-2 d-flex align-items-end">
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            id="isPrimary"
                                                            name="isPrimary"
                                                            value="1"
                                                            style="border:1px solid #000; box-shadow:none;"
                                                            {{ old('isPrimary', $data['row']->is_primary ?? 0) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="isPrimary">
                                                            Is Primary
                                                        </label>
                                                    </div>
                                                </div>
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
                                            <a href="{{ route('states.index') }}" class="btn btn-secondary btn-sm">
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
    let selectedOrganization = "{{ old('organization', $data['row']->organization_id ?? '') }}";
    let selectedBranch = "{{ old('branch_id', $data['row']->branch_id ?? '') }}";
    let selectedDepartment = "{{ old('department_id', $data['row']->department_id ?? '') }}";
    let selectedRole = @json(old('role_id', $data['selectedRoles'] ?? []));
    let selectedUser = "{{ old('user_id', $data['row']->user_id ?? '') }}";
    let selectedAssignedBy = "{{ old('assigned_by', $data['row']->assigned_by ?? '') }}";
    let selectedOrgType = "{{ old('org', $data['row']->organization_type_id ?? '') }}";

    commonAjax.initSelect2('#org', 'Select Organization Type');
    commonAjax.initSelect2('#organization', 'Select Organization');
    commonAjax.loadOrganizationTypeList(selectedOrgType);

    if (selectedOrgType) {

        commonAjax.loadOrganizationListForUserRoles(
            selectedOrgType,
            selectedOrganization
        );

        setTimeout(function() {

            $.ajax({
                url: "{{ route('organization.details') }}",
                type: "POST",
                data: {
                    organization_type_id: selectedOrgType,
                    organization_id: selectedOrganization,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {

                    // Branch
                    $('#branch_id').html('<option value="">Select Branch</option>');
                    $.each(res.branches, function(i, item) {

                        $('#branch_id').append(
                            '<option value="' + item.id + '"' +
                            (item.id == selectedBranch ? ' selected' : '') +
                            '>' + item.branch_name + '</option>'
                        );

                    });

                    // Department
                    $('#department_id').html('<option value="">Select Department</option>');
                    $.each(res.departments, function(i, item) {

                        $('#department_id').append(
                            '<option value="' + item.id + '"' +
                            (item.id == selectedDepartment ? ' selected' : '') +
                            '>' + item.department_name + '</option>'
                        );

                    });

                    // Role
                    $('#role_id').html('<option value="">Select Role</option>');
                    $.each(res.roles, function(i, item) {

                        $('#role_id').append(
                            '<option value="' + item.id + '">' +
                            item.role_name +
                            '</option>'
                        );

                    });

                    if (selectedRole.length > 0) {
                        $('#role_id').val(selectedRole).trigger('change');
                    }

                    // Users
                    $('#assigned_by').html('<option value="">Select User</option>');
                    $('#user_id').html('<option value="">Select User</option>');

                    $.each(res.users, function(i, item) {

                        $('#assigned_by').append(
                            '<option value="' + item.id + '"' +
                            (item.id == selectedAssignedBy ? ' selected' : '') +
                            '>' + item.name + '</option>'
                        );

                        $('#user_id').append(
                            '<option value="' + item.id + '"' +
                            (item.id == selectedUser ? ' selected' : '') +
                            '>' + item.name + '</option>'
                        );

                    });

                    $('#branch_id').trigger('change');
                    $('#department_id').trigger('change');
                    $('#role_id').trigger('change');
                    $('#assigned_by').trigger('change');
                    $('#user_id').trigger('change');

                }
            });

        }, 300);
    }
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

        commonAjax.initSelect2('#org', 'Select Organization');
        commonAjax.initSelect2('#branch_id', 'Select Branch');
        commonAjax.initSelect2('#department_id', 'Select Department');
        commonAjax.initSelect2('#assigned_by', 'Select User');
        commonAjax.initSelect2('#user_id', 'Select User');
        commonAjax.initSelect2('#role_id', 'Select Role');

        commonAjax.initClearableInputs();

    });


    $('#org').on('change', function() {

        let organizationTypeId = $(this).val();

        $('#organization').html('<option value="">Select Organization</option>');

        if (organizationTypeId) {
            commonAjax.loadOrganizationListForUserRoles(organizationTypeId);
        }

        $.ajax({
            url: "{{ route('organization.details') }}",
            type: "POST",
            data: {
                organization_type_id: organizationTypeId,
                organization_id: '',
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {

                $('#branch_id').html('<option value="">Select Branch</option>');
                $.each(res.branches, function(i, item) {
                    $('#branch_id').append('<option value="' + item.id + '">' + item.branch_name + '</option>');
                });

                $('#department_id').html('<option value="">Select Department</option>');
                $.each(res.departments, function(i, item) {
                    $('#department_id').append('<option value="' + item.id + '">' + item.department_name + '</option>');
                });

                $('#role_id').html('<option value="">Select Role</option>');
                $.each(res.roles, function(i, item) {
                    $('#role_id').append('<option value="' + item.id + '">' + item.role_name + '</option>');
                });

                $('#assigned_by').html('<option value="">Select User</option>');
                $('#user_id').html('<option value="">Select User</option>');

                $.each(res.users, function(i, item) {
                    $('#assigned_by').append('<option value="' + item.id + '">' + item.name + '</option>');
                    $('#user_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                });

                $('#role_id').trigger('change');
                $('#assigned_by').trigger('change');
                $('#user_id').trigger('change');
            }
        });

    });

    $('#branchName').on('keyup blur', function() {

        let code = $(this).val()
            .toUpperCase()
            .trim()
            .replace(/\s+/g, '_')
            .replace(/[^A-Z0-9_]/g, '');

        $('#branchCode').val(code);

    });
    $('#branchCode').on('keyup', function() {
        $(this).val(
            $(this).val()
            .toUpperCase()
            .replace(/\s+/g, '_')
            .replace(/[^A-Z0-9_]/g, '')
        );
    });

    $('#displyOrder').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    $('#organization').on('change', function() {

        let organizationTypeId = $('#org').val();
        let organizationId = $('#organization').val();

        console.log("Org Type =", organizationTypeId);
        console.log("Organization =", organizationId);


    });

    $('#organization').on('change', function() {

        let organizationTypeId = $('#org').val();
        let organizationId = $(this).val();

        $.ajax({
            url: "{{ route('organization.details') }}",
            type: "POST",
            data: {
                organization_type_id: organizationTypeId,
                organization_id: organizationId,
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {

                // Branch
                $('#branch_id').html('<option value="">Select Branch</option>');
                $.each(res.branches, function(i, item) {
                    $('#branch_id').append(
                        '<option value="' + item.id + '">' + item.branch_name + '</option>'
                    );
                });

                // Department
                $('#department_id').html('<option value="">Select Department</option>');
                $.each(res.departments, function(i, item) {
                    $('#department_id').append(
                        '<option value="' + item.id + '">' + item.department_name + '</option>'
                    );
                });

                // Roles
                $('#role_id').html('<option value="">Select Role</option>');
                $.each(res.roles, function(i, item) {
                    $('#role_id').append(
                        '<option value="' + item.id + '">' + item.role_name + '</option>'
                    );
                });

                if (selectedRole.length > 0) {
                    $('#role_id').val(selectedRole).trigger('change');
                }

                // Users
                $('#assigned_by').html('<option value="">Select User</option>');
                $('#user_id').html('<option value="">Select User</option>');

                $.each(res.users, function(i, item) {

                    $('#assigned_by').append(
                        '<option value="' + item.id + '">' + item.name + '</option>'
                    );

                    $('#user_id').append(
                        '<option value="' + item.id + '">' + item.name + '</option>'
                    );

                });

            }
        });

    });
</script>
@endpush