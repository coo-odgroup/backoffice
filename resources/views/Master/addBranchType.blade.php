@extends('admin.layouts.master')
@section('page_title', 'Branch Type')
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
        <a href="{{ route('branch-type.index') }}" class="btn btn-success btn-sm">
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

                                            <div class="col-md-10  mb-2">
                                                <label for="branchName">Branch Type Name</label>

                                                <input type="text"
                                                    class="form-control form-control-sm clearable "
                                                    id="branchName"
                                                    name="branchName"
                                                    value="{{ old('branchName',$data['row']->branch_type_name ?? '') }}">
                                            </div>

                                            <div class="col-md-10  mb-2">
                                                <label for="branchCode">Branch Type Code<span class="text-danger important">*</span></label>
                                                <input type="text" class="form-control form-control-sm clearable" id="branchCode" name="branchCode" value="{{ $data['row']->branch_type_code ?? '' }}">
                                            </div>
                                            <div class="col-md-10  mb-2">
                                                <label for="displyOrder">Display Order<span class="text-danger important">*</span></label>
                                                <input type="number"
                                                    min="1"
                                                    class="form-control form-control-sm clearable"
                                                    id="displyOrder"
                                                    name="displyOrder"
                                                    value="{{ old('displyOrder',$data['row']->display_order ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col">

                                            <div class="col-md-12">
                                                <label for="desc">
                                                    Description<span class="text-danger important">*</span>
                                                </label>
                                                <textarea
                                                    class="form-control form-control-sm clearable"
                                                    id="desc"
                                                    name="desc"
                                                    rows=" 10 "
                                                    placeholder="Enter small Description">{{ old('description', $data['row']->description ?? '') }}</textarea>
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

        let selectedOrg = "{{ old('org', $data['row']->organization_type_id ?? '') }}";
        commonAjax.loadOrganizationTypeList(selectedOrg);
        commonAjax.initClearableInputs();
        commonAjax.initClearableInputs();

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
</script>
@endpush