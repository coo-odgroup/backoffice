@extends('admin.layouts.master')
@section('content')

<?php
$page_name = 'All Roles';
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">{{ $data['strPage'] }} Modules</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">Modules</h5>
    <div>
        <a href="{{ route('modules.index') }}" class="btn btn-success btn-sm">
            View Modules
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
                                        <div class="col-md-6">
                                            <label for="selParent">Parent Module</label>
                                            <select class="form-select form-select-sm selParent" id="selParent" name="selParent">
                                                <option value="0">Select Parent Module</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="ModulesWrapper">
                                        <div class="row mb-3">
                                            <div class="col-md-4 mb-1">
                                                <label for="moduleCode">Module Code<span class="text-danger important">*</span></label>
                                                <input type="text" class="form-control form-control-sm moduleCode" placeholder="Enter Module Code" id="moduleCode" name="moduleCode[]" value="{{ $data['row']->code ?? '' }}">
                                            </div>
                                            <div class="col-md-4 mb-1">
                                                <label for="moduleName">Module Name<span class="text-danger important">*</span></label>
                                                <input type="text" class="form-control form-control-sm moduleName" placeholder="Enter Module Name" id="moduleName" name="moduleName[]" value="{{ $data['row']->name ?? '' }}">
                                            </div>
                                            <div class="col-md-3 mb-1">
                                                <label for="sequence_no">Sequence No</label>
                                                <input type="text" class="form-control form-control-sm" id="sequence_no" placeholder="Enter Sequence No" name="sequence_no[]" value="{{ $data['row']->sequence_no ?? '1' }}">
                                            </div>
                                            <?php $isEdit = isset($data['row']->id) ? 'd-none' : ''; ?>
                                            <div class="col-md-1 d-flex align-items-end mb-1 <?= $isEdit ?>">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-add">
                                                    <i class="fa fa-plus"></i>
                                                </button>
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
                                        <a href="{{ route('modules.index') }}" class="btn btn-secondary btn-sm">
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

        commonAjax.initSelect2('.selParent', 'Select Parent Module');

        let parent_id = <?= $data['row']->parent_id ?? '0' ?>;

        commonAjax.loadParentList(parent_id);
    });


    
    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        let isValid = true;

        /* Loop through each module row */
        $('#ModulesWrapper .row').each(function(index) {

            let moduleCode = $(this).find('input[name="moduleCode[]"]');
            let moduleName = $(this).find('input[name="moduleName[]"]');

            /* Module Code validation */
            if ($.trim(moduleCode.val()) === '') {
                commonAjax.confirmAlert(
                    'Module Code cannot be blank in row ' + (index + 1)
                );
                moduleCode.focus();
                isValid = false;
                return false;
            }

            if (moduleCode.val().length > 100) {
                commonAjax.confirmAlert(
                    'Module Code must not exceed 100 characters in row ' + (index + 1)
                );
                moduleCode.focus();
                isValid = false;
                return false;
            }

            if ($.trim(moduleName.val()) === '') {
                commonAjax.confirmAlert(
                    'Module Name cannot be blank in row ' + (index + 1)
                );
                moduleName.focus();
                isValid = false;
                return false;
            }

            if (moduleName.val().length > 100) {
                commonAjax.confirmAlert(
                    'Module Name must not exceed 100 characters in row ' + (index + 1)
                );
                moduleName.focus();
                isValid = false;
                return false;
            }
        });

        if (!isValid) return false;

        /* Final confirmation */
        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').off('click').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    // $(document).ready(function () {

    // $('#moduleCode').on('keyup', function() {

    //     let val = $(this).val();
    //     val = val.toUpperCase();
    //     val = val.replace(/\s+/g, '_');
    //     val = val.replace(/[0-9]/g, '');
    //     val = val.replace(/[^A-Z_]/g, '');

    //     $(this).val(val);
    // });

    // });<script>
    let moduleRowCount = 1;

    $(document).ready(function() {

      $('.moduleCode').on('keyup', function() {

        let val = $(this).val();
        val = val.toUpperCase();
        val = val.replace(/\s+/g, '_');
        val = val.replace(/[0-9]/g, '');
        val = val.replace(/[^A-Z_]/g, '');

        $(this).val(val);
    });




        /* ADD MODULE ROW */
        $(document).on('click', '.btn-add', function(e) {

            const parentVal = $('#selParent').val();

            if (!parentVal || parentVal === '0') {
                commonAjax.confirmAlert('Please Select Parent Module');
                $('#selParent').focus();
                e.preventDefault();
                return false;
            }

            moduleRowCount++;

            $("#ModulesWrapper").append(
                "<div id='module_row" + moduleRowCount + "' class='module-item'>" +

                "<div class='row mb-3'>" +

                "<div class='col-md-4 mb-1'>" +
                "<label>Module Code<span class='text-danger important'>*</span></label>" +
                "<input type='text' class='form-control form-control-sm moduleCode' " +
                "placeholder='Enter Module Code' id='moduleCode" + moduleRowCount + "' name='moduleCode[]'>" +
                "</div>" +

                "<div class='col-md-4 mb-1'>" +
                "<label>Module Name<span class='text-danger important'>*</span></label>" +
                "<input type='text' class='form-control form-control-sm moduleName' " +
                "placeholder='Enter Module Name' name='moduleName[]'>" +
                "</div>" +

                "<div class='col-md-3 mb-1'>" +
                "<label>Sequence No</label>" +
                "<input type='text' class='form-control form-control-sm' " +
                "name='sequence_no[]' value='" + moduleRowCount + "'>" +
                "</div>" +

                "<div class='col-md-1 d-flex align-items-end mb-1'>" +
                "<button type='button' class='btn btn-outline-danger btn-remove btn-sm' " +
                "data-id='module_row" + moduleRowCount + "'>" +
                "<i class='fa fa-trash'></i>" +
                "</button>" +
                "</div>" +

                "</div>" +
                "<hr>" +
                "</div>"
            );
            $('.moduleCode').on('keyup', function() {

        let val = $(this).val();
        val = val.toUpperCase();
        val = val.replace(/\s+/g, '_');
        val = val.replace(/[0-9]/g, '');
        val = val.replace(/[^A-Z_]/g, '');

        $(this).val(val);
    });
        });

        /* REMOVE MODULE ROW */
        $(document).on('click', '.btn-remove', function() {
            let id = $(this).data('id');
            $('#' + id).remove();
        });

    });
</script>
@endpush
