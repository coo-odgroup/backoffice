@extends('admin.layouts.master')
@section('page_title', 'Department')
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
        <a href="{{ route('department-type.index') }}" class="btn btn-success btn-sm">
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
                                    <div class="row ">
                                        <div class="col-md-6 ">
                                            <label for="department">Department Name<span class="text-danger important">*</span></label>
                                            <input type="text"
                                                class="form-control form-control-sm clearable"
                                                id="department"
                                                name="department"
                                                maxlength="100"
                                                value="{{ $data['row']->department_name ?? '' }}">
                                            <small class="text-muted char-counter float-end"></small>
                                        </div>
                                        <div class="col-md-6 ">
                                            <label for="department_code">Department Code<span class="text-danger important">*</span></label>
                                            <input type="text"
                                                class="form-control form-control-sm clearable"
                                                id="department_code"
                                                name="department_code"
                                                maxlength="100"
                                                value="{{ $data['row']->department_code ?? '' }}">
                                            <small class="text-muted char-counter float-end"></small>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="department_desc">
                                                Description
                                            </label>

                                            <textarea
                                                class="form-control form-control-sm clearable"
                                                id="department_desc"
                                                name="department_desc"
                                                rows="3"
                                                maxlength="1000">{{ old('department_desc', $data['row']->description ?? '') }}</textarea>

                                            <small class="text-muted char-counter float-end"></small>
                                        </div>
                                    </div>

                                    <!-- BUTTONS -->
                                    <div class="row">
                                        <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                            <button class="btn btn-primary btn-sm" type="submit">
                                                {{ $data['strSubmit'] }}
                                            </button>
                                            @if($data['strReset'] == 'Cancel')
                                            <a href="{{ route('department-type.index') }}" class="btn btn-secondary btn-sm">
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
        if (!validator.blankCheck('department', 'Department Name cannot be left blank'))
            return false;
        if (!validator.maxLength('department', 100, 'Department  Can not be more than 100 chracters'))
            return false;
        if (!validator.blankCheck('department_code', 'Department Code Name cannot be left blank'))
            return false;
        if (!validator.maxLength('department_code', 100, 'Department Code Can not be more than 100 chracters'))
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
        commonAjax.initCharCounter(['department_code']);
        commonAjax.initClearableInputs();
        commonAjax.initCharCounter(['department', 'department_code', 'department_desc']);
        commonAjax.initClearableInputs();

        $('#department').on('keyup', function() {

            let val = $(this).val();

            val = val.toUpperCase();
            val = val.replace(/\s+/g, '_');
            val = val.replace(/[0-9]/g, '');
            val = val.replace(/[^A-Z_]/g, '');

            $('#department_code').val(val);
        });

    });
</script>
@endpush