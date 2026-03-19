@extends('admin.layouts.master')
@section('page_title', 'Users')
@section('content')

<?php
$page_name = 'All ' . trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Bus Management</li>
        <li class="breadcrumb-item active">{{ $data['strPage'] }} @yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('users.index') }}" class="btn btn-success btn-sm">
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
                    <div class="mb-2">
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
                                    <div class="accordion" id="userFormAccordion">

                                        @if($data['edit_param']=='basic')
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingBasicEdit">
                                                <button class="accordion-button {{ ($data['edit_param'] ?? '') === 'basic' ? '' : 'collapsed' }}" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseBasicEdit"
                                                    aria-expanded="{{ ($data['edit_param'] ?? '') === 'basic' ? 'true' : 'false' }}"
                                                    aria-controls="collapseBasicEdit">
                                                    Basic Info
                                                </button>
                                            </h2>
                                            <div id="collapseBasicEdit"
                                                class="accordion-collapse collapse {{ ($data['edit_param'] ?? '') === 'basic' ? 'show' : '' }}"
                                                aria-labelledby="headingBasicEdit"
                                                data-bs-parent="#userFormAccordion">

                                                <div class="accordion-body">
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 mb-2">
                                                            <label for="userRole">User Role<span class="text-danger important">*</span></label>
                                                            <select class="form-select user_role" id="userRole" name="user_role">
                                                                <option value="0">Select User Role</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="name">Name<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="name" name="name" value="{{ $data['row']->name ?? old('name') }}" placeholder="Enter Name">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="organization_name">Organization Name<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="organization_name" name="organization_name" value="{{ $data['row']->organization_name ?? old('organization_name') }}" placeholder="Enter Organization Name">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="primary_email">Primary Email<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="primary_email" name="primary_email" value="{{ $data['row']->primary_email ?? old('primary_email') }}" placeholder="Enter Primary Email">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="primary_contact">Primary Contact<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="primary_contact" name="primary_contact" value="{{ $data['row']->primary_contact ?? old('primary_contact') }}" placeholder="Enter Primary Contact">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="location">Location<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="location" name="location" value="{{ $data['row']->location ?? old('location') }}" placeholder="Enter Location">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @elseif($data['strPage']=='Add')
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingBasic">
                                                <button class="accordion-button" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseBasic"
                                                    aria-expanded="true"
                                                    aria-controls="collapseBasic">
                                                    Basic Info
                                                </button>
                                            </h2>
                                            <div id="collapseBasic"
                                                class="accordion-collapse collapse show"
                                                aria-labelledby="headingBasic"
                                                data-bs-parent="#userFormAccordion">

                                                <div class="accordion-body">
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 mb-2">
                                                            <label for="userRole">User Role<span class="text-danger important">*</span></label>
                                                            <select class="form-select user_role" id="userRole" name="user_role">
                                                                <option value="0">Select User Role</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="name">Name<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="name" name="name" value="{{ $data['row']->name ?? old('name') }}" placeholder="Enter Name">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="organization_name">Organization Name<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="organization_name" name="organization_name" value="{{ $data['row']->organization_name ?? old('organization_name') }}" placeholder="Enter Organization Name">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="primary_email">Primary Email<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="primary_email" name="primary_email" value="{{ $data['row']->primary_email ?? old('primary_email') }}" placeholder="Enter Primary Email">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="primary_contact">Primary Contact<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="primary_contact" name="primary_contact" value="{{ $data['row']->primary_contact ?? old('primary_contact') }}" placeholder="Enter Primary Contact">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="location">Location<span class="text-danger important">*</span></label>
                                                            <input type="text" class="form-control" id="location" name="location" value="{{ $data['row']->location ?? old('location') }}" placeholder="Enter Location">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if($data['edit_param']=='moreinfo')
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingMoreEdit">
                                                <button class="accordion-button {{ ($data['edit_param'] ?? '') === 'moreinfo' ? '' : 'collapsed' }}"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseMoreEdit"
                                                    aria-expanded="{{ ($data['edit_param'] ?? '') === 'moreinfo' ? 'true' : 'false' }}"
                                                    aria-controls="collapseMoreEdit">
                                                    More Info
                                                </button>
                                            </h2>
                                            <div id="collapseMoreEdit"
                                                class="accordion-collapse collapse {{ ($data['edit_param'] ?? '') === 'moreinfo' ? 'show' : '' }}"
                                                aria-labelledby="headingMoreEdit"
                                                data-bs-parent="#userFormAccordion">

                                                <div class="accordion-body">
                                                    <div class="row mb-2">
                                                        <div class="col-md-3 mb-2">
                                                            <label for="secondary_email">Secondary Email</label>
                                                            <input type="text" class="form-control" id="secondary_email" name="secondary_email" value="{{ $data['row']->info->secondary_email ?? old('secondary_email') }}" placeholder="Enter Secondary Email">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="secondary_contact">Secondary Contact</label>
                                                            <input type="text" class="form-control" id="secondary_contact" name="secondary_contact" value="{{ $data['row']->info->secondary_contact ?? old('secondary_contact') }}" placeholder="Enter Secondary Contact">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="aadhaar_no">Aadhaar No</label>
                                                            <input type="text" class="form-control" id="aadhaar_no" name="aadhaar_no" value="{{ $data['row']->info->aadhaar_no ?? old('aadhaar_no') }}" placeholder="Enter Aadhaar No">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="pancard_no">Pancard No</label>
                                                            <input type="text" class="form-control" id="pancard_no" name="pancard_no" value="{{ $data['row']->info->pancard_no ?? old('pancard_no') }}" placeholder="Enter Pancard No">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="president_name">President Name</label>
                                                            <input type="text" class="form-control" id="president_name" name="president_name" value="{{ $data['row']->info->president_name ?? old('president_name') }}" placeholder="Enter President Name">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="president_phone">President Phone</label>
                                                            <input type="text" class="form-control" id="president_phone" name="president_phone" value="{{ $data['row']->info->president_phone ?? old('president_phone') }}" placeholder="Enter President Phone">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="general_secretary_name">General Secretary Name</label>
                                                            <input type="text" class="form-control" id="general_secretary_name" name="general_secretary_name" value="{{ $data['row']->info->general_secretary_name ?? old('general_secretary_name') }}" placeholder="Enter General Secretary Name">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="general_secretary_phone">General Secretary Phone</label>
                                                            <input type="text" class="form-control" id="general_secretary_phone" name="general_secretary_phone" value="{{ $data['row']->info->general_secretary_phone ?? old('general_secretary_phone') }}" placeholder="Enter General Secretary Phone">
                                                        </div>
                                                        <div class="col-md-1 mb-2">
                                                            <div class="form-check mt-4">
                                                                <input class="form-check-input" type="checkbox" id="has_gst" name="has_gst" value="1"
                                                                    {{ isset($data['row']->info) && $data['row']->info->has_gst == 1 ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="has_gst">
                                                                    Has GST
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 mb-2">
                                                            <label for="gst_no" class="form-label">GST No</label>
                                                            <input type="text" class="form-control" id="gst_no" name="gst_no"
                                                                value="{{ $data['row']->info->gst_no ?? old('gst_no') }}"
                                                                placeholder="Enter GST Number"
                                                                {{ isset($data['row']->info) && $data['row']->info->has_gst == 1 ? '' : 'disabled' }}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @elseif($data['strPage']=='Add')
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingMore">
                                                <button class="accordion-button collapsed"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseMore"
                                                    aria-expanded="false"
                                                    aria-controls="collapseMore">
                                                    More Info
                                                </button>
                                            </h2>
                                            <div id="collapseMore"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="headingMore"
                                                data-bs-parent="#userFormAccordion">

                                                <div class="accordion-body">
                                                    <div class="row mb-2">
                                                        <div class="col-md-3 mb-2">
                                                            <label for="secondary_email">Secondary Email</label>
                                                            <input type="text" class="form-control" id="secondary_email" name="secondary_email" value="{{ $data['row']->info->secondary_email ?? old('secondary_email') }}" placeholder="Enter Secondary Email">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="secondary_contact">Secondary Contact</label>
                                                            <input type="text" class="form-control" id="secondary_contact" name="secondary_contact" value="{{ $data['row']->info->secondary_contact ?? old('secondary_contact') }}" placeholder="Enter Secondary Contact">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="aadhaar_no">Aadhaar No</label>
                                                            <input type="text" class="form-control" id="aadhaar_no" name="aadhaar_no" value="{{ $data['row']->info->aadhaar_no ?? old('aadhaar_no') }}" placeholder="Enter Aadhaar No">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="pancard_no">Pancard No</label>
                                                            <input type="text" class="form-control" id="pancard_no" name="pancard_no" value="{{ $data['row']->info->pancard_no ?? old('pancard_no') }}" placeholder="Enter Pancard No">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="president_name">President Name</label>
                                                            <input type="text" class="form-control" id="president_name" name="president_name" value="{{ $data['row']->info->president_name ?? old('president_name') }}" placeholder="Enter President Name">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="president_phone">President Phone</label>
                                                            <input type="text" class="form-control" id="president_phone" name="president_phone" value="{{ $data['row']->info->president_phone ?? old('president_phone') }}" placeholder="Enter President Phone">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="general_secretary_name">General Secretary Name</label>
                                                            <input type="text" class="form-control" id="general_secretary_name" name="general_secretary_name" value="{{ $data['row']->info->general_secretary_name ?? old('general_secretary_name') }}" placeholder="Enter General Secretary Name">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="general_secretary_phone">General Secretary Phone</label>
                                                            <input type="text" class="form-control" id="general_secretary_phone" name="general_secretary_phone" value="{{ $data['row']->info->general_secretary_phone ?? old('general_secretary_phone') }}" placeholder="Enter General Secretary Phone">
                                                        </div>
                                                        <div class="col-md-1 mb-2">
                                                            <div class="form-check mt-4">
                                                                <input class="form-check-input" type="checkbox" id="has_gst" name="has_gst" value="1"
                                                                    {{ isset($data['row']->info) && $data['row']->info->has_gst == 1 ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="has_gst">
                                                                    Has GST
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 mb-2">
                                                            <label for="gst_no" class="form-label">GST No</label>
                                                            <input type="text" class="form-control" id="gst_no" name="gst_no"
                                                                value="{{ $data['row']->info->gst_no ?? old('gst_no') }}"
                                                                placeholder="Enter GST Number"
                                                                {{ isset($data['row']->info) && $data['row']->info->has_gst == 1 ? '' : 'disabled' }}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if($data['edit_param']=='address')
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingAddressEdit">
                                                <button class="accordion-button {{ ($data['edit_param'] ?? '') === 'address' ? '' : 'collapsed' }}"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseAddressEdit"
                                                    aria-expanded="{{ ($data['edit_param'] ?? '') === 'address' ? 'true' : 'false' }}"
                                                    aria-controls="collapseAddressEdit">
                                                    Address
                                                </button>
                                            </h2>
                                            <div id="collapseAddressEdit"
                                                class="accordion-collapse collapse {{ ($data['edit_param'] ?? '') === 'address' ? 'show' : '' }}"
                                                aria-labelledby="headingAddressEdit"
                                                data-bs-parent="#userFormAccordion">

                                                <div class="accordion-body">
                                                    <div class="row mb-2">
                                                        <div class="col-md-12 mb-2">
                                                            <label for="address">Address</label>
                                                            <textarea class="form-control" name="address" id="address" placeholder="Enter Address">{{ $data['row']->address->address ?? old('address') }}</textarea>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="street">Street</label>
                                                            <input type="text" class="form-control" id="street" name="street" value="{{ $data['row']->address->street ?? old('street') }}" placeholder="Enter Street">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="landmark">Landmark</label>
                                                            <input type="text" class="form-control" id="landmark" name="landmark" value="{{ $data['row']->address->landmark ?? old('landmark') }}" placeholder="Enter Landmark">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="city">City Name</label>
                                                            <input type="text" class="form-control" id="city" name="city" value="{{ $data['row']->address->city ?? old('city') }}" placeholder="Enter City Name">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="pincode">Pincode</label>
                                                            <input type="text" class="form-control" id="pincode" name="pincode" value="{{ $data['row']->address->pincode ?? old('pincode') }}" placeholder="Enter Pincode">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @elseif($data['strPage']=='Add')
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingAddress">
                                                <button class="accordion-button collapsed"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseAddress"
                                                    aria-expanded="false"
                                                    aria-controls="collapseAddress">
                                                    Address
                                                </button>
                                            </h2>
                                            <div id="collapseAddress"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="headingAddress"
                                                data-bs-parent="#userFormAccordion">

                                                <div class="accordion-body">
                                                    <div class="row mb-2">
                                                        <div class="col-md-12 mb-2">
                                                            <label for="address">Address</label>
                                                            <textarea class="form-control" name="address" id="address" placeholder="Enter Address">{{ $data['row']->address->address ?? old('address') }}</textarea>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="street">Street</label>
                                                            <input type="text" class="form-control" id="street" name="street" value="{{ $data['row']->address->street ?? old('street') }}" placeholder="Enter Street">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="landmark">Landmark</label>
                                                            <input type="text" class="form-control" id="landmark" name="landmark" value="{{ $data['row']->address->landmark ?? old('landmark') }}" placeholder="Enter Landmark">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="city">City Name</label>
                                                            <input type="text" class="form-control" id="city" name="city" value="{{ $data['row']->address->city ?? old('city') }}" placeholder="Enter City Name">
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label for="pincode">Pincode</label>
                                                            <input type="text" class="form-control" id="pincode" name="pincode" value="{{ $data['row']->address->pincode ?? old('pincode') }}" placeholder="Enter Pincode">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if($data['edit_param']=='bankdetails')
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingBankDetailsEdit">
                                                <button class="accordion-button {{ ($data['edit_param'] ?? '') === 'bankdetails' ? '' : 'collapsed' }}"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseBankDetailsEdit"
                                                    aria-expanded="{{ ($data['edit_param'] ?? '') === 'bankdetails' ? 'true' : 'false' }}"
                                                    aria-controls="collapseBankDetailsEdit">
                                                    Bank Details
                                                </button>
                                            </h2>
                                            <div id="collapseBankDetailsEdit"
                                                class="accordion-collapse collapse {{ ($data['edit_param'] ?? '') === 'bankdetails' ? 'show' : '' }}"
                                                aria-labelledby="headingBankDetailsEdit"
                                                data-bs-parent="#userFormAccordion">

                                                <div class="accordion-body">
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 mb-2">
                                                            <label for="bank_account_name">Bank Aaccount Name</label>
                                                            <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" value="{{ $data['row']->bankdetails->bank_account_name ?? old('bank_account_name') }}" placeholder="Enter Bank Aaccount Name">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="bank_name">Bank Name</label>
                                                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ $data['row']->bankdetails->bank_name ?? old('bank_name') }}" placeholder="Enter Bank Name">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="bank_ifsc">Bank IFSC Code</label>
                                                            <input type="text" class="form-control" id="bank_ifsc" name="bank_ifsc" value="{{ $data['row']->bankdetails->bank_ifsc ?? old('bank_ifsc') }}" placeholder="Enter Bank IFSC Code">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="bank_account_number">Bank Account Number</label>
                                                            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ $data['row']->bankdetails->bank_account_number ?? old('bank_account_number') }}" placeholder="Enter Bank Account Number">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="bank_address">Bank Address</label>
                                                            <input type="text" class="form-control" id="bank_address" name="bank_address" value="{{ $data['row']->bankdetails->bank_address ?? old('bank_address') }}" placeholder="Enter Bank Address">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="upi_id">UPI Id</label>
                                                            <input type="text" class="form-control" id="upi_id" name="upi_id" value="{{ $data['row']->bankdetails->upi_id ?? old('upi_id') }}" placeholder="Enter UPI Id">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @elseif($data['strPage']=='Add')
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingBankDetails">
                                                <button class="accordion-button collapsed"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseBankDetails"
                                                    aria-expanded="false"
                                                    aria-controls="collapseBankDetails">
                                                    Bank Details
                                                </button>
                                            </h2>
                                            <div id="collapseBankDetails"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="headingBankDetails"
                                                data-bs-parent="#userFormAccordion">

                                                <div class="accordion-body">
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 mb-2">
                                                            <label for="bank_account_name">Bank Aaccount Name</label>
                                                            <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" value="{{ $data['row']->bankdetails->bank_account_name ?? old('bank_account_name') }}" placeholder="Enter Bank Aaccount Name">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="bank_name">Bank Name</label>
                                                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ $data['row']->bankdetails->bank_name ?? old('bank_name') }}" placeholder="Enter Bank Name">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="bank_ifsc">Bank IFSC Code</label>
                                                            <input type="text" class="form-control" id="bank_ifsc" name="bank_ifsc" value="{{ $data['row']->bankdetails->bank_ifsc ?? old('bank_ifsc') }}" placeholder="Enter Bank IFSC Code">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="bank_account_number">Bank Account Number</label>
                                                            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ $data['row']->bankdetails->bank_account_number ?? old('bank_account_number') }}" placeholder="Enter Bank Account Number">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="bank_address">Bank Address</label>
                                                            <input type="text" class="form-control" id="bank_address" name="bank_address" value="{{ $data['row']->bankdetails->bank_address ?? old('bank_address') }}" placeholder="Enter Bank Address">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label for="upi_id">UPI Id</label>
                                                            <input type="text" class="form-control" id="upi_id" name="upi_id" value="{{ $data['row']->bankdetails->upi_id ?? old('upi_id') }}" placeholder="Enter UPI Id">
                                                        </div>
                                                    </div>
                                                </div>
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
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
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

        commonAjax.initSelect2('#userRole', 'Select User Role');

        let user_role = <?= $data['row']->user_role ?? '0' ?>;

        commonAjax.loadRoleList(user_role);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        <?php
        if ($data['strPage'] == 'Add') {
        ?>
            if (!validator.selectDropdown('userRole', 'Select User Role')) {
                return false;
            }

            if (!validator.blankCheck('name', 'Name cannot be left blank')) {
                return false;
            }
            if (!validator.maxLength('name', 100, 'Name')) {
                return false;
            }

            if (!validator.blankCheck('organization_name', 'Organization Name cannot be left blank')) {
                return false;
            }
            if (!validator.maxLength('organization_name', 100, 'Organization Name')) {
                return false;
            }

            if (!validator.blankCheck('primary_email', 'Primary Email cannot be left blank')) {
                return false;
            }
            if (!validator.maxLength('primary_email', 100, 'Primary Email')) {
                return false;
            }

            if (!validator.blankCheck('primary_contact', 'Primary Contact cannot be left blank')) {
                return false;
            }
            if (!validator.maxLength('primary_contact', 15, 'Primary Contact')) {
                return false;
            }

            if (!validator.blankCheck('location', 'Location cannot be left blank')) {
                return false;
            }
            if (!validator.maxLength('location', 100, 'Location')) {
                return false;
            }
        <?php
        }
        ?>

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {

        $('#has_gst').on('change', function() {
            if ($(this).is(':checked')) {
                $('#gst_no').prop('disabled', false);
            } else {
                $('#gst_no').prop('disabled', true).val('');
            }
        });

    });
</script>
@endpush