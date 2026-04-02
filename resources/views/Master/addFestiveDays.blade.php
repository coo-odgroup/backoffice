@extends('admin.layouts.master')
@section('page_title', 'Add Festive Day')
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
        <a href="{{ route('festiveDays.index') }}" class="btn btn-success btn-sm">
            View Festive Days
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
                                    <div class="row">
                                        <div class="row">

                                            <!-- ✅ Festive Name -->
                                            <div class="col-md-3 mb-3">
                                                <label for="festive_name">Festive Name <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control clearable form-control-sm"
                                                    id="festive_name"
                                                    name="festive_name"
                                                    placeholder="Enter Festive Name"
                                                    maxlength="100"
                                                    value="{{ $data['row']->short_desc ?? '' }}">
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label for="year">Year <span class="text-danger">*</span></label>
                                                <select class="form-select clearable form-select-sm" id="year" name="year">
                                                    <option value="">Select Year</option>

                                                    @for($y = date('Y'); $y <= date('Y') + 5; $y++)
                                                        <option value="{{ $y }}"
                                                        {{ (isset($data['row']->year) && $data['row']->year == $y) ? 'selected' : '' }}>
                                                        {{ $y }}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>
                                            <!-- ✅ Festive Date -->
                                            <div class="col-md-3 mb-3">
                                                <label for="festive_date">Festive Date <span class="text-danger">*</span></label>
                                                <input type="date"
                                                    class="form-control clearable form-control-sm"
                                                    id="festive_date"
                                                    name="festive_date"
                                                    value="{{ $data['row']->festive_date ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!--    Description -->
                                            <div class="col-md-9 mb-3">
                                                <label for="short_desc">Description</label>
                                                <textarea
                                                    class="form-control clearable form-control-sm"
                                                    id="short_desc"
                                                    name="short_desc"
                                                    rows="2"
                                                    placeholder="Enter Description">{{ $data['row']->short_desc ?? '' }}</textarea>
                                            </div>

                                        </div>

                                    </div>

                                    <!-- BUTTONS -->
                                    <div class="row align-items-start">
                                        <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                            <button class="btn btn-primary btn-sm" type="submit">
                                                {{ $data['strSubmit'] }}
                                            </button>

                                            @if($data['strReset'] == 'Cancel')
                                            <a href="{{ route('festiveDays.index') }}" class="btn btn-secondary btn-sm">
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

        if (!validator.blankCheck('festive_name', 'Festive Name cannot be blank')) return false;
        if (!validator.maxLength('festive_name', 100, 'Festive Name')) return false;

        if (!validator.blankCheck('festive_date', 'Festive Date cannot be blank')) return false;

        if (!validator.blankCheck('year', 'Year cannot be blank')) return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });
    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });
    document.getElementById('festive_date').addEventListener('change', function() {
        let year = new Date(this.value).getFullYear();
        document.getElementById('year').value = year;
    });
    $(document).ready(function() {
        commonAjax.initClearableInputs();
        commonAjax.initCharCounter(['festiveDays']);

    });
</script>
@endpush