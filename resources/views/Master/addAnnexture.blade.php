@extends('admin.layouts.master')
@section('page_title', 'Annexture')
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
        <a href="{{ route('annexture.index') }}" class="btn btn-success btn-sm">
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
                                        <div class="col-md-4 mb-3">
                                            <label for="selAnnexureType">Annexture Type<span class="text-danger important">*</span></label>
                                            <select class="form-select selAnnexureType" id="selAnnexureType" name="selAnnexureType">
                                                <option value="0">Select Annexure Type</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="boardingDroppingWrapper">
                                        <div class="row mb-3">

                                            <div class="col-md-4 mb-3">
                                                <label for="annexture_name">Annexture Name<span class="text-danger important">*</span></label>
                                                <input type="text" class="form-control annexture_name" data-check-url="{{ route('annexture.checkExists') }}"
                                                    placeholder="Enter Annexture Name" id="annexture_name" name="annexture_name[]" maxlength="100" value="{{ $data['row']->annexture_name ?? '' }}">
                                                <small class="text-muted char-counter float-end"></small>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="annexture_value">Annexture Value<span class="text-danger important">*</span></label>
                                                <input type="text" class="form-control" placeholder="Enter Annexture Value" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="annexture_value" name="annexture_value[]" maxlength="3" value="{{ $data['row']->annexture_value ?? '' }}">
                                                <small class="text-muted char-counter float-end"></small>
                                            </div>
                                            <?php $isEdit = isset($data['row']->id) ? 'd-none' : ''; ?>
                                            <div class="col-md-1 d-flex align-items-center mb-3 <?= $isEdit ?>">
                                                <button type="button" class="btn btn-outline-primary btn-add">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>

                                        </div>
                                    </div>

                                </div>




                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="row mb-3">
                                            <div class="col-md-8"> <!-- 👈 makes it smaller horizontally -->
                                                <div class="card shadow-sm border-0 d-none" id="annexturePreviewCard">

                                                    <div class="card-header bg-primary text-white py-2 px-3">
                                                        <strong>Existing Annexture Data</strong>
                                                    </div>

                                                    <div class="card-body p-2">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-striped table-hover align-middle mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th style="width: 50px;">#</th>
                                                                        <th>Annexture Name</th>
                                                                        <th style="width: 120px;">Value</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="annexturePreviewBody">
                                                                    <!-- Dynamic -->
                                                                </tbody>
                                                            </table>
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
                                        <a href="{{ route('annexture.index') }}" class="btn btn-secondary btn-sm">
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

        commonAjax.initCharCounter(['annexture_name', 'annexture_value']);
        commonAjax.initSelect2('.selAnnexureType', 'Select Annexure Type');

        let annexure_type_id = <?= $data['row']->annexture_type_id ?? '0' ?>;

        commonAjax.loadAnnexureTypeList(annexure_type_id);
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        //City validation
        if (!validator.selectDropdown('selAnnexureType', 'Select Annexure Type')) {
            return false;
        }

        let isValid = true;

        $('#boardingDroppingWrapper .row').each(function(index) {

            let point = $(this).find('input[name="annexture_name[]"]');


            if ($.trim(point.val()) === '') {
                commonAjax.confirmAlert(
                    'Annexture Name cannot be blank in row ' + (index + 1)
                );
                point.focus();
                isValid = false;
                return false;
            }
        });

        if (!isValid) return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').off('click').on('click', function() {
            e.currentTarget.submit();
        });

    });

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });

    var rowCount = 1;

    $(document).ready(function() {

        $(document).on('click', '.btn-add', function(e) {

            e.preventDefault();

            const testVal = $('#selAnnexureType').val();

            if (!testVal || testVal == 0) {
                commonAjax.confirmAlert('Please Select Annexure Type');
                $('#selAnnexureType').focus();
                return false;
            }

            rowCount++;

            let newRow =
                "<div id='bd_row" + rowCount + "' class='boarding-dropping-item'>" +

                "<div class='row mb-3'>" +

                "<div class='col-md-4 mb-3'>" +
                "<label>Annexture Name <span class='text-danger important'>*</span></label>" +
                "<input type='text' maxlength='100' " +
                "placeholder='Enter Annexture Name' " +
                "class='form-control annexture_name' " +
                "data-check-url='{{ route('annexture.checkExists') }}' " +
                "name='annexture_name[]'>" +
                "<small class='text-muted char-counter float-end'></small>" +
                "</div>" +

                "<div class='col-md-4 mb-3'>" +
                "<label>Annexture Value</label>" +
                "<input type='text' maxlength='3' " +
                "class='form-control annexture_value' " +
                "placeholder='Enter Annexture Value' " +
                "oninput=\"this.value = this.value.replace(/[^0-9]/g, '')\" " +
                "name='annexture_value[]'>" +
                "<small class='text-muted char-counter float-end'></small>" +
                "</div>" +

                "<div class='col-md-2 d-flex align-items-center mb-3'>" +
                "<button type='button' class='btn btn-outline-danger btn-remove' data-id='bd_row" + rowCount + "'>" +
                "<i class='fa fa-trash'></i>" +
                "</button>" +
                "</div>" +

                "</div>" +
                "<hr>" +
                "</div>";

            $("#boardingDroppingWrapper").append(newRow);

            commonAjax.initCharCounter([
                '.annexture_name',
                '.annexture_value',
            ]);
        });
    });



    /* remove row */
    $(document).on('click', '.btn-remove', function() {
        let id = $(this).data('id');
        $('#' + id).remove();
    });

    $(document).on('blur', '.annexture_name', function() {

        let $input = $(this);

        let point = $.trim($input.val());
        if (point === '') return;

        let annexureTypeId = $('#selAnnexureType').val();

        if (!annexureTypeId) return;
        $.ajax({
            url: $input.data('check-url'),
            type: 'POST',
            data: {
                annexure_type_id: annexureTypeId,
                type: type,
                point: point,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {

                let html = '';

                if (res.data.length > 0) {

                    res.data.forEach((item, index) => {
                        html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.annexture_name}</td>
                    <td>${item.annexture_value}</td>
                </tr>
            `;
                    });


                    $('#annexturePreviewBody').html(html);
                    $('#annexturePreviewCard').removeClass('d-none');

                } else {

                    $('#annexturePreviewCard').addClass('d-none');
                    $('#annexturePreviewBody').html('');
                }
            }
        });
    });








    $(document).on('change', '#selAnnexureType', function() {

        let typeId = $(this).val();

        if (!typeId || typeId == 0) {
            $('#annexturePreviewCard').addClass('d-none');
            return;
        }

        $.ajax({
            url: "{{ route('annexture.getByType') }}",
            type: "GET",
            data: {
                annexture_type_id: typeId
            },
            success: function(res) {

                let html = '';

                if (res.data.length > 0) {

                    res.data.forEach((item, index) => {
                        html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.annexture_name}</td>
                    <td>${item.annexture_value}</td>
                </tr>
            `;
                    });

                    $('#annexturePreviewBody').html(html);
                    $('#annexturePreviewCard').removeClass('d-none');

                } else {

                    // ❌ HIDE completely when no data
                    $('#annexturePreviewCard').addClass('d-none');
                    $('#annexturePreviewBody').html('');
                }
            }
        });
    });
</script>
@endpush