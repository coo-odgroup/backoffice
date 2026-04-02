@extends('admin.layouts.master')
@section('page_title', 'Add FAQ')
@section('content')

<?php
$page_name = 'All FAQ';
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">{{ $data['strPage'] }} FAQ</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('faq.index') }}" class="btn btn-success btn-sm">
            View FAQ
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
                                    <div class="row">
                                        <div class="col-md-3 mb-1">
                                            <label for="faqCategory">FAQ Category<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm" id="faqCategory" name="faqCategory">
                                                <option value="0">Select FAQ Category</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb-3=1">
                                            <label for="faq_name">Title<span class="text-danger important">*</span></label>
                                            <input type="text" class="form-control form-control-sm clearable" id="faq_name" name="faq_name" value="{{ $data['row']->title ?? '' }}" placeholder="Enter Title" maxlength="100">
                                            <small class="text-muted char-counter float-end"></small>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="description">Content</label>
                                            <textarea
                                                class="form-control form-control-sm clearable"
                                                id="content"
                                                name="content">{{ htmlDecode($data['row']->content ?? '') }}</textarea>
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
                                        <a href="{{ route('faq.index') }}" class="btn btn-secondary btn-sm">
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
        initCkEditor('#content');
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $(document).ready(function() {

        commonAjax.initClearableInputs();
        commonAjax.initCharCounter(['faq_name']);
        commonAjax.initSelect2('#faqCategory', 'Select FAQ Category');

        let category_id = <?= $data['row']->faq_category_id ?? '0' ?>;

        commonAjax.loadFaqCategory(category_id);

    });
    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.selectDropdown('faqCategory', 'FAQ Category'))
            return false;

        if (!validator.blankCheck('faq_name', 'Amenity Name cannot be left blank'))
            return false;
        if (!validator.maxLength('faq_name', 100, 'Amenity Name'))
            return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {

            if (window.ckEditors && window.ckEditors['#content']) {
                $('#content').val(
                    window.ckEditors['#content'].getData()
                );
            }

            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );

        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush