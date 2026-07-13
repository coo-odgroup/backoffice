@extends('admin.layouts.master')
@section('page_title', 'Organization Type ')
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
        <a href="{{ route('organization-type.index') }}" class="btn btn-success btn-sm">
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
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="row mb-2">
                                            <!-- Left Side -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="org">
                                                        Organization Type
                                                        <span class="text-danger important">*</span>
                                                    </label>
                                                    <select class="form-select form-select-sm selOrg" id="org" name="org">
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="orgName">
                                                        Organization Name
                                                        <span class="text-danger important">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control form-control-sm clearable"
                                                        id="orgName"
                                                        name="orgName"
                                                        value="{{ $data['row']->type_name ?? '' }}">
                                                </div>
                                            </div>

                                            <!-- Right Side -->
                                            <div class="col-md-6">
                                                <label for="description">
                                                    Description
                                                    <span class="text-danger important">*</span>
                                                </label>

                                                <textarea
                                                    class="form-control form-control-sm"
                                                    id="description"
                                                    name="description"
                                                    rows="5"
                                                    placeholder="Enter Description">{{ $data['row']->small_desc ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 d-flex align-items-center">

                                        <div class="w-100">
                                            <div class="row text-center">

                                                <div class="col-4">
                                                    <div class="form-check d-flex flex-column align-items-center">
                                                        <input class="form-check-input mb-2"
                                                            type="checkbox"
                                                            id="can_have_branches"
                                                            name="can_have_branches"
                                                            value="1"
                                                            {{ !empty($data['row']->is_branches) ? 'checked' : '' }}>

                                                        <label class="form-check-label" for="can_have_branches">
                                                            Can Have Branches
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-4">
                                                    <div class="form-check d-flex flex-column align-items-center">
                                                        <input class="form-check-input mb-2"
                                                            type="checkbox"
                                                            id="can_have_employees"
                                                            name="can_have_employees"
                                                            value="1"
                                                            {{ !empty($data['row']->is_employees) ? 'checked' : '' }}>

                                                        <label class="form-check-label" for="can_have_employees">
                                                            Can Have Employees
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-4">
                                                    <div class="form-check d-flex flex-column align-items-center">
                                                        <input class="form-check-input mb-2"
                                                            type="checkbox"
                                                            id="can_sell_tickets"
                                                            name="can_sell_tickets"
                                                            value="1"
                                                            {{ !empty($data['row']->is_sell_tickets) ? 'checked' : '' }}>

                                                        <label class="form-check-label" for="can_sell_tickets">
                                                            Can Sell Tickets
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>
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
    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.selectDropdown('org', 'Select Organization Type'))
            return false;
        if (!validator.blankCheck('orgName', 'Organization Name cannot be left blank'))
            return false;
        if (!validator.maxLength('orgName', 100, 'Organization Name'))
            return false;

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
        let selectedOrg = "{{ $data['row']->type_code ?? '' }}";
        commonAjax.loadAnnextureList([
            'ORGANIZATION_TYPE_CODE'
        ], function(data) {
            renderDropdown(
                '#org',
                data.ORGANIZATION_TYPE_CODE || [],
                  selectedOrg
            );
        });
        commonAjax.initClearableInputs();
        commonAjax.initClearableInputs();

    });

    function renderDropdown(selector, items = [], selected = '') {
        let options = '<option value="">Select Organization Type</option>';

        $.each(items, function(index, item) {

            let isSelected =
                selected == item.annexture_value ? 'selected' : '';

            options += `
            <option value="${item.annexture_value}" ${isSelected}>
                ${item.annexture_name}
            </option>
        `;
        });

        $(selector).html(options).trigger('change');
    }
</script>
@endpush