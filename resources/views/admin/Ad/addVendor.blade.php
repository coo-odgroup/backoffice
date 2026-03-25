@extends('admin.layouts.master')
@section('page_title', 'Add Vendor')
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
        <li class="breadcrumb-item active">@yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('vendor.index') }}" class="btn btn-success btn-sm">
            View Vendor
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
                                        <div class="col-md-4 mb-3">
                                            <label for="companyName">Company Name</label>
                                            <input type="text" class="form-control form-select-sm" id="companyName" placeholder="Company Name" name="companyName" maxlength="50" value="{{ $data['row']->company_name ?? '' }}">

                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="personName">Person Name<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control form-select-sm" id="personName" placeholder="Person Name" name="personName" onkeypress="return validator.isOnlyCharSpace(event)" maxlength="100" value="{{ $data['row']->contact_person ?? '' }}">

                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="email">Email Id<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control form-select-sm" id="email" placeholder="Email Id" name="email" maxlength="100" value="{{ $data['row']->email ?? '' }}">

                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="phone">Phone Number<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control form-select-sm" id="phone" placeholder="Phone Number" name="phone" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')" value="{{ $data['row']->phone ?? '' }}">

                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="gst">GST Number<span class="text-danger important">*</span></label>
                                            <input type="text"
                                                class="form-control form-select-sm"
                                                id="gst"
                                                placeholder="GST Number"
                                                name="gst"
                                                maxlength="15"
                                                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g,'')" value="{{ $data['row']->gst_number ?? '' }}">
                                        </div>


                                        <!-- BUTTONS -->
                                        <div class="row">
                                            <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                                <button class="btn btn-primary btn-sm" type="submit">
                                                    {{ $data['strSubmit'] }}
                                                </button>
                                                @if($data['strReset'] == 'Cancel')
                                                <a href="{{ route('vendor.index') }}" class="btn btn-secondary btn-sm">
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

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.blankCheck('personName', 'Person Name cannot be left blank'))
            return false;

        let name = $('#personName').val().trim();

        if (name.length < 3) {
            commonAjax.viewAlert('Person Name must contain at least 3 letters');
            return false;
        }

        if (name.length > 100) {
            commonAjax.viewAlert('Person Name cannot be more than 100 characters');
            return false;
        }

        if (!validator.blankCheck('email', 'Email cannot be left blank'))
            return false;

        if (!validator.maxLength('email', 100, 'Email Id cannot be more than 100 characters'))
            return false;

        if (!validator.validEmail('email', 'Please enter a valid Email Id'))
            return false;

        if (!validator.maxLength('gst', 15, 'GST Number cannot be more than 15 characters'))
            return false;

        if (!validator.blankCheck('phone', 'Phone Number cannot be left blank'))
            return false;

        if (!validator.maxLength('phone', 10, 'Phone Number cannot be more than 10 digits'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').off('click').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {
        commonAjax.initCharCounter(['companyName', 'personName', 'email', 'phone', 'gst']);

    });
</script>
@endpush