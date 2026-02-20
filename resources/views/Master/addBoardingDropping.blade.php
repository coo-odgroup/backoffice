@extends('admin.layouts.master')
@section('content')

<?php
$page_name = 'All Cities';
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">{{ $data['strPage'] }} Boarding Dropping</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">Boarding Dropping</h5>
    <div>
        <a href="{{ route('boardingDropping.index') }}" class="btn btn-success btn-sm">
            View Boarding Dropping
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
                                            <label for="selCity">City<span class="text-danger important">*</span></label>
                                            <select class="form-select selCity" id="selCity" name="selCity">
                                                <option value="0">Select City</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="boardingDroppingWrapper">
                                        <div class="row mb-3">
                                            <div class="col-md-4 mb-3">
                                                <label for="type">Type<span class="text-danger important">*</span></label>
                                                <select class="form-select type" id="type" name="type[]">
                                                    <option disabled selected>Select Type</option>
                                                    <option value="1">Boarding</option>
                                                    <option value="2">Dropping</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="brd_drp_point">Boarding / Dropping Point<span class="text-danger important">*</span></label>
                                                <input type="text" class="form-control brd_drp_point" placeholder="Enter Boarding / Dropping Point" id="brd_drp_point" name="brd_drp_point[]" value="{{ $data['row']->brd_drp_point ?? '' }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="landmark">Landmark</label>
                                                <input type="text" class="form-control" placeholder="Enter Landmark" name="landmark[]" value="{{ $data['row']->landmark ?? '' }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="latitude">Latitude</label>
                                                <input type="text" class="form-control latitude" placeholder="Enter Latitude" id="latitude" name="latitude[]" value="{{ $data['row']->latitude ?? '' }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="longitude">Longitude</label>
                                                <input type="text" class="form-control longitude" placeholder="Enter Longitude" id="longitude" name="longitude[]" value="{{ $data['row']->longitude ?? '' }}">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="sequence_no">Sequence No</label>
                                                <input type="text" class="form-control" id="sequence_no" placeholder="Enter Sequence No" name="sequence_no[]" value="{{ $data['row']->sequence_no ?? '1' }}">
                                            </div>
                                            <?php
                                            
                                    
                                            
                                            $isEdit = isset($data['row']->id) ? 'd-none' : ''; ?>
                                            <div class="col-md-1 d-flex align-items-end mb-3 {{ $isEdit }}">
                                                <button type="button" class="btn btn-outline-primary btn-add">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                            
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

    $(document).ready(function() {

        commonAjax.initSelect2('.selCity', 'Select City');

        let cities_id = <?= $data['row']->cities_id ?? '0' ?>

        commonAjax.loadCityList(cities_id);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.selectDropdown('selCity', 'Select City'))
            return false;

        if (!validator.selectDropdown('type', 'Select Type'))
            return false;

        if (!validator.blankCheck('brd_drp_point', 'Boarding/Dropping Point cannot be left blank'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    var rowCount = 1;

    $(document).ready(function () {

        $(document).on('click', '.btn-add', function () {

            rowCount++;

            $("#boardingDroppingWrapper").append(
                "<div id='bd_row" + rowCount + "' class='boarding-dropping-item'>" +

                    "<div class='row mb-3'>" +

                        "<div class='col-md-4 mb-3'>" +
                            "<label>Type<span class='text-danger important'>*</span></label>" +
                            "<select class='form-select ' name='type[]'>" +
                                "<option disabled selected>Select Type</option>" +
                                "<option value='1'>Boarding</option>" +
                                "<option value='2'>Dropping</option>" +
                            "</select>" +
                        "</div>" +

                        "<div class='col-md-4 mb-3'>" +
                            "<label>Boarding / Dropping Point<span class='text-danger important'>*</span></label>" +
                            "<input type='text' class='form-control brd_drp_point' name='brd_drp_point[]'>" +
                        "</div>" +

                        "<div class='col-md-4 mb-3'>" +
                            "<label>Landmark</label>" +
                            "<input type='text' class='form-control' name='landmark[]'>" +
                        "</div>" +

                        "<div class='col-md-4 mb-3'>" +
                            "<label>Latitude</label>" +
                            "<input type='text' class='form-control latitude' name='latitude[]'>" +
                        "</div>" +

                        "<div class='col-md-4 mb-3'>" +
                            "<label>Longitude</label>" +
                            "<input type='text' class='form-control longitude' name='longitude[]'>" +
                        "</div>" +

                        "<div class='col-md-3 mb-3'>" +
                            "<label>Sequence No</label>" +
                            "<input type='text' class='form-control' name='sequence_no[]' value='" + rowCount + "'>" +
                        "</div>" +

                        "<div class='col-md-1 d-flex align-items-end mb-3'>" +
                            "<button type='button' class='btn btn-outline-danger btn-remove' data-id='bd_row" + rowCount + "'>" +
                                "<i class='fa fa-trash'></i>" +
                            "</button>" +
                        "</div>" +

                    "</div>" +
                    "<hr>" +
                "</div>"
            );
        });

    });

    /* remove row */
    $(document).on('click', '.btn-remove', function () {
        if (!confirm('Are you sure to remove this entry !')) return;

        $("#" + $(this).data('id')).remove();
    });
</script>
@endpush