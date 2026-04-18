@extends('admin.layouts.master')
@section('page_title', 'Contact Info')
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
        <a href="{{ route('bus.index') }}" class="btn btn-success btn-sm">
            View Bus List
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
                                                <label class="form-label fw-semibold">Conductor Number <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="Conductor Number"
                                                    name="contacts[0][phone]" id="conductorNumber" value="{{ @$data['step6Res'][0]->phone }}">
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">SMS On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[0][booking_sms_send]" {{ @$data['step6Res'][0]->booking_sms_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[0][cancel_sms_send]" {{ @$data['step6Res'][0]->cancel_sms_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[0][booking_wp_send]" {{ @$data['step6Res'][0]->booking_wp_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[0][cancel_wp_send]" {{ @$data['step6Res'][0]->cancel_wp_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Manager Row -->
                                        <div class="row align-items-center mb-4">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Manager Number</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="Manager Number"
                                                    name="contacts[1][phone]" id="managerNumber" value="{{ @$data['step6Res'][1]->phone }}">
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">SMS On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[1][booking_sms_send]" {{ @$data['step6Res'][1]->booking_sms_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[1][cancel_sms_send]" {{ @$data['step6Res'][1]->cancel_sms_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[1][booking_wp_send]" {{ @$data['step6Res'][1]->booking_wp_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[1][cancel_wp_send]" {{ @$data['step6Res'][1]->cancel_wp_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Owner Row -->
                                        <div class="row align-items-center mb-4">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Owner Number</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="Owner Number"
                                                    name="contacts[2][phone]" id="ownerNumber" value="{{ @$data['step6Res'][2]->phone }}">
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">SMS On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[2][booking_sms_send]" {{ @$data['step6Res'][2]->booking_sms_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[2][cancel_sms_send]" {{ @$data['step6Res'][2]->cancel_sms_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[2][booking_wp_send]" {{ @$data['step6Res'][2]->booking_wp_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-center">
                                                <label class="form-label fw-semibold d-block">Cancellation Wp On Ticket</label>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input red-switch" type="checkbox"
                                                        name="contacts[2][cancel_wp_send]" {{ @$data['step6Res'][2]->cancel_wp_send ? 'checked' : '' }} value="1">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="text-center mt-1">
                                            <input type="hidden" name="bus_id" value="{{$data['bus_id']}}">
                                            <input type="hidden" name="param" value="{{$data['param']}}">
                                            <input type="hidden" name="param2" value="{{$data['param2']}}">
                                            <input type="hidden" name="existRes" value="{{ $data['existRes'] ?? 0 }}">
                                            @php
                                            $isSave = ($data['param'] ?? null) === 'save';
                                            $isBack = ($data['param2'] ?? null) === 'back';
                                            @endphp

                                            @if ($data['param2'] == 'edit')
                                            <button type="submit" class="btn btn-success px-5 rounded-pill">Update & Continue →</button>
                                            @else

                                            @if ($isSave)
                                            <a href="{{ url($createBusUrl.'step5/'.$data['enc_bus_id'].'/save/back') }}"
                                                class="btn btn-secondary px-5 rounded-pill me-3">
                                                ← Back
                                            </a>
                                            @endif

                                            @if (($isSave && $isBack) || @$data['existRes'] == 1)
                                            <a href="{{ url($createBusUrl.'step7/'.$data['enc_bus_id'].'/save') }}"
                                                class="btn btn-warning px-5 rounded-pill me-3">
                                                Continue →
                                            </a>
                                            @endif

                                            <button type="submit" class="btn btn-success px-5 rounded-pill">
                                                Save & Continue →
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
    </div>
</form>
@endsection
@push('scripts')

<script type="module">
    $(document).ready(function() {
        commonAjax.allowOnlyNumbers(['conductorNumber', 'managerNumber', 'ownerNumber']); // Ids
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.blankCheck('conductorNumber', 'Conductor Number cannot be left blank'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').one('click', function() {
            e.currentTarget.submit();
        });

    });
</script>
@endpush