@extends('admin.layouts.master')
@section('page_title', 'Organization Documents')
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
        <li class="breadcrumb-item active">{{ $data['strPage'] }} @yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('organization.index') }}" class="btn btn-success btn-sm">
            View Organization
        </a>
    </div>
</div>

<!-- TABLE -->
@if(isset($data['row']))
<form id="backoffice-form"
    enctype="multipart/form-data"
    method="POST"
    action="{{ route('organization-document.edit', Crypt::encryptString($data['row']->id)) }}">
    @else
    <form id="backoffice-form"
        enctype="multipart/form-data"
        method="POST"
        action="{{ route('organization-document.add') }}">
        @endif

        @csrf
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

                                    <div id="addressContainer" class="border border-dark rounded pt-2 mb-4">

                                        @php
                                        $documents = (!empty($data['documents']) && $data['documents']->count() > 0)
                                        ? $data['documents']
                                        : collect([
                                        (object)[
                                        'document_type' => '',
                                        'document_number' => '',
                                        'original_file_name' => '',
                                        'file_path' => '',
                                        'mime_type' => '',
                                        'file_extension' => '',
                                        'issue_date' => '',
                                        'expiry_date' => '',
                                        ]
                                        ]);
                                        @endphp
                                        <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                                            <strong class="text-white">
                                                <i class="fa-solid fa-file-lines me-2"></i>
                                                Organization Documents
                                            </strong>
                                            <button
                                                type="button"
                                                class="btn btn-sm text-dark fw-bold"
                                                id="btnAddDocument"
                                                style="background:#eebf07;border-color:#eebf07;">
                                                <i class="fa fa-plus"></i> Add Document
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div id="documentContainer">
                                                @foreach($documents as $i => $document)
                                                <div class="document-card border border-dark rounded p-3 mb-3 position-relative">
                                                    @if($i>0)
                                                    <button
                                                        type="button"
                                                        class="btn btn-danger btn-sm btn-remove-document position-absolute"
                                                        style="top:10px;right:10px;z-index:10;">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                    @endif
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label>
                                                                Document Type
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <select
                                                                class="form-select form-select-sm"
                                                                name="document_type[]">

                                                                <option value="">Select Document</option>

                                                                @foreach($data['documentTypes'] as $type)

                                                                <option
                                                                    value="{{ $type->id }}"
                                                                    data-has-expiry="{{ $type->has_expiry }}"
                                                                    {{ old('document_type.'.$i,$document->document_type ?? '') == $type->id ? 'selected' : '' }}>
                                                                    {{ $type->document_name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>
                                                                Document Number
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="document_number[]"
                                                                value="{{ old('document_number.'.$i,$document->document_number ?? '') }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Upload Document</label>
                                                            <input
                                                                type="file"
                                                                class="form-control form-control-sm"
                                                                name="file_name[]">
                                                        </div>

                                                        <div class="expiry-fields">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label>Issue Date</label>
                                                                    <input
                                                                        type="date"
                                                                        class="form-control form-control-sm"
                                                                        name="issue_date[]"
                                                                        value="{{ old('issue_date.'.$i,$document->issue_date ?? '') }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label>Expiry Date</label>
                                                                    <input
                                                                        type="date"
                                                                        class="form-control form-control-sm"
                                                                        name="expiry_date[]"
                                                                        value="{{ old('expiry_date.'.$i,$document->expiry_date ?? '') }}">
                                                                </div>

                                                                <input type="hidden"
                                                                    name="old_file_path[]"
                                                                    value="{{ $document->file_path ?? '' }}">

                                                                <input type="hidden"
                                                                    name="old_file_name[]"
                                                                    value="{{ $document->file_name ?? '' }}">
                                                                <input type="hidden"
                                                                    name="document_id[]"
                                                                    value="{{ $document->id ?? '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
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
                                        <a href="{{ route('states.index') }}" class="btn btn-secondary btn-sm">
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

    <style>
        .expiry-fields {
            display: none;
        }
    </style>

    @endsection
    @push('scripts')

    <script type="module">
        $('#btnReset').click(function() {
            $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
            $('.form-select').val(0);
            $('.form-select').val('').trigger('change');
        });

        // $('#backoffice-form').on('submit', function(e) {

        //     e.preventDefault();
        //     commonAjax.confirmAlert('Are you sure to proceed !');
        //     $('#btnConfirmOk').on('click', function() {
        //         e.currentTarget.submit();
        //     });
        // });
        document.getElementById("menu-toggle").addEventListener("click", function() {
            document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
        });

        // Reset Form
        $('#btnReset').click(function() {
            $('#backoffice-form')[0].reset();
        });
        $(document).on('click', '#btnAddDocument', function() {
            let clone = $('.document-card:first').clone();
            clone.find('input').each(function() {

                if ($(this).attr('type') == 'file') {
                    $(this).val('');
                } else {
                    $(this).val('');
                }

            });

            clone.find('select').val('');
            clone.find('.expiry-fields').hide();
            clone.find('.btn-remove-document').remove();
            clone.prepend(`
                    <button
                        type="button"
                        class="btn btn-danger btn-sm btn-remove-document position-absolute"
                        style="top:5px;right:20px;z-index:10;">
                        <i class="fa fa-trash"></i>
                    </button>
                `);
            $('#documentContainer').append(clone);

        });

        $(document).on('click', '.btn-remove-document', function() {

            if ($('.document-card').length == 1) {
                Swal.fire(
                    'Warning',
                    'At least one document is required.',
                    'warning'
                );
                return;
            }
            $(this).closest('.document-card').remove();

        });


        function toggleExpiryFields(card) {

            let hasExpiry = card.find('select[name="document_type[]"] option:selected')
                .data('has-expiry');

            if (parseInt(hasExpiry) === 1) {

                card.find('.expiry-fields').slideDown();

            } else {

                card.find('.expiry-fields').slideUp();

                card.find('input[name="issue_date[]"]').val('');
                card.find('input[name="expiry_date[]"]').val('');
            }
        }

        $(document).on('change', 'select[name="document_type[]"]', function() {

            toggleExpiryFields($(this).closest('.document-card'));

        });

        $('.document-card').each(function() {

            toggleExpiryFields($(this));

        });


        $(document).on('change', 'input[name="file_name[]"]', function() {

            const file = this.files[0];

            if (!file) return;

            const card = $(this).closest('.document-card');

            card.find('input[name="file_path[]"]').val(file.name);

        });

        $(document).on('change', 'input[name="file_name[]"]', function() {

            const file = this.files[0];

            if (!file) return;

            let card = $(this).closest('.document-card');

            card.find('input[name="file_path[]"]').val(file.name);

        });
    </script>
    @endpush