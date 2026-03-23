@extends('admin.layouts.master')
@section('page_title', 'Cancellation Slab Info')
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
        <a href="{{ route('cancellationslab-info.index') }}" class="btn btn-success btn-sm">
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
                                        <div class="col-md-6 mb-3">
                                            <label for="slab">Cancellation Slab<span class="text-danger important">*</span></label>
                                            <select class="form-select form-select-sm" id="slab" name="slab_id">
                                            </select>
                                        </div>
                                    </div>
                                    <div id="slabWrapper">

                                        @if($data['strPage']=="Edit")
                                        @if(!empty($data['row']->slabInfo))

                                        @foreach($data['row']->slabInfo as $index => $row)
                                        <div class="row mb-3 {{ $index == 0 ? '' : 'dynamic-item' }}">

                                            <div class="col-md-5 mb-3">
                                                <label for="duration">
                                                    Duration<span class="text-danger important">*</span>
                                                </label>
                                                <input type="text" class="form-control form-select-sm"
                                                    name="duration[]"
                                                    value="{{ $row->duration }}"
                                                    placeholder="Enter Duration">
                                            </div>

                                            <div class="col-md-5 mb-3">
                                                <label for="deduction">
                                                    Deduction %<span class="text-danger important">*</span>
                                                </label>
                                                <input type="text" class="form-control form-select-sm"
                                                    name="deduction[]"
                                                    value="{{ $row->deduction }}"
                                                    placeholder="Enter Deduction %">
                                            </div>

                                            <div class="col-md-2 d-flex align-items-center mb-3 mt-3">
                                                @if($index == 0)
                                                <button type="button" class="btn btn-outline-primary btn-sm btn-add">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                @else
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-remove">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                                @endif
                                            </div>

                                        </div>
                                        @endforeach

                                        @else
                                        {{-- Empty edit case --}}
                                        <div class="row mb-3">

                                            <div class="col-md-5 mb-3">
                                                <label for="duration">Duration<span class="text-danger important">*</span></label>
                                                <input type="text" class="form-control form-select-sm"
                                                    name="duration[]"
                                                    placeholder="Enter Duration">
                                            </div>

                                            <div class="col-md-5 mb-3">
                                                <label for="deduction">Deduction %<span class="text-danger important">*</span></label>
                                                <input type="text" class="form-control form-select-sm"
                                                    name="deduction[]"
                                                    placeholder="Enter Deduction %">
                                            </div>

                                            <div class="col-md-2 d-flex align-items-center mb-3 mt-3">
                                                <button type="button" class="btn btn-outline-primary btn-sm btn-add">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>

                                        </div>
                                        @endif

                                        @else
                                        {{-- Add mode --}}
                                        <div class="row mb-3">

                                            <div class="col-md-5 mb-3">
                                                <label for="duration">Duration<span class="text-danger important">*</span></label>
                                                <input type="text" class="form-control form-select-sm"
                                                    name="duration[]"
                                                    placeholder="Enter Duration">
                                            </div>

                                            <div class="col-md-5 mb-3">
                                                <label for="deduction">Deduction %<span class="text-danger important">*</span></label>
                                                <input type="text" class="form-control form-select-sm"
                                                    name="deduction[]"
                                                    placeholder="Enter Deduction %">
                                            </div>

                                            <div class="col-md-2 d-flex align-items-center mb-3 mt-3">
                                                <button type="button" class="btn btn-outline-primary btn-sm btn-add">
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
                                        <a href="{{ route('cancellationslab-info.index') }}" class="btn btn-secondary btn-sm">
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

        // Validate dropdown
        if (!validator.selectDropdown('slab', 'Select Cancellation Slab')) {
            return false;
        }

        let isValid = true;

        const durations = document.querySelectorAll('[name="duration[]"]');
        const deductions = document.querySelectorAll('[name="deduction[]"]');

        durations.forEach((input, index) => {
            if (input.value.trim() === '') {
                commonAjax.viewAlert(`Duration is required in row ${index + 1}`);
                input.focus();
                isValid = false;
                return false;
            }
        });

        if (!isValid) return false;

        deductions.forEach((input, index) => {
            if (input.value.trim() === '') {
                commonAjax.viewAlert(`Deduction is required in row ${index + 1}`);
                input.focus();
                isValid = false;
                return false;
            }
        });

        if (!isValid) return false;

        // Confirm popup
        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').off('click').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    $(document).ready(function() {

        commonAjax.initSelect2('#slab', 'Select Cancellation Slab');

        let slab_id = "{{ $data['row']->slab_id ?? '' }}";

        commonAjax.loadCancellationslabList(slab_id);

    });

    document.addEventListener('DOMContentLoaded', function() {

        const container = document.getElementById('slabWrapper');

        container.addEventListener('click', function(e) {

            // ADD ROW
            if (e.target.closest('.btn-add')) {

                const newRow = document.createElement('div');
                newRow.className = 'row mb-3 dynamic-item';

                newRow.innerHTML = `
                <div class="col-md-5 mb-3">
                    <label>Duration<span class="text-danger important">*</span></label>
                    <input type="text" class="form-control form-select-sm" name="duration[]" placeholder="Enter Duration">
                </div>

                <div class="col-md-5 mb-3">
                    <label>Deduction %<span class="text-danger important">*</span></label>
                    <input type="text" class="form-control form-select-sm" name="deduction[]" placeholder="Enter Deduction %">
                </div>

                <div class="col-md-2 d-flex align-items-center mb-3 mt-3">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            `;

                container.appendChild(newRow);
            }

            // REMOVE ROW
            if (e.target.closest('.btn-remove')) {

                const rows = container.querySelectorAll('.dynamic-item');

                if (rows.length > 0) {
                    e.target.closest('.dynamic-item').remove();
                }
            }

        });

    });
</script>
@endpush