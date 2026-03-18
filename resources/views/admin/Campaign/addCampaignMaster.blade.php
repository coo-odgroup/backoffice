@extends('admin.layouts.master')
@section('page_title', 'Campaign Master')
@section('content')

<?php
$page_name = 'All '.trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Campaign</li>
        <li class="breadcrumb-item active">{{ $data['strPage'] }} @yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('campaign-master.index') }}" class="btn btn-success btn-sm">
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
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="campaign_name">Campaign Name<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control" id="campaign_name" name="campaign_name" value="{{ $data['row']->campaign_name ?? old('campaign_name') }}" placeholder="Enter Campaign Name">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="start">Start Time<span class="text-danger important">*</span></label>
                                            <input type="date" class="form-control" id="start" name="start" value="{{ $data['row']->start ?? old('start') }}" placeholder="Enter Start Time">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="stop">Stop Time<span class="text-danger important">*</span></label>
                                            <input type="date" class="form-control" id="stop" name="stop" value="{{ $data['row']->stop ?? old('stop') }}" placeholder="Enter Stop Time">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="short_desc">Short Description</label>
                                            <textarea class="form-control" id="short_desc" name="short_desc" placeholder="Enter Short Description">{{ $data['row']->short_desc ?? old('short_desc') }}</textarea>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="full_desc">Full Description</label>
                                            <textarea class="form-control" id="full_desc" name="full_desc" placeholder="Enter Full Description">{{ strip_tags(html_entity_decode($data['row']->full_desc ?? old('full_desc'))) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- BUTTONS -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            {{ $data['strSubmit'] }}
                                        </button>
                                        @if($data['strReset'] == 'Cancel')
                                        <a href="{{ route('campaign-master.index') }}" class="btn btn-secondary btn-sm">
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

    document.addEventListener('DOMContentLoaded', function() {
        initCkEditor('#full_desc');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.blankCheck('campaign_name', 'Campaign Name cannot be left blank'))
            return false;
        if (!validator.maxLength('campaign_name', 100, 'Campaign Name'))
            return false;

        if (!validator.blankCheck('start', 'Start time cannot be left blank'))
            return false;

        if (!validator.blankCheck('stop', 'Stop time cannot be left blank'))
            return false;

        if (!validator.maxLength('short_desc', 255, 'Short Desctiption'))
            return false;

        if (!validator.maxLength('full_desc', 255, 'Full Desctiption'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });
</script>
@endpush
