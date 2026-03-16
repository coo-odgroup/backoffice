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
            View Bus Types
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
                                            <label for="classType">Class Type<span class="text-danger important">*</span></label>
                                            <select class="form-select" id="rows" name="rows">
                                                <option value="0">Rows</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="classType">Columns<span class="text-danger important">*</span></label>
                                            <select class="form-select" id="cols" name="cols">
                                                <option value="0">Columns</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="classType">Bus Tier<span class="text-danger important">*</span></label>
                                            <select class="form-select" id="busTier" name="busTier">
                                                <option value="0">Select Tier</option>
                                                <option value="1">2 Tier</option>
                                                <option value="2">3 Tier</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3 align-self-end">
                                            <button id="generateBtn" type="button" class="btn btn-secondary w-100">Generate</button>
                                        </div>
                                    </div>

                                    <!-- Preview Seat Layout -->
                                    <div class="row mb-2" id="seat_layout">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>Upper Berth</h5>
                                                <div id="UPPER"></div>
                                            </div>
                                            <div class="col-6 d-flex justify-content-center align-items-center">
                                                <div class="berth-row">
                                                    <div class="berth-label">Upper Berth</div>

                                                    <div class="layout-box">
                                                       <!-- <div class="preview-cell"></div> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <h5 class="mt-4">Lower Berth</h5>
                                                <div id="LOWER"></div>
                                            </div>
                                            <div class="col-6 d-flex justify-content-center align-items-center">
                                                <div class="berth-row">
                                                    <div class="berth-label">Lower Berth</div>

                                                    <div class="layout-box">
                                                        <!-- 1st Seat Row -->
                                                        <!-- <div class="preview-cell"></div> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" id="saveLayout">
                                                Save Seat Layout
                                            </button>
                                        </div>
                                    </div>

                                    <!-- BUTTONS -->
                                    <!-- <div class="row mt-4">
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
                                </div> -->
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

        if (!validator.selectDropdown('classType', 'Select Class Type'))
            return false;
        if (!validator.blankCheck('busType', 'Bus Type Name cannot be left blank'))
            return false;
        if (!validator.maxLength('busType', 100, 'Bus Type Name'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

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

            const rows = parseInt(document.getElementById('rows').value);
            const cols = parseInt(document.getElementById('cols').value);

            if (!rows || !cols) {
                alert("Select rows and columns");
                return;
            }

            seatAjax.initLayout(rows, cols);
            document.getElementById('seat_layout').style.display = 'block';
        });
    }

});

document.getElementById("backoffice-form").addEventListener("submit", function(e) {

    const layout = seatAjax.generateSeatJSON();

    console.log(layout);

    document.getElementById("seat_layout_json").value = JSON.stringify(layout);

});
</script>
@endpush