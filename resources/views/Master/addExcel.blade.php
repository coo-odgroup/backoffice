@extends('admin.layouts.master')
@section('page_title', 'Excel Import')
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
        <a href="{{ route('excel.index') }}" class="btn btn-success btn-sm">
            View @yield('page_title')
        </a>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form"
    method="POST"
    action="{{ route('excel.add') }}"
    enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <label>Excel File <span class="text-danger">*</span></label>

                    <input type="file"
                        class="form-control form-control-sm"
                        name="excel_file"
                        id="excel_file"
                        accept=".xlsx,.xls,.csv">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary btn-sm">
                        Import Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection
@push('scripts')

<script type="module">

$('#backoffice-form').on('submit', function(e){

    e.preventDefault();

    if($('#excel_file').val()==''){

        Swal.fire(
            'Warning',
            'Please select an Excel file.',
            'warning'
        );

        return;
    }

    let form=this;

    commonAjax.confirmAlert('Import this Excel file?');

    $('#btnConfirmOk')
        .off('click')
        .on('click',function(){

            form.submit();

        });

});

</script>
@endpush