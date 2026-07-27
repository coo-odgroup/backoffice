@extends('admin.layouts.master')
@section('page_title', 'Organization Department')
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
        <a href="{{ route('org-department.index') }}" class="btn btn-success btn-sm">
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
                                            <div class="col-md-10 mb-2">
                                                <label for="org">Organization Type<span class="text-danger important">*</span></label>
                                                <select class="form-select form-select-sm selOrg" id="org" name="org">
                                                </select>
                                            </div>

                                            <div class="col-md-10 mb-2">
                                                <label for="branch">Branch Type<span class="text-danger important">*</span></label>
                                                <select class="form-select form-select-sm selBranch" id="branch" name="branch">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">

                                            <div class="col-md-10 mb-2">
                                                <label for="dept">Department Type<span class="text-danger important">*</span></label>
                                                <select class="form-select form-select-sm selDepartment" id="dept" name="dept">
                                                </select>
                                            </div>

                                            <div class="col-md-10 mb-2">
                                                <label for="parentDept"> Parent Deprtment<span class="text-danger important">*</span></label>
                                                <select class="form-select form-select-sm selDepartment" id="parentDept" name="parentDept">
                                                </select>
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

        commonAjax.initSelect2('#org', 'Select Organization Type');
        commonAjax.initSelect2('#dept', 'Select Department Type');
        commonAjax.initSelect2('#parentDept', 'Select Praent Department Type');

        let selectedOrg = "{{ old('org', $data['row']->organization_id ?? '') }}";
        let selectedDepartment = "{{ old('dept', $data['row']->department_id ?? '') }}";
        let selectedParentDepartment = "{{ old('parentDept', $data['row']->parent_department_id ?? '') }}";

        commonAjax.loadOrganizationTypeList(selectedOrg);
        commonAjax.loadDepartmentList(selectedDepartment, '#dept');
        commonAjax.loadDepartmentList(selectedParentDepartment, '#parentDept');

        commonAjax.initClearableInputs();
        commonAjax.initClearableInputs();

    });

    $('#org').on('change', function() {

        let organization_id = $(this).val();

        commonAjax.loadBranchList(organization_id);

    });

    let selectedOrg = "{{ old('org', $data['row']->organization_id ?? '') }}";
    let selectedBranch = "{{ old('branch', $data['row']->branch_id ?? '') }}";

    commonAjax.loadOrganizationTypeList(selectedOrg);

    if (selectedOrg) {
        commonAjax.loadBranchList(selectedOrg, selectedBranch);
    }

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
</script>
@endpush