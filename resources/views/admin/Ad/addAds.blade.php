@extends('admin.layouts.master')
@section('page_title', 'Ads')
@section('content')

<?php
$page_name = 'All ' . trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Ads Managemenmt</li>
        <li class="breadcrumb-item active">{{ $data['strPage'] }} @yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('Ads.index') }}" class="btn btn-success btn-sm">
            View @yield('page_title')
        </a>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form" enctype="multipart/form-data">
    {{csrf_field()}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- FILTER -->
                    <div class="mb-1">
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
                                    <div class="row align-items-stretch">

                                        <!-- LEFT COLUMN -->
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-white h-100">

                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <label for="campaign">
                                                            Campaign<span class="text-danger important">*</span>
                                                        </label>

                                                        <select class="form-select form-select-sm" id="campaign" name="campaign">
                                                            <option value="">Select Campaign</option>
                                                        </select>

                                                        <input type="hidden" id="selectedCampaign" value="{{ $data['row']->campaign_id ?? '' }}">
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <label for="redirectUrl">
                                                            Redirect URL <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text"
                                                            class="form-control clearable form-control-sm"
                                                            id="redirectUrl"
                                                            name="redirectUrl"
                                                            value="{{ $data['row']->redirect_url ?? '' }}"
                                                            placeholder="Enter Redirect URL"
                                                            maxlength="500">
                                                        <small class="text-muted char-counter float-end"></small>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- RIGHT COLUMN -->
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-white h-100">

                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <label for="alt_text">Alt Text</label>
                                                        <input type="text"
                                                            class="form-control clearable form-control-sm"
                                                            id="alt_text"
                                                            name="alt_text"
                                                            value="{{ $data['row']->alt_text ?? '' }}"
                                                            placeholder="Enter Image Alt Text">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <label for="ad_image">Ad Image</label>
                                                        <input type="file"
                                                            class="form-control clearable form-control-sm"
                                                            id="adImageInput"
                                                            name="ad_image"
                                                            accept="image/*">

                                                        <small class="text-muted text-md-end mt-2">
                                                            Allowed: JPG, JPEG, PNG | Max: 2MB | Size: 1600×500px
                                                        </small>
                                                    </div>
                                                </div>

                                                <div id="previewContainer" class="{{ empty($data['row']->image ?? '') ? 'd-none' : '' }}">

                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <img id="adPreview"
                                                                src="{{ !empty($data['row']->image) ? asset('storage/uploads/Ad/Ads/'.$data['row']->image) : '#' }}"
                                                                alt="Preview"
                                                                class="img-fluid border p-1 {{ empty($data['row']->image) ? 'd-none' : '' }}">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12 mb-1">

                                                            @if($data['strPage']=='Add')

                                                            <button type="button"
                                                                id="removeImageBtn"
                                                                class="btn btn-danger btn-sm">
                                                                Remove Image
                                                            </button>

                                                            @else

                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-image"
                                                                data-id="{{ $data['row']->id }}"
                                                                data-table="ads "
                                                                data-column="image"
                                                                data-path="uploads/Ad/Ads"
                                                                data-container="previewContainer">
                                                                Remove Image
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

                            <!-- BUTTONS -->
                            <div class="row mt-4">
                                <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                    <button class="btn btn-primary btn-sm" type="submit">
                                        {{ $data['strSubmit'] }}
                                    </button>
                                    @if($data['strReset'] == 'Cancel')
                                    <a href="{{ route('Ads.index') }}" class="btn btn-secondary btn-sm">
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
    document.addEventListener('DOMContentLoaded', function() {

        const input = document.getElementById('adImageInput');
        const preview = document.getElementById('adPreview');
        const previewContainer = document.getElementById('previewContainer');
        const removeBtn = document.getElementById('removeImageBtn');

        if (!input) return;

        input.addEventListener('change', function(event) {

            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                commonAjax.viewAlert("File size must be less than 2MB", 'adImageInput');
                input.value = '';
                previewContainer.classList.add('d-none');
                return;
            }

            const allowedExtensions = ['jpg', 'jpeg', 'png'];
            const allowedMimeTypes = ['image/jpeg', 'image/png'];

            const ext = file.name.split('.').pop().toLowerCase();

            if (!allowedExtensions.includes(ext)) {
                commonAjax.viewAlert("Only JPG, JPEG, PNG images are allowed.", 'adImageInput');
                input.value = '';
                return;
            }

            if (!allowedMimeTypes.includes(file.type)) {
                commonAjax.viewAlert("Invalid image format.", 'adImageInput');
                input.value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('d-none');
            };

            reader.readAsDataURL(file);

        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                input.value = '';
                preview.src = '#';
                previewContainer.classList.add('d-none');
            });
        }

    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $(document).ready(function() {

        commonAjax.initClearableInputs();
        commonAjax.initSelect2('#campaign', 'Select Campaign');

        commonAjax.loadCampaignList("{{ $data['row']->campaign_id ?? 0 }}");

    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        let adImage = $('#adImageInput')[0].files.length ?? 0;

        if (!validator.blankCheck('campaign', 'Campaign must be selected')) {
            return false;
        }

        if (!validator.blankCheck('alt_text', 'Alternate Text cannot be left blank')) {
            return false;
        }

        if (!validator.maxLength('alt_text', 50, 'Alternate Text')) {
            return false;
        }

        if (!validator.blankCheck('redirectUrl', 'Redirect URL cannot be left blank')) {
            return false;
        }

        if (!validator.maxLength('redirectUrl', 500, 'Redirect URL')) {
            return false;
        }

        if (!adImage && "{{ $data['strPage'] }}" === "Add") {
            commonAjax.viewAlert('Ad Image must be uploaded');
            $('#adImageInput').focus();
            return false;
        }

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').off('click').on('click', function() {
            e.currentTarget.submit();
        });

    });
</script>
@endpush