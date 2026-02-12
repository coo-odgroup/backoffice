@extends('admin.layouts.master')
@section('content')

<?php
      $page_name = 'All Cities';
      $listButtons = ['indicate'=>'N','print'=>'N','xls'=>'N','download'=>'N','back'=>'N','delete'=>'y', 'active' => 'y', 'inactive' => 'y'];
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
        <button type="button" class="btn btn-primary btn-sm" onclick="toggleFilter()">
            <i class="fa-solid fa-magnifying-glass me-1"></i> Search
        </button>
        <button class="btn btn-success btn-sm">+ Add Bus
        </button>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate>
    <div class="card">
        <div class="card-body">
            <!-- FILTER -->
            <div class="mb-3 border-bottom" id="filterBox">
                <div class="card-body">
                    <div class="row">
                        <!-- FILTER FIELDS -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6 col-sm-6 col-md-6  col-lg-2 mb-2">
                                    <label for="txtSearch">Search By City Name/Alias</label>
                                    <input type="text" class="form-control" id="txtSearch" name="txtSearch"
                                        placeholder="City Name/Alias">
                                </div>

                                <div class="col-12 col-sm-12 col-md-4 col-lg-2 mb-2">
                                    <label for="selState">State</label>
                                    <select class="form-select" id="selState" name="selState">
                                        <option value="0">Select State</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-2 mb-2">
                                    <label for="selDistrict">District</label>
                                    <select class="form-select" id="selDistrict" name="selDistrict">
                                        <option value="0">Select District</option>
                                    </select>
                                </div>
                                <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                                    <label for="selStatus">Status</label>
                                    <select class="form-select" id="selStatus" name="selStatus">
                                        <option value="">Select Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <!-- BUTTONS -->
                        <div class="col-12 mt-3 d-flex justify-content-end flex-wrap action-btns">
                            <button class="btn btn-primary btn-sm" type="button" onclick="return getDataTableView()">
                                <i class="fa-solid fa-check me-1"></i>Submit
                            </button>
                            <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                <i class="fa-solid fa-rotate-left me-1"></i>Reset
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Table start -->
             <div id="tableActions">
                 <div class="d-flex justify-content-between mb-2">
                <select id="pageSizeDatatable" class="form-select page-size">
                    <option value="10" selected="selected">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="-1">All</option>
                </select>
                <div>
                    <button type="button" id="btnExcel" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-file-excel me-1"></i>
                        Delete
                    </button>
                    <button type="button" id="btnPdf" class="btn btn-warning btn-sm text-white">
                        <i class="fa-solid fa-file-pdf me-1"></i>
                        Active
                    </button>
                    <button type="button" id="btnPrint" class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-print me-1"></i>
                        Inactive
                    </button>

                </div>
                
            </div>
             </div>
           

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button type="button" id="btnExcel" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-file-excel me-1"></i>
                    </button>
                    <button type="button" id="btnPdf" class="btn btn-warning btn-sm text-white">
                        <i class="fa-solid fa-file-pdf me-1"></i>
                    </button>
                    <button type="button" id="btnPrint" class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-print me-1"></i>
                    </button>

                </div>
                <div id="customPaginationTop"></div>
            </div>
            <table class="table table-hover table-bordered align-middle table-sm" id="datatable"
                data-url="{{ route('cities.dataTableView') }}" data-edit-url="{{ route('cities.edit', 'ID') }}">
                <thead class="thead-light">
                    <tr>
                        <th>Sl No</th>
                        <th>State/District Name</th>
                        <th>City Name</th>
                        <th>Alias</th>
                        <th>Synonymn</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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