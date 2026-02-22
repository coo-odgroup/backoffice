@extends('admin.layouts.master')
@section('content')

<?php
$page_name = 'All Roles';
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">{{ $data['strPage'] }} Roles</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">Roles</h5>
    <div>
        <a href="{{ route('roles.index') }}" class="btn btn-success btn-sm">
            View Roles
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
                                        <div class="col-md-4 mb-3">
                                            <label for="roleType">Role Type<span class="text-danger important" >*</span></label>
                                            <input type="text" class="form-control" id="roleType" placeholder="Role Type" name="roleType" value="{{ $data['row']->name ?? '' }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="roleCode">Role Code<span class="text-danger important" >*</span></label>
                                            <input type="text" class="form-control" id="roleCode" placeholder="Role Code" name="roleCode" value="{{ $data['row']->code ?? '' }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="systemRolesType">System Role Type<span class="text-danger important">*</span></label>
                                            <select class="form-select" id="systemRolesType" name="Type">
                                                <option value="1"
                                                    {{ (isset($data['row']) && $data['row']->is_system_role == 1) ? 'selected' : '' }}>
                                                    True
                                                </option>

                                                <option value="0" selected
                                                    {{ (isset($data['row']) && $data['row']->is_system_role == 0) ? 'selected' : '' }}>
                                                    False
                                                </option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" id="description" name="description" placeholder="Description" rows="3">{{ $data['row']->description ?? '' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- BUTTONS -->
                                    <div class="row mt-4">
                                        <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                            <button class="btn btn-primary btn-sm" type="submit">
                                                {{ $data['strSubmit'] }}
                                            </button>
                                            @if($data['strReset'] == 'Cancel')
                                            <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
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

        if (!validator.selectDropdown('systemRolesType', ' System Role Type'))
            return false;
        if (!validator.blankCheck('roleCode', 'Role Code cannot be left blank'))
            return false;
        if (!validator.maxLength('roleCode', 100, 'Role Code'))
            return false;
        if (!validator.maxLength('description', 256, 'Description'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function () {

        $('#roleCode').on('keyup', function () {

            let val = $(this).val();
            val = val.toUpperCase();
            val = val.replace(/\s+/g, '_');
            val = val.replace(/[0-9]/g, '');
            val = val.replace(/[^A-Z_]/g, '');

            $(this).val(val);
        });

    });
</script>
@endpush