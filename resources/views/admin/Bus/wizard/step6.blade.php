@extends('admin.layouts.master')
@section('page_title', 'City Timings')
@section('content')

<style>
    #previewList .d-flex {
        cursor: move;
    }
</style>

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
        <a href="{{ route('amenities.index') }}" class="btn btn-success btn-sm">
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

                                    <div id="step6">

                                        <h3 class="fw-bold mb-4 border-bottom pb-2">Update Contact Info</h3>

                                        <div class="row mb-4">

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Bus Number</label>
                                                <input type="text" class="form-control form-control-sm" placeholder="Bus Number" disabled value="{{$data['bus_number']}}" />
                                            </div>

                                        </div>

                                        <!-- Conductor Row -->
                                        <div class="row align-items-center mb-4">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Conductor Number</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="Conductor Number"
                                                    name="contacts[0][phone]">
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">SMS On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[0][booking_sms_send]" value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[0][cancel_sms_send]" value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[0][booking_wp_send]" value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[0][cancel_wp_send]" value="1">
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Manager Row -->
                                        <div class="row align-items-center mb-4">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Manager Number</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="Manager Number"
                                                    name="contacts[1][phone]">
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">SMS On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[1][booking_sms_send]" value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[1][cancel_sms_send]" value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[1][booking_wp_send]" value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[1][cancel_wp_send]" value="1">
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Owner Row -->
                                        <div class="row align-items-center mb-4">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Owner Number</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="Owner Number"
                                                    name="contacts[2][phone]">
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">SMS On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[2][booking_sms_send]" value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[2][cancel_sms_send]" value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[2][booking_wp_send]" value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[2][cancel_wp_send]" value="1">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="text-center mt-1">
                                            <input type="hidden" name="bus_id" value="{{$data['bus_id']}}">
                                            <button class="btn btn-warning px-5 rounded-pill me-3">
                                                Back
                                            </button>
                                            <button type="submit" class="btn btn-warning px-5 rounded-pill">Preview →</button>

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
</script>
@endpush