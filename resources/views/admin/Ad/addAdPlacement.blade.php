@extends('admin.layouts.master')
@section('page_title', 'Add Ad Placement')
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
        <a href="{{ route('AdPlacement.index') }}" class="btn btn-success btn-sm">
            View AD Placements
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
                                            <label for="placement">Placement<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control" id="placement" placeholder="Add Placement" name="placement" onkeypress="return validator.isOnlyCharSpace(event)" maxlength="100" value="{{ $data['row']->name ?? '' }}">

                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="slug">Slug<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control" id="slug" placeholder="Add Slug" name="slug" maxlength="100" value="{{ $data['row']->slug ?? '' }}">

                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="defaultModel">Default Model<span class="text-danger important">*</span></label>

                                            <select class="form-select" id="defaultModel" name="defaultModel">
                                                <option value="">Select Model</option>

                                                <option value="1" {{ (isset($data['row']) && $data['row']->default_model == 1) ? 'selected' : '' }}>
                                                    CPM
                                                </option>

                                                <option value="2" {{ (isset($data['row']) && $data['row']->default_model == 2) ? 'selected' : '' }}>
                                                    CPC
                                                </option>

                                                <option value="3" {{ (isset($data['row']) && $data['row']->default_model == 3) ? 'selected' : '' }}>
                                                    FIXED
                                                </option>
                                            </select>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 mb-3">
                                                <label for="description">Description</label>
                                                <textarea class="form-control" id="description" name="description" placeholder="Description" maxlength="500" rows="3">{{ $data['row']->description ?? '' }}</textarea>
                                                <small class="text-muted char-counter float-end"></small>
                                            </div>
                                        </div>


                                        <!-- BUTTONS -->
                                        <div class="row mt-4">
                                            <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                                <button class="btn btn-primary btn-sm" type="submit">
                                                    {{ $data['strSubmit'] }}
                                                </button>
                                                @if($data['strReset'] == 'Cancel')
                                                <a href="{{ route('AdPlacement.index') }}" class="btn btn-secondary btn-sm">
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

        if (!validator.blankCheck('placement', 'Placement cannot be left blank'))
            return false;

        let placement = $('#placement').val().trim();

        if (placement.length < 3) {
            commonAjax.viewAlert('Placement must contain at least 3 letters');
            return false;
        }

        if (placement.length > 100) {
            commonAjax.viewAlert('Placement cannot be more than 100 characters');
            return false;
        }

        if (!validator.blankCheck('slug', 'Slug cannot be left blank'))
            return false;

        if (!validator.maxLength('slug', 100, 'Slug cannot be more than 100 characters'))

            if (!validator.blankCheck('description', 'Description cannot be left blank'))
                return false;

        if (!validator.maxLength('description', 500, 'Description cannot be more than 500 characters'))
            return false;

        if ($('#defaultModel').val() == '') {
            commonAjax.viewAlert('Default Model cannot be left blank');
            $('#defaultModel').focus();
            return false;
        }

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').off('click').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {
        commonAjax.initCharCounter(['companyName', 'name', 'slug', 'phone', 'gst']);

    });
</script>
@endpush