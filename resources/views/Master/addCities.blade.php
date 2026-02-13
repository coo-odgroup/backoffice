@extends('admin.layouts.master')
@section('content')

<?php
$page_name = 'All Cities';
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>


<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Data Tables</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">Cities</h5>
    <div>
        <button class="btn btn-success btn-sm">Viw Cities
        </button>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- FILTER -->
                    <div class="mb-3 border-bottom">
                        <div class="card-body">
                            <div class="row">
                                <!-- FILTER FIELDS -->
                                <div class="col-12">
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="txtCity">City Name</label>
                                            <input type="text" class="form-control" id="txtCity" name="txtCity">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="txtAlias">Alias</label>
                                            <input type="text" class="form-control" id="txtAlias" name="txtAlias">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="selState">State</label>
                                            <select class="form-select" id="selState" name="selState">
                                                <option value="0">Select State</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="selDistrict">District</label>
                                            <select class="form-select" id="selDistrict" name="selDistrict">
                                                <option value="0">Select District</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-center">
                                        <!-- Label -->
                                        <div class="col-md-1 text-end">
                                            <label for="txtSynonym" class="mb-0">Synonyms</label>
                                        </div>

                                        <!-- Input -->
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" id="txtSynonym" name="txtSynonym">
                                        </div>

                                        <!-- Plus Button -->
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-primary">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center">
                                        <!-- Label -->
                                        <div class="col-md-1 text-end">
                                            <label for="txtSynonym" class="mb-0">Synonyms</label>
                                        </div>

                                        <!-- Input -->
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" id="txtSynonym" name="txtSynonym">
                                        </div>

                                        <!-- Plus Button -->
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>


                                </div>

                                <!-- BUTTONS -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                        <button class="btn btn-primary btn-sm" type="button" onclick="return getDataTableView()">
                                            Submit
                                        </button>
                                        <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                            Reset
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Table start -->
                    <div id="tableActions">
                    </div>

                    <div class="footer-background border-success text-center" id="norecord" style="display:none">No record found.</div>
                    {{csrf_field()}}
                    <input name="hdn_ids" id="hdn_ids" type="hidden">
                    <input name="hdn_qs" id="hdn_qs" type="hidden">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div id="customTableInfo"></div>
                        <div id="customPagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>

@endsection
@push('scripts')

<script>
    $('#backoffice-form').on('submit', function(e) {
        e.preventDefault();
    });


    $(document).ready(function() {



        initSelect2('#selState', 'Select State');
        initSelect2('#selDistrict', 'Select District');
        // By default hide filter
        $("#filterBox").hide();

        // Toggle on button click
        window.toggleFilter = function() {
            $("#filterBox").slideToggle(300);
        };
        loadStateList();
        getDataTableView();
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');

        getDataTableView();
    });

    $(document).on('change', '#selState', function() {
        let state_id = $(this).val();
        getDistrictList(state_id);
    });


    function toggleFilter() {
        console.log("toggleFilter called");
        document.getElementById("filterBox").classList.toggle("d-none");
    }

    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
    });



    function getDataTableView() {

        $('#pageSizeDatatable').val(10);
        txtSearch = '';
        selStatus = '';
        selState = 0;
        selDistrict = 0;

        if ($('#txtSearch').val() != '') {
            txtSearch = $('#txtSearch').val();
        }
        if ($('#selStatus').val() != '') {
            selStatus = $('#selStatus').val();
        }
        if ($('#selState').val() != 0) {
            selState = $('#selState').val();
        }
        if ($('#selDistrict').val() != 0) {
            selDistrict = $('#selDistrict').val();
        }

        let tableId = 'datatable';
        let orderBy = [2, 'asc'];
        let searchParams = {
            txtsearch: txtSearch,
            selstatus: selStatus,
            selstate: selState,
            seldistrict: selDistrict
        };
        let displayColumns = [1, 2, 3, 4, 5, 6];
        let dataTableColumns = [
            // {
            //     data: '',
            //     render: function(data, type, row) {
            //         return '<input class="form-check-input chkItem" type="checkbox" id="check' + row.service_id +
            //             '" name="chkStd' + row.id + '" value="' + row.id +
            //             '" onclick="checkFun(this.id)">';
            //     },
            //     className: "noPrint text-center"
            // },
            {
                data: 'slNo',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center"
            },
            {
                data: 'state_name',
                defaultContent: "--"
            },
            {
                data: 'city_name',
                defaultContent: "--"
            },
            {
                data: 'city_alias',
                defaultContent: "--"
            },
            {
                data: 'city_alias',
                defaultContent: "--"
            },
            {
                data: 'is_active',
                render: function(data, type, row) {
                    var cls = ((row.is_active == 'Active') ? 'badge bg-success' : 'badge bg-danger');
                    return '<span class="' + cls + '">' + row.is_active + '</span>';
                },
                className: "text-center"
            },

            // {
            //     data: 'created_date',
            //     defaultContent: "--",
            //     className: "text-center text-nowrap"
            // },

            {
                data: '',
                render: function(data, type, row) {

                    let editUrl = $('#' + tableId).data('edit-url');

                    if (!editUrl) return '';

                    return `
                        <a class="btn btn-sm btn-info"
                        href="${editUrl.replace('ID', row.enc_city_id)}">
                        <i class="fa fa-edit"></i> Edit
                        </a>
                    `;
                },
                className: "noPrint text-center"
            }
        ]

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }
</script>

@endpush