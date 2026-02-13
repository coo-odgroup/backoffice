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
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Data Tables</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">Cities</h5>
    <div>
        <button class="btn btn-success btn-sm">Viw Cities
        </button>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- FILTER -->
                    <div class="mb-3 border-bottom">
                        <div class="card-body">
                            <div class="row">
                                <!-- FILTER FIELDS -->
                                <div class="col-12">
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="txtCity">City Name<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control" id="txtCity" name="txtCity">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="txtAlias">Alias</label>
                                            <input type="text" class="form-control" id="txtAlias" name="txtAlias">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="selState">State</label>
                                            <select class="form-select" id="selState" name="selState">
                                                <option value="0">Select State</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="selDistrict">District</label>
                                            <select class="form-select" id="selDistrict" name="selDistrict">
                                                <option value="0">Select District</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-center synonym-row">
                                        <!-- Label -->
                                        <div class="col-md-1 text-left">
                                            <label for="txtSynonym" class="mb-0">Synonyms</label>
                                        </div>

                                        <!-- Input -->
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" id="txtSynonym" name="txtSynonym">
                                        </div>

                                        <!-- Plus Button -->
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-primary">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center synonym-row">
                                        <!-- Label -->
                                        <div class="col-md-1 text-left">
                                            <label for="txtSynonym" class="mb-0">Synonyms</label>
                                        </div>

                                        <!-- Input -->
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" id="txtSynonym" name="txtSynonym">
                                        </div>

                                        <!-- Plus Button -->
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>


                                </div>

                                <!-- BUTTONS -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                        <button class="btn btn-primary btn-sm" type="button" onclick="return getDataTableView()">
                                            Submit
                                        </button>
                                        <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                            Reset
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
    
    $('#backoffice-form').on('submit', function(e) {
        e.preventDefault();
    });


    $(document).ready(function() {

        commonAjax.initSelect2('#selState', 'Select State');
        commonAjax.initSelect2('#selDistrict', 'Select District');
        // By default hide filter
        $("#filterBox").hide();

        // Toggle on button click
        window.toggleFilter = function() {
            $("#filterBox").slideToggle(300);
        };
        commonAjax.loadStateList();
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $(document).on('change', '#selState', function() {
        let state_id = $(this).val();
        commonAjax.loadDistrictList(state_id);
        
    });



    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });


</script>

@endpush