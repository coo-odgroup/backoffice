@extends('admin.layouts.master')
@section('content')

<?php
$page_name = 'Add Seat Layout';
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">{{ $data['strPage'] }} Seat Layout</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">Add Seat Layout</h5>
    <div>
        <a href="{{ route('seatlayout.index') }}" class="btn btn-success btn-sm">
            View Seat Layout
        </a>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form">
    {{csrf_field()}}
    <input type="hidden" name="seat_layout_json" id="seat_layout_json">
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
                                        <div class="col-md-3 mb-3">
                                            <label for="layout_name">Seat Layout Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control clearable form-control-sm" id="layout_name" name="layout_name" placeholder="Seat Layout Name" value="{{ $data['row']->layout_name ?? '' }}">
                                            <small id="layoutError" class="text-danger"></small>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="classType">Rows<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm clearable" id="rows" name="rows">
                                                <option value="0">Rows</option>
                                               @for ($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}" 
                                                        {{ (isset($data['row']->rows) && $data['row']->rows == $i) ? 'selected' : '' }}>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="classType">Columns<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm clearable" id="cols" name="cols">
                                                <option value="0">Columns</option>
                                                    @for ($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" 
                                                            {{ (isset($data['row']->cols) && $data['row']->cols == $i) ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                     @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="classType">Bus Tier<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm clearable" id="busTier" name="busTier">
                                                <option value="0">Select Tier</option>
                                                <option value="1" {{ (isset($data['row']->tier) && $data['row']->tier == 1) ? 'selected' : '' }}>1 Tier</option>
                                                <option value="2" {{ (isset($data['row']->tier) && $data['row']->tier == 2) ? 'selected' : '' }}>2 Tier</option>
                                                <option value="3" {{ (isset($data['row']->tier) && $data['row']->tier == 3) ? 'selected' : '' }}>3 Tier</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3 align-self-end">
                                            <button id="generateBtn" type="button" class="btn btn-sm btn-primary">Generate</button>
                                        </div>
                                    </div>
                                    <!-- Preview Seat Layout -->
                                    <div class="row mb-2" id="seat_layout">
                                        <div class="row" id="upperSection">
                                            <div class="col-6">
                                                <h5>Upper Berth</h5>
                                                <div id="UPPER"></div>
                                            </div>
                                            <div class="col-6 d-flex justify-content-center align-items-center">
                                                <div class="berth-row">
                                                    <div class="berth-label">Upper Berth</div>

                                                    <div class="layout-box">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="lowerSection">
                                            <div class="col-6">
                                                <h5 class="mt-4">Lower Berth</h5>
                                                <div id="LOWER"></div>
                                            </div>
                                            <div class="col-6 d-flex justify-content-center align-items-center">
                                                <div class="berth-row">
                                                    <div class="berth-label">Lower Berth</div>

                                                    <div class="layout-box">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-11 justify-content-end mb-3" id="validBtn" style="display: none;">
                                        <button id="validBtns" type="button" class="btn btn-secondary">Validate Seat</button>
                                    </div>
                                    <div class="col-md-11" id="tag" style="display: none;">
                                        <label for="classType">Window Seat<span class="text-danger important">*</span></label>
                                        <input id="tags" name="window_seat" class="form-control form-control-sm" placeholder="Enter Window Seat">
                                        <small id="seatError" class="text-danger"></small>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" id="saveLayout">
                                               Submit
                                            </button>
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
        let saveBtn = $('#saveLayout');
        // Disable initially
        saveBtn.hide();
        commonAjax.initClearableInputs();
    });
    
    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.blankCheck('rows', 'Row cannot be left blank'))
            return false;
        if (!validator.blankCheck('cols', 'Col cannot be left blank'))
            return false;
        if (!validator.blankCheck('layout_name', 'Layout Name cannot be left blank'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

        const layout = seatAjax.generateSeatJSON();     

        document.getElementById("seat_layout_json").value = JSON.stringify(layout);

    });

    //For validation window seats

    let validSeats = [];

    function updateValidSeats() {
        $('#saveLayout').show();
        const layout = seatAjax.generateSeatJSON();

        validSeats = layout
            .map(seat => seat.seat_text)
            .filter(seat => seat !== null);

        console.log("Valid Seats:", validSeats);
    }

    $('#validBtns').on('click', function() {
        updateValidSeats();
        document.getElementById('tag').style.display = 'block';
    });

    $('#tags').on('change', function() {

        let errorBox = $('#seatError');

        let input = $(this).val();

        if (!input) {
            errorBox.text('');
            return;
        }

        // Parse JSON if needed
        if (typeof input === "string") {
            try {
                input = JSON.parse(input);
            } catch (e) {
                errorBox.text("Invalid format");
                return;
            }
        }

        // Extract values
        let enteredSeats = input.map(item => item.value);

        let invalidSeats = enteredSeats.filter(seat => !validSeats.includes(seat));

        if (invalidSeats.length > 0) {
            errorBox.text("Invalid seats: " + invalidSeats.join(', '));
            // saveBtn.hide();

        } else {
            errorBox.text('');
            // saveBtn.show();

        }
    });


    document.addEventListener('DOMContentLoaded', () => {

        const btn = document.getElementById('generateBtn');
        const upperContainer = document.querySelectorAll('.layout-box')[0];
        const lowerContainer = document.querySelectorAll('.layout-box')[1];

        if (!upperContainer || !lowerContainer) return;

        // Initial default grid
        //seatAjax.initLayout(5, 10);

        if (btn) {
            btn.addEventListener('click', () => {

                let tier = $('#busTier').val();

                if (tier == "1") {
                    // Only LOWER
                    $('#upperSection').hide();
                    $('#lowerSection').show();
                } else if (tier == "2") {
                    // LOWER + UPPER
                    $('#upperSection').show();
                    $('#lowerSection').show();
                } else if (tier == "3") {
                    // For future (same as 2 for now)
                    $('#upperSection').show();
                    $('#lowerSection').show();
                }

                const rows = parseInt(document.getElementById('rows').value);
                const cols = parseInt(document.getElementById('cols').value);

                if (!rows || !cols) {
                    alert("Select rows and columns");
                    return;
                }

                seatAjax.initLayout(rows, cols);
                document.getElementById('seat_layout').style.display = 'block';
                document.getElementById('validBtn').style.display = 'block';
            });
        }

    });

    $('#layout_name').on('change', function() {

        let layoutName = $(this).val().trim();
        let errorBox = $('#layoutError');

        if (layoutName === '') {
            errorBox.text('Layout name is required');
            return;
        }

        $.ajax({
            url: "{{ url('admin/check-layout-name') }}",
            type: 'POST',
            data: {
                layout_name: layoutName,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {

                if (res.exists) {
                    errorBox.text('Layout name already exists');
                    $('#generateBtn').prop('disabled', true);
                } else {
                    errorBox.text('');
                    $('#generateBtn').prop('disabled', false);
                }
            }
        });

    });

  
</script>
@endpush