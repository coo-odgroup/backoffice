@extends('admin.layouts.master')
@section('page_title', 'Add Roles Hierarchy')
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
        <a href="{{ route('roles-hierarchy.index') }}" class="btn btn-success btn-sm">
            View Roles Hierarchy
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
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label for="org">Organization<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm selOrg" id="org" name="org">
                                                <option value="">Select Organization</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="role">Role Type<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm role" id="role" name="role">
                                                <option value="">Select Role </option>
                                            </select>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-3">
                                                <label for="parent">Parent select<span class="text-danger important">*</span></label>
                                                <select class="form-select form-select-sm parent" id="parent" name="parent">
                                                    <option value="">Select Parent </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="hierarchylevel">
                                                    Hierarchy Level
                                                    <span class="text-danger important">*</span>
                                                </label>

                                                <input
                                                    type="number"
                                                    class="form-control form-control-sm clearable"
                                                    id="hierarchylevel"
                                                    name="hierarchylevel"
                                                    placeholder="Hierarchy Level"
                                                    min="1"
                                                    step="1"
                                                    value="{{ old('hierarchylevel', $data['row']->hierarchy_level ?? '') }}"
                                                    onkeydown="return event.key !== '.' && event.key !== 'e' && event.key !== 'E' && event.key !== '-'">

                                                <small class="text-muted char-counter float-end"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4 justify-center">
                                        <div class="col-md-3">
                                            <div class="form-check ">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="can_create_users"
                                                    name="can_create_users"
                                                    value="1"
                                                    style="border:2px solid #495057; box-shadow:none;"

                                                    {{ old('can_create_users', $data['row']->can_create_users ?? 0) ? 'checked' : '' }}>

                                                <label class="form-check-label" for="can_create_users">
                                                    Can Create Users
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check mt-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="can_manage_lower_roles"
                                                    name="can_manage_lower_roles"
                                                    value="1"
                                                    style="border:2px solid #495057; box-shadow:none;"

                                                    {{ old('can_manage_lower_roles', $data['row']->can_manage_lower_roles ?? 0) ? 'checked' : '' }}>

                                                <label class="form-check-label" for="can_manage_lower_roles">
                                                    Can Manage Lower Roles
                                                </label>
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
                                            <a href="{{ route('roles-hierarchy.index') }}" class="btn btn-secondary btn-sm">
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

        if (!validator.selectDropdown('org', 'Organization'))
            return false;

        if (!validator.selectDropdown('role', 'Role Type'))
            return false;

        if (!validator.blankCheck('hierarchylevel', 'Hierarchy Level cannot be left blank'))
            return false;

        let hierarchyLevel = $('#hierarchylevel').val().trim();

        if (hierarchyLevel === '') {
            validator.showError('hierarchylevel', 'Hierarchy Level cannot be left blank.');
            return false;
        }

        if (!/^[0-9]+$/.test(hierarchyLevel)) {
            validator.showError('hierarchylevel', 'Hierarchy Level must be a valid integer.');
            return false;
        }

        if (parseInt(hierarchyLevel) <= 0) {
            validator.showError('hierarchylevel', 'Hierarchy Level must be greater than 0.');
            return false;
        }

        if (parseInt($('#hierarchylevel').val()) <= 0) {

            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Hierarchy Level must be greater than 0.'
            });

            $('#hierarchylevel').focus();
            return false;
        }

        commonAjax.confirmAlert('Are you sure to proceed!');

        $('#btnConfirmOk').off('click').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {

        commonAjax.initCharCounter(['roleType', 'roleCode', 'description']);
        commonAjax.initClearableInputs();
        commonAjax.initSelect2('#org', 'Select Organization');
        commonAjax.initSelect2('#role', 'Select Role');
        commonAjax.initSelect2('#parent', 'Select Parent');

        let selectedOrg = "{{ old('org', $data['row']->organization_type_id ?? '') }}";
        let selectedRole = "{{ old('role', $data['row']->role_id ?? '') }}";
        let selectedParent = "{{ old('parent', $data['row']->parent_role_id ?? 0) }}";
        commonAjax.loadOrganizationTypeList(selectedOrg);

        // Wait for organization dropdown to load, then trigger change
        setTimeout(function() {
            $('#org').val(selectedOrg).trigger('change');
        }, 300);

        $('#org').on('change', function() {

            let orgId = $(this).val();

            if (orgId == '') {
                $('#role').html('<option value="">Select Role</option>');
                $('#parent').html('<option value="0">None</option>');
                return;
            }
            $.ajax({
                url: "{{ url('admin/roles/get-role-by-organization') }}/" + orgId,
                type: "GET",
                dataType: "json",
                success: function(response) {

                    let roleOptions = '<option value="">Select Role</option>';
                    let parentOptions = '<option value="0">None</option>';

                    if (response.status) {

                        $.each(response.data, function(i, item) {
                            roleOptions += `<option value="${item.id}" ${item.id == selectedRole ? 'selected' : ''}>
                                            ${item.role_name}
                                        </option>`;
                            parentOptions += `<option value="${item.id}" ${item.id == selectedParent ? 'selected' : ''}>
                                              ${item.role_name}
                                          </option>`;
                        });
                    }
                    $('#role').html(roleOptions).trigger('change');
                    $('#parent').html(parentOptions).trigger('change');
                }
            });
        });

    });
</script>
@endpush