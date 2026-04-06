@extends('admin.layouts.master')
@section('page_title', 'Ticket Fare Slab Info')
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

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <a href="{{ route('ticketfareslab-info.index') }}" class="btn btn-success btn-sm">
            View @yield('page_title')
        </a>
    </div>
</div>

<form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form">
    {{csrf_field()}}

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="mb-3">
                        <div class="card-body">
                            <div class="row">

                                <!-- Alerts -->
                                @if (session('message'))
                                <div class="alert alert-{{ session('level') ?? 'success' }} alert-dismissible fade show">
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                @endif

                                @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                @endif

                                <!-- Slab -->
                                <div class="col-12">
                                    <div class="row mb-1">
                                        <div class="col-md-5">
                                            <label>Ticket Fare Slab *</label>
                                            <select class="form-select form-select-sm" id="slab" name="slab_id"></select>
                                        </div>
                                    </div>

                                    <!-- Operator -->
                                    <div class="row mb-1">
                                        <div class="col-md-5">
                                            <label>Bus Operator *</label>
                                            <select class="form-select form-select-sm" id="operator"></select>

                                            <div id="selectedOperatorsWrapper" class="mt-2" style="display:none;">
                                                <div id="selectedOperators"></div>
                                            </div>

                                            <input type="hidden" name="bus_operator_id" id="operator_ids">
                                        </div>
                                    </div>
                                </div>

                                <!-- Slab Rows -->
                                <div id="slabWrapper">
                                    <div class="row mb-3 mt-3">
                                        <div class="col-md-2"> <label for="starting_fare">"From Fare<span class="text-danger important">*</span></label>
                                            <input type="number" name="starting_fare[]" placeholder="From Fare" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-2"> <label for="upto_fare">To Fare<span class="text-danger important">*</span></label>
                                            <input type="number" name="upto_fare[]" placeholder="To Fare" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-2"> <label for="commision">Commission<span class="text-danger important">*</span></label>
                                            <input type="number" name="commision[]" placeholder="Commission" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-2"> <label for="from_date">From Date<span class="text-danger important">*</span></label>
                                            <input type="date" name="from_date[]" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-2"> <label for="to_date">To Date<span class="text-danger important">*</span></label>
                                            <input type="date" name="to_date[]" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center mt-4">
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-add">+</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tables -->
                                <div id="operatorTables" class=""></div>

                            </div>

                            <!-- Buttons -->
                            <div class="row mt-4">
                                <div class="col-12 d-flex gap-2">
                                    <button class="btn btn-primary btn-sm" type="submit">
                                        {{ $data['strSubmit'] }}
                                    </button>
                                    <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                        {{ $data['strReset'] }}
                                    </button>
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
    .selected-tag {
        display: inline-flex;
        align-items: center;
        background: #ffc107;
        padding: 5px 10px;
        border-radius: 20px;
        margin: 3px
    }

    .selected-tag .remove {
        margin-left: 6px;
        cursor: pointer
    }
</style>

@endsection

@push('scripts')
<script type="module">
    let selectedOperators = [];

    $(document).ready(function() {

        commonAjax.initSelect2('#slab', 'Select Ticket Fare Slab');
        commonAjax.initSelect2('#operator', 'Select Bus Operator');

        commonAjax.loadTicketFareSlabList('#slab');
        commonAjax.loadBusOperatorList();

        $('#operator').on('change', function() {

            let id = $(this).val();
            let text = $("#operator option:selected").text();

            if (!id) return;
            if (selectedOperators.some(op => op.id == id)) return;

            let operator = {
                id,
                text
            };
            selectedOperators.push(operator);

            renderOperators();
            loadOperatorTable(operator);

            $(this).val('').trigger('change');
        });
    });

    function renderOperators() {

        let html = '';

        selectedOperators.forEach((op, index) => {
            html += `<span class="selected-tag" data-index="${index}">${op.text}<span class="remove">×</span></span>`;
        });

        $('#selectedOperators').html(html);
        $('#operator_ids').val(selectedOperators.map(op => op.id).join(','));

        $('#selectedOperatorsWrapper').toggle(selectedOperators.length > 0);
    }

    $(document).on('click', '.remove', function() {

        let index = $(this).closest('.selected-tag').data('index');
        let operator = selectedOperators[index];

        selectedOperators.splice(index, 1);
        $(`#table_${operator.id}`).remove();

        renderOperators();
    });

    $('#btnReset').click(function() {

        $('#backoffice-form')[0].reset();
        $('.form-select').val('').trigger('change');

        selectedOperators = [];
        renderOperators();
        $('#operatorTables').html('');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        if (!validator.selectDropdown('slab', 'Select Ticket Fare Slab')) return;

        if (selectedOperators.length === 0) {
            commonAjax.viewAlert('Please select at least one bus operator');
            return;
        }

        commonAjax.confirmAlert('Are you sure to proceed!');

        $('#btnConfirmOk').one('click', () => this.submit());
    });

    // add/remove rows
    $(document).on('click', '.btn-add', function() {
        $('#slabWrapper').append(`
        <div class="row mb-3 dynamic-item">
            <div class="col-md-2"><input type="number" name="starting_fare[]" placeholder="From Fare" class="form-control form-control-sm"></div>
            <div class="col-md-2"><input type="number" name="upto_fare[]" placeholder="To Fare" class="form-control form-control-sm"></div>
            <div class="col-md-2"><input type="number" name="commision[]" placeholder="Commission" class="form-control form-control-sm"></div>
            <div class="col-md-2"><input type="date" name="from_date[]" class="form-control form-control-sm"></div>
            <div class="col-md-2"><input type="date" name="to_date[]" class="form-control form-control-sm"></div>
            <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm btn-remove mt-1">-</button></div>
        </div>
    `);
    });

    $(document).on('click', '.btn-remove', function() {
        $(this).closest('.dynamic-item').remove();
    });

    // UPDATED: no table if no data
    function loadOperatorTable(operator) {

        $.ajax({
            url: "/admin/get-operator-slab-data",
            type: "POST",
            data: {
                operator_id: operator.id,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },

            success: function(res) {

                // skip if no data
                if (!res.status || res.data.length === 0) {
                    $(`#table_${operator.id}`).remove();
                    return;
                }

                let tableHtml = `
                <div class="card mt-3 operator-table" id="table_${operator.id}">
                    <div class="card-header bg-warning">
                        <b>${operator.text}</b>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Slab</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Commission</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                </tr>
                            </thead>
                            <tbody>`;

                res.data.forEach(row => {
                    tableHtml += `
                    <tr>
                        <td>${row.slab_name}</td>
                        <td>${row.starting_fare}</td>
                        <td>${row.upto_fare}</td>
                        <td>${row.commision}%</td>
                        <td>${row.from_date}</td>
                        <td>${row.to_date}</td>
                    </tr>`;
                });

                tableHtml += `
                            </tbody>
                        </table>
                    </div>
                </div>`;

                $(`#table_${operator.id}`).remove();
                $('#operatorTables').append(tableHtml);
            }
        });
    }
</script>
@endpush