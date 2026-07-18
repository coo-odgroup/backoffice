@extends('admin.layouts.master')
@section('page_title', 'Document Type')
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
        <a href="{{ route('documentType.index') }}" class="btn btn-success btn-sm">
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
                                        <div class="col-md-4 ">
                                            <label for="documentCode">Document Code<span class="text-danger important">*</span></label>
                                            <input type="text"
                                                class="form-control form-control-sm clearable"
                                                id="documentCode"
                                                name="documentCode"
                                                maxlength="100"
                                                value="{{ $data['row']->document_code ?? '' }}">
                                            <small class="text-muted char-counter float-end"></small>
                                        </div>

                                        <div class="col-md-6 d-flex  ">
                                            <div class="form-check mt-4">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="is_mandatory"
                                                    name="is_mandatory"
                                                    value="1"
                                                    style="border: 2px solid #000;"
                                                    {{ old('is_mandatory', $data['row']->is_mandatory ?? 0) ? 'checked' : '' }}

                                                    <label class="form-check-label" for="is_mandatory">
                                                Is Mandatory
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="row mb-2">
                                            <div class="col-md-4 ">
                                                <label for="documentType">Document Name<span class="text-danger important">*</span></label>
                                                <input type="text"
                                                    class="form-control form-control-sm clearable"
                                                    id="documentType"
                                                    name="documentType"
                                                    maxlength="100"
                                                    value="{{ $data['row']->document_name ?? '' }}">
                                                <small class="text-muted char-counter float-end"></small>
                                            </div>

                                            <div class="col-md-6 d-flex  ">
                                                <div class="form-check mt-4">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        id="has_expiry"
                                                        name="has_expiry"
                                                        value="1"
                                                        style="border: 2px solid #000;"
                                                        {{ old('has_expiry', $data['row']->has_expiry ?? 0) ? 'checked' : '' }}>

                                                    <label class="form-check-label" for="has_expiry">
                                                        Has Expiry
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
                                                <a href="{{ route('documentType.index') }}" class="btn btn-secondary btn-sm">
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

        if (!validator.blankCheck('documentCode', 'Document Code cannot be left blank'))
            return false;

        if (!validator.maxLength('documentCode', 100, 'Document Code'))
            return false;

        if (!validator.blankCheck('documentType', 'Document Name cannot be left blank'))
            return false;

        if (!validator.maxLength('documentType', 100, 'Document Name'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').off('click').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {
        commonAjax.initCharCounter(['documentType', 'documentType']);
        commonAjax.initClearableInputs();
        
        $('#documentCode').on('keyup', function() {

            let val = $(this).val();

            val = val.toUpperCase();
            val = val.replace(/\s+/g, '_');
            val = val.replace(/[^A-Z0-9_]/g, '');

            $(this).val(val);

        });

    });
</script>
@endpush