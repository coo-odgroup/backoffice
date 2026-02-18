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
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">City List</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">Cities</h5>
    <div>
        <button type="button"
            class="btn btn-primary btn-sm"
            id="filterToggleBtn"
            onclick="toggleFilter()">
            <i class="fa-solid fa-magnifying-glass me-1"></i>
            <span class="btn-text">Search</span>
        </button>

        <a href="{{ route('cities.add') }}" class="btn btn-success btn-sm">
            + Add City
        </a>
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
                            <button class="btn btn-primary btn-sm" type="button" onclick="getDataTableView()">
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
                    <select id="pageSizeDatatable" class="form-select form-select-sm page-size">
                        <option value="10" selected="selected">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">All</option>
                    </select>
                    <div>
                        <button type="button" id="btnDelete" class="btn btn-warning btn-sm" onclick="actionRec('D');">
                            <i class="fa-solid fa-trash me-1"></i>
                            Delete
                        </button>
                        <button type="button" id="btnActive" class="btn btn-success btn-sm text-white" onclick="actionRec('A');">
                            <i class="fa-solid fa-circle-check me-1"></i>
                            Active
                        </button>
                        <button type="button" id="btnInactive" class="btn btn-danger btn-sm" onclick="actionRec('UN');">
                            <i class="fa-solid fa-times me-1"></i>
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
                data-url="{{ route('cities.dataTableView') }}"
                data-edit-url="{{ route('cities.edit', 'ID') }}">
                <thead class="thead-light">
                    <tr>
                        <th class="noPrint no-sort">
                            <input id="checkboxall" name="btSelectItem" class="form-check-input chkAll" type="checkbox">
                        </th>
                        <th>Sl No</th>
                        <th>State/District Name</th>
                        <th>City Name</th>
                        <th>Alias</th>
                        <th>Synonymn</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th class="no-sort">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="footer-background border-success text-center" id="norecord" style="display:none">No record found.</div>
            {{csrf_field()}}
            <input name="hdn_ids" id="hdn_ids" type="hidden">
            <input name="hdn_qs" id="hdn_qs" type="hidden">
            <input type="hidden" id="hdn_model" value="Cities">

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

<script type="module">
    window.bulkActionUrl = "{{ route('admin.bulkAction') }}";

    
    $(document).ready(function () {

        // init selects
        commonAjax.initSelect2('#selState', 'Select State');
        commonAjax.initSelect2('#selDistrict', 'Select District');

        // hide filter by default
        $('#filterBox').hide();

        // load data
        commonAjax.loadStateList();
        commonAjax.initTableCheckbox('#checkboxall', '.chkItem');
        getDataTableView();
    });

 
    let filterOpen = false;

    window.toggleFilter = function () {
        const filterBox = $('#filterBox');
        const btn = $('#filterToggleBtn');
        const btnText = btn.find('.btn-text');
        const btnIcon = btn.find('i');

        if (!filterOpen) {
            // OPEN
            filterBox.slideDown(300);

            btn.removeClass('btn-primary').addClass('btn-danger');
            btnText.text('Close');

            btnIcon
                .removeClass('fa-magnifying-glass')
                .addClass('fa-xmark');

            filterOpen = true;
        } else {
            // CLOSE
            filterBox.slideUp(300);

            btn.removeClass('btn-danger').addClass('btn-primary');
            btnText.text('Search');

            btnIcon
                .removeClass('fa-xmark')
                .addClass('fa-magnifying-glass');

            filterOpen = false;
        }
    };

    $('#btnReset').on('click', function () {
        $(':input', '#backoffice-form')
            .not(':button, :submit, :reset, :hidden')
            .val('');

        $('.form-select').val('').trigger('change');

        getDataTableView();
    });


    $(document).on('change', '#selState', function () {
        commonAjax.getDistrictList($(this).val());
    });


    document.getElementById("menu-toggle")?.addEventListener("click", function () {
        document.getElementById("sidebar-wrapper")?.classList.toggle("collapsed");
    });


    window.getDataTableView = function () {

        $('#pageSizeDatatable').val(10);

        let searchParams = {
            txtsearch: $('#txtSearch').val() || '',
            selstatus: $('#selStatus').val() || '',
            selstate: $('#selState').val() || 0,
            seldistrict: $('#selDistrict').val() || 0
        };

        let tableId = 'datatable';
        let orderBy = [2, 'asc'];

        let dataTableColumns = [
            {
                data: '',
                className: "noPrint text-center",
                render: (d, t, r) =>
                    `<input class="form-check-input chkItem" type="checkbox" value="${r.city_id}">`
            },
            {
                data: null,
                className: "text-center",
                render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1
            },
            { data: 'state_name', defaultContent: "--" },
            { data: 'city_name', defaultContent: "--" },
            { data: 'city_alias', defaultContent: "--" },
            {
                data: 'synonyms',
                orderable: false,
                searchable: false,
                render: data => {
                    if (!data) return '--';
                    return data
                        .split('||')
                        .map((v, i) => `${i + 1}. ${v.trim()}`)
                        .join('<br>');
                }
            },
            { data: 'created_by_name', defaultContent: "--" },
            { data: 'created_date', defaultContent: "--" },
            {
                data: 'is_active',
                className: "text-center",
                render: d =>
                    `<span class="badge ${d === 'Active' ? 'bg-success' : 'bg-danger'}">${d}</span>`
            },
            {
                data: '',
                className: "noPrint text-center",
                render: (d, t, r) => {
                    let editUrl = $('#datatable').data('edit-url');
                    return editUrl
                        ? `<a class="btn btn-sm btn-info" href="${editUrl.replace('ID', r.enc_city_id)}">
                               <i class="fa fa-edit"></i> Edit
                           </a>`
                        : '';
                }
            }
        ];

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, [1,2,3,4,5,6]);
    };
</script>



@endpush