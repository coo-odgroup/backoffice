   @extends('admin.layouts.master')
@section('page_title', 'Boarding / Dropping')
@section('content')

<?php
$page_name = 'All Boarding and Dropping Points';
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
                                    <div class="row ">
                                        <div class="col-md-4">
                                            <label for="selCity">City<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm selCity" id="selCity" name="selCity">
                                                <option value="0">Select City</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="boardingDroppingWrapper">
                                        <div class="row">
                                            <div class="col-md-4 mb-1">
                                                <label for="type">Type<span class="text-danger important">*</span></label>
                                                <select class="form-select form-select-sm type" id="type" name="type[]">
                                                    <option selected>Select Type</option>
                                                    <option value="1"
                                                        {{ (isset($data['row']) && $data['row']->type == 1) ? 'selected' : '' }}>
                                                        Boarding
                                                    </option>

                                                    <option value="2"
                                                        {{ (isset($data['row']) && $data['row']->type == 2) ? 'selected' : '' }}>
                                                        Dropping
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-1">
                                                <label for="brd_drp_point">Boarding / Dropping Point<span class="text-danger important">*</span></label>
                                                <input type="text" class="form-control form-control-sm brd_drp_point" data-check-url="{{ route('boardingDropping.checkExists') }}"
                                                    placeholder="Enter Boarding / Dropping Point" id="brd_drp_point" name="brd_drp_point[]" maxlength="100" value="{{ $data['row']->brd_drp_point ?? '' }}">
                                                <small class="text-muted char-counter float-end"></small>
                                            </div>
                                            <div class="col-md-4 mb-1">
                                                <label for="landmark">Landmark</label>
                                                <input type="text" class="form-control form-control-sm form-control" placeholder="Enter Landmark" id="landmark" name="landmark[]" maxlength="100" value="{{ $data['row']->landmark ?? '' }}">
                                                <small class="text-muted char-counter float-end"></small>
                                            </div>
                                            <div class="col-md-4 mb-1">
                                                <label for="latitude">Latitude</label>
                                                <input type="text" class="form-control form-control-sm latitude" placeholder="Enter Latitude" id="latitude" name="latitude[]" maxlength="7" value="{{ $data['row']->latitude ?? '' }}">
                                                <small class="text-muted char-counter float-end"></small>
                                            </div>
                                            <div class="col-md-4 mb-1">
                                                <label for="longitude">Longitude</label>
                                                <input type="text" class="form-control form-control-sm longitude" placeholder="Enter Longitude" id="longitude" name="longitude[]" maxlength="7" value="{{ $data['row']->longitude ?? '' }}">
                                                <small class="text-muted char-counter float-end"></small>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="sequence_no">Sequence No</label>
                                                <input type="text" class="form-control form-control-sm" id="sequence_no" placeholder="Enter Sequence No" name="sequence_no[]" maxlength="3" value="{{ $data['row']->sequence_no ?? '1' }}">
                                                <small class="text-muted char-counter float-end"></small>
                                            </div>
                                            <?php $isEdit = isset($data['row']->id) ? 'd-none' : ''; ?>
                                            <div class="col-md-1 d-flex align-items-center mb-3 <?= $isEdit ?>">
                                                <button type="button" class="btn btn-outline-primary btn-sm btn-add">
                                                    <i class="fa fa-plus"></i>
                                                </button>
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
                                        <a href="{{ route('boardingDropping.index') }}" class="btn btn-secondary btn-sm">
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

        commonAjax.initCharCounter(['brd_drp_point', 'landmark','sequence_no', 'latitude', 'longitude', '']);
        commonAjax.initSelect2('.selCity', 'Select City');

        let cities_id = <?= $data['row']->cities_id ?? '0' ?>

        commonAjax.loadCityList(cities_id);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        //City validation
        if (!validator.selectDropdown('selCity', 'Select City')) {
            return false;
        }

        let isValid = true;

        // Loop through each boarding / dropping row 
        $('#boardingDroppingWrapper .row').each(function(index) {

            let type = $(this).find('select[name="type[]"]');
            let point = $(this).find('input[name="brd_drp_point[]"]');

            if (!type.val() || type.val() === 'Select Type') {
                commonAjax.confirmAlert(
                    'Please select Type in row ' + (index + 1)
                );
                type.focus();
                isValid = false;
                return false;
            }

            if ($.trim(point.val()) === '') {
                commonAjax.confirmAlert(
                    'Boarding / Dropping Point cannot be blank in row ' + (index + 1)
                );
                point.focus();
                isValid = false;
                return false;
            }
        });

        if (!isValid) return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').off('click').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    var rowCount = 1;

  $(document).ready(function() {

    $(document).on('click', '.btn-add', function(e) {

        e.preventDefault();

        const testVal = $('#selCity').val();

        if (!testVal || testVal == 0) {
            commonAjax.confirmAlert('Please Select City');
            $('#selCity').focus();
            return false;
        }

        rowCount++;

        $("#boardingDroppingWrapper").append(

            "<div id='bd_row" + rowCount + "' class='boarding-dropping-item'>" +

                "<div class='row mb-3'>" +

                    "<div class='col-md-4 mb-1'>" +
                        "<label>Type<span class='text-danger important'>*</span></label>" +
                        "<select class='form-select form-select-sm type' name='type[]'>" +
                            "<option selected>Select Type</option>" +
                            "<option value='1'>Boarding</option>" +
                            "<option value='2'>Dropping</option>" +
                        "</select>" +
                    "</div>" +

                    "<div class='col-md-4 mb-1'>" +
                        "<label>Boarding / Dropping Point<span class='text-danger important'>*</span></label>" +
                        "<input type='text' maxlength='100' " +
                        "placeholder='Enter Boarding/Dropping Point' " +
                        "class='form-control form-control-sm brd_drp_point' " +
                        "data-check-url='{{ route('boardingDropping.checkExists') }}' " +
                        "name='brd_drp_point[]'>" +
                        "<small class='text-muted char-counter float-end'></small>" +
                    "</div>" +

                    "<div class='col-md-4 mb-1'>" +
                        "<label>Landmark</label>" +
                        "<input type='text' maxlength='100' " +
                        "class='form-control form-control-sm landmark' " +
                        "placeholder='Enter Landmark' " +
                        "name='landmark[]'>" +
                        "<small class='text-muted char-counter float-end'></small>" +
                    "</div>" +

                    "<div class='col-md-4 mb-1'>" +
                        "<label>Latitude</label>" +
                        "<input type='text' maxlength='7' " +
                        "class='form-control form-control-sm latitude' " +
                        "placeholder='Enter Latitude' " +
                        "name='latitude[]'>" +
                        "<small class='text-muted char-counter float-end'></small>" +
                    "</div>" +

                    "<div class='col-md-4 mb-1'>" +
                        "<label>Longitude</label>" +
                        "<input type='text' maxlength='7' " +
                        "class='form-control form-control-sm longitude' " +
                        "placeholder='Enter Longitude' " +
                        "name='longitude[]'>" +
                        "<small class='text-muted char-counter float-end'></small>" +
                    "</div>" +

                    "<div class='col-md-3 mb-1'>" +
                        "<label>Sequence No</label>" +
                        "<input type='text' maxlength='3' " +
                        "class='form-control form-control-sm sequence_no' " +
                        "name='sequence_no[]' value='" + rowCount + "'>" +
                        "<small class='text-muted char-counter float-end'></small>" +
                    "</div>" +

                    "<div class='col-md-1 d-flex align-items-center mb-1'>" +
                        "<button type='button' class='btn btn-outline-danger btn-sm btn-remove' data-id='bd_row" + rowCount + "'>" +
                            "<i class='fa fa-trash'></i>" +
                        "</button>" +
                    "</div>" +

                "</div>" +
                "<hr>" +
            "</div>"
        );

        // ✅ Use SELECTORS (class based)
        commonAjax.initCharCounter([
            '.brd_drp_point',
            '.landmark',
            '.latitude',
            '.longitude',
            '.sequence_no'
        ]);

    });

});

    /* remove row */
    $(document).on('click', '.btn-remove', function() {
        let id = $(this).data('id');
        $('#' + id).remove();
    });

    $(document).on('blur', '.brd_drp_point', function() {

        let $input = $(this);

        let point = $.trim($input.val());
        if (point === '') return;

        let cityId = $('#selCity').val();
        let type = $input.closest('.row').find('select[name="type[]"]').val();

        if (!cityId || !type) return;

        $.ajax({
            url: $input.data('check-url'),
            type: 'POST',
            data: {
                city_id: cityId,
                type: type,
                point: point,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {

                if (res.exists) {
                    commonAjax.confirmAlert('Boarding / Dropping point already exists');
                    $input.val('').focus();
                }
            }
        });
    });
</script>
@endpush