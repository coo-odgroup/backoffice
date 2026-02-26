@extends('admin.layouts.master')
@section('page_title', 'Cities')
@section('content')

<?php
$page_name = 'All '.trim($__env->yieldContent('page_title'));
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
        <a class="btn btn-success btn-sm" href="{{ route('cities.index') }}">View @yield('page_title')
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

                                <!-- FILTER FIELDS -->
                                <div class="col-12">
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="txtCity">City Name<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control"
                                                               id="txtCity"
                                                               name="txtCity"
                                                               value="{{ $data['row']->city_name ?? '' }}"
                                                               placeholder="Enter City Name"
                                                               maxlength="100">
                                            <small class="text-muted char-counter float-end"></small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="txtAlias">Alias<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control" id="txtCityAlias"
                                                   name="txtCityAlias" value="{{ $data['row']->alias ?? '' }}"
                                                   placeholder="Enter Alias"
                                                   oninput="this.value = this.value.toLowerCase();"
                                                   maxlength="100">
                                            <small class="text-muted char-counter float-end"></small>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="selState">State<span class="text-danger important">*</span></label>
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
                                    <div id="synonymContainer">
                                        @if($data['strPage']=="Edit")
                                            @if(!empty($data['synonyms']))
                                                @foreach($data['synonyms'] ?? [] as $index => $synonym)
                                                <div class="row mb-3 align-items-center synonym-row">
                                                    <div class="col-md-1">
                                                        <label for="txtSynonym" class="mb-0">
                                                            @if ($index==0)
                                                            Synonyms
                                                            @else
                                                            &nbsp;
                                                            @endif
                                                        </label>
                                                    </div>

                                                    <div class="col-md-5">
                                                        <input type="text" class="form-control synonym-input" name="txtSynonym[]" placeholder="Enter City Synonym" value="{{$synonym}}" maxlength="50">
                                                    </div>

                                                    <div class="col-md-1">
                                                        @if ($index==0)
                                                        <button type="button" class="btn btn-outline-primary btn-add">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                        @else
                                                        <button type="button" class="btn btn-outline-danger btn-remove">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            @else
                                            <div class="row mb-3 align-items-center synonym-row">
                                                <div class="col-md-1">
                                                    <label for="txtSynonym" class="mb-0">Synonyms</label>
                                                </div>

                                                <div class="col-md-5">
                                                    <input type="text" class="form-control synonym-input" name="txtSynonym[]" placeholder="Enter City Synonym" maxlength="50">
                                                </div>

                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-outline-primary btn-add">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endif
                                        @else
                                        <div class="row mb-3 align-items-center synonym-row">
                                            <div class="col-md-1">
                                                <label for="txtSynonym" class="mb-0">Synonyms</label>
                                            </div>

                                            <div class="col-md-5">
                                                <input type="text" class="form-control synonym-input" name="txtSynonym[]" placeholder="Enter City Synonym">
                                            </div>

                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-outline-primary btn-add">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- BUTTONS -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            {{ $data['strSubmit'] }}
                                        </button>
                                        @if($data['strReset'] == 'Cancel')
                                        <a href="{{ route('cities.index') }}" class="btn btn-secondary btn-sm">
                                            {{ $data['strReset'] }}
                                        </a>
                                        @else
                                        <button class="btn btn-secondary btn-sm" id="btnReset" type="button" id="resetBtn">
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

    document.getElementById('txtCity').addEventListener('input', function() {

        this.value = this.value.replace(/\s+/g, ' ').trimStart();

        let cityName = this.value;

        let alias = cityName
            .toLowerCase() // convert to lowercase
            .trim() // remove extra spaces
            .replace(/[^a-z0-9-\s]/g, '') // remove special characters
            .replace(/\s+/g, '-') // replace spaces with -
            .replace(/-+/g, '-'); // remove duplicate -

        document.getElementById('txtCityAlias').value = alias;
    });

    document.getElementById('txtCityAlias').addEventListener('input', function () {

        this.value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9-]/g, '')   // allow only a-z, 0-9, -
            .replace(/-+/g, '-')      // remove duplicate -
            .replace(/^-+$/g, '');     // remove hyphen from start & end

    });


    $(document).ready(function() {

        commonAjax.initSelect2('#selState', 'Select State');
        commonAjax.initSelect2('#selDistrict', 'Select District');

        let state_id = <?=$data['row'] -> state_id ?? 0 ?>;
        let district_id = <?=$data['row'] -> district_id ?? 0 ?>;

        commonAjax.loadStateList(state_id);
        commonAjax.getDistrictList(state_id, district_id);
        commonAjax.initCharCounter(['txtCity','txtCityAlias']);

        $('#resetBtn').click(function() {

            $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
            $('.form-select').val(0);
          
        });
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $(document).on('change', '#selState', function() {
        let state_id = $(this).val();
        commonAjax.getDistrictList(state_id);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.blankCheck('txtCity', 'City Name cannot be left blank')){
            return false;
        }
            
        if (!validator.maxLength('txtCity', 100, 'City Name')){
            return false;
        }

        if (!validator.blankCheck('txtCityAlias', 'City Alias cannot be left blank')){
            return false;
        }

        if (!validator.maxLength('txtCityAlias', 100, 'City Alias')){
            return false;
        }

        if (!validator.selectDropdown('selState', 'Select State')){
            return false;
        }

        if (!validator.checkAlias('txtCityAlias', 'City Alias')){
            return false;
        }

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.addEventListener('DOMContentLoaded', function () {

        const container = document.getElementById('synonymContainer');

        container.addEventListener('click', function (e) {

            // Add synonym Field
            if (e.target.closest('.btn-add')) {
                const newRow = document.createElement('div');
                newRow.className = 'row mb-3 align-items-center synonym-row';

                newRow.innerHTML = `
                    <div class="col-md-1">
                     &nbsp;
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control synonym-input" name="txtSynonym[]" placeholder="Enter City Synonym" maxlength="50">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-remove">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                `;

                container.appendChild(newRow);
            }

            // Remove synonym field
            if (e.target.closest('.btn-remove')) {
                const rows = container.querySelectorAll('.synonym-row');

                if (rows.length > 1) {
                    e.target.closest('.synonym-row').remove();
                }
            }
        });

    });
</script>

@endpush
