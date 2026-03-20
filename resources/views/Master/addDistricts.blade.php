@extends('admin.layouts.master')
@section('page_title', 'Districts')
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
        <a class="btn btn-success btn-sm" href="{{ route('district.index') }}">View @yield('page_title')
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
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="selState">State<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm" id="selState" name="selState">
                                                <option value="0">Select State</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="txtDistrict">District Name<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control form-control-sm" id="txtDistrict" name="txtDistrict" value="{{ $data['row']->district_name ?? '' }}" placeholder="Enter District">
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
                                        <a class="btn btn-secondary btn-sm" type="button" href="{{ route('district.index') }}">
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
    document.getElementById('txtDistrict').addEventListener('input', function() {

        let cityName = this.value;

        let alias = cityName
            .toLowerCase() // convert to lowercase
            .trim() // remove extra spaces
            .replace(/[^a-z0-9\s-]/g, '') // remove special characters
            .replace(/\s+/g, '-') // replace spaces with -
            .replace(/-+/g, '-'); // remove duplicate -

    });


    $(document).ready(function() {

        commonAjax.initSelect2('#selState', 'Select State');

        let state_id = <?= $data['row']->state_id ?? '0' ?>

        commonAjax.loadStateList(state_id);
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.selectDropdown('selState', 'Select State'))
            return false;

        if (!validator.blankCheck('txtDistrict', 'District Name cannot be left blank'))
            return false;
        if (!validator.maxLength('txtDistrict', 100, 'District Name'))
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