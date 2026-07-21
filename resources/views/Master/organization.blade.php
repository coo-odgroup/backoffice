@extends('admin.layouts.master')
@section('page_title', 'Organization')
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
        <li class="breadcrumb-item active">@yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <button type="button" id="btnToggleFilter" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-magnifying-glass me-1"></i>
            <span class="btn-text">Filter</span>
        </button>
        <a href="{{ route('organization.add') }}" class="btn btn-success btn-sm">
            + Add @yield('page_title')
        </a>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate>
    <div class="card">
        <div class="card-body">
            <!-- FILTER -->
            <div class="mb-1 border-bottom d-none" id="filterBox">
                <div class="card-body">
                    <div class="row align-items-end">

                        <!-- Search -->
                        <div class="col-lg-2 col-md-6">
                            <label for="txtSearch">Search By Organization</label>
                            <input type="text" class="form-control clearable form-control-sm" id="txtSearch" name="txtSearch"
                                placeholder="Enter Organization">
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label for="selOrgType">Organization Type</label>
                            <select class="form-select form-select-sm selOrg" id="selOrgType" name="selOrgType">
                                <option value="">Select Organization Type</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-lg-2 col-md-6">
                            <label for="selStatus">Status</label>
                            <select class="form-select form-select-sm" id="selStatus" name="selStatus">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-lg-4 d-flex justify-content-end flex-wrap action-btns gap-1 mt-1">
                            <button class="btn btn-primary btn-sm" type="button" onclick="getDataTableView()">
                                <i class="fa-solid fa-search me-1"></i>Search
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
                        <button type="button" id="btnDelete" class="btn btn-warning btn-sm d-none" onclick="actionRec('D');">
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
                <div id="utilitiesTop">
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
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle table-sm table-responsive"
                    id="datatable"
                    data-url="{{ route('organization.dataTableView') }}"
                    data-edit-url="{{ route('organization.edit','ID') }}"
                    data-address-url="{{ route('organization-address.edit','ID') }}"
                    data-bank-url="{{ route('organization-bank-account.edit','ID') }}"
                    data-contact-url="{{ route('organization-contacts.edit','ID') }}"
                    data-tax-url="{{ route('organization-tax-details.edit','ID') }}"
                    data-document-url="{{ route('organization-document.edit','ID') }}"
                    data-view-url="{{ route('organization.view','ID') }}">
                    <thead class="table-secondary">
                        <tr>
                            <th class="noPrint no-sort">
                                <div class="checkbox">
                                    <input id="checkboxall" name="btSelectItem" class="chkAll" type="checkbox">
                                </div>
                            </th>
                            <th>Sl No</th>
                            <th>Organization Name </th>
                            <th>Organization Type </th>
                            <th>Unique ID</th>
                            <th>Parent</th>
                            <th>ORG Website</th>
                            <th>Last Modified</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="footer-background border-success text-center" id="norecord" style="display:none">No record found.</div>
            {{csrf_field()}}
            <input name="hdn_ids" id="hdn_ids" type="hidden">
            <input name="hdn_qs" id="hdn_qs" type="hidden">
            <input type="hidden" id="hdn_model" value="Organization">

            <div class="d-flex justify-content-between align-items-center mt-2">
                <div id="customTableInfo"></div>
                <div id="customPagination"></div>
            </div>
        </div>
    </div>
    </div>
</form>
<!-- ===========================================================
    Organization View Modal
============================================================ -->
<div class="modal fade" id="organizationViewModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content modern-modal">

            <div class="modal-header modern-modal-header">

                <div class="d-flex align-items-center">

                    <div class="modal-icon">
                        <i class="fa fa-building"></i>
                    </div>

                    <div class="ms-3">
                        <h4 class="mb-0 fw-bold text-white">
                            Organization Details
                        </h4>

                        <small class="text-white-50">
                            Complete Organization Profile
                        </small>
                    </div>

                </div>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body modern-modal-body">

                <div id="viewLoader" class="text-center py-5">

                    <div class="spinner-border text-primary"></div>

                    <h6 class="mt-3">
                        Loading Organization...
                    </h6>

                </div>

                <div id="viewContent" style="display:none">

                    <div id="organizationHero"></div>

                    <div class="section-title">
                        <i class="fa fa-users"></i>
                        <span>Contacts</span>
                    </div>
                    <div id="contactCards" class="row g-3 mb-4"></div>

                    <div class="section-title">
                        <i class="fa fa-location-dot"></i>
                        <span>Addresses</span>
                    </div>
                    <div id="addressCards" class="row g-3 mb-4"></div>

                    <div class="section-title">
                        <i class="fa fa-building-columns"></i>
                        <span>Bank Accounts</span>
                    </div>
                    <div id="bankCards" class="row g-3 mb-4"></div>

                    <div class="section-title">
                        <i class="fa fa-file-invoice"></i>
                        <span>Tax Details</span>
                    </div>
                    <div id="taxDetails" class="mb-4"></div>

                    <div class="section-title">
                        <i class="fa fa-file-lines"></i>
                        <span>Documents</span>
                    </div>
                    <div id="documentCards" class="row g-3"></div>

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>

    </div>

</div>
</div>
</div>
<style>
    :root {
        --bg: #f8fafc;
        --card: #ffffff;
        --border: #e5e7eb;
        --text: #111827;
        --muted: #6b7280;
        --primary: #2563eb;
    }

    .modern-modal {
        background: #fff;
        border: none;
        border-radius: 14px;
        overflow: hidden;
    }

    .modern-modal-header {
        background: #fff;
        color: #111827;
        border-bottom: 1px solid #e5e7eb;
        padding: 18px 22px;
    }

    .modern-modal-header h4 {
        color: #111827 !important;
        font-size: 22px;
    }

    .modern-modal-header small {
        color: #6b7280 !important;
    }

    .modal-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #374151;
        font-size: 18px;
    }

    .modern-modal-body {
        background: #f9fafb;
        padding: 20px;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 22px 0 12px;
        font-size: 18px;
        font-weight: 600;
        color: #111827;
    }

    .section-title i {
        width: 30px;
        text-align: center;
        color: #6b7280;
        background: none;
    }

    .hero-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 20px;
    }

    .hero-row {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .hero-logo {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .hero-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-info {
        flex: 1;
    }

    .line1 {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .org-name {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
    }

    .line2 {
        font-size: 14px;
        color: #6b7280;
    }

    .line3 {
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        margin-top: 10px;
        font-size: 14px;
    }

    .line3 a {
        color: #2563eb;
        text-decoration: none;
    }

    .line3 span {
        color: #374151;
    }

    .badge-primary,
    .badge-success,
    .badge-default {
        font-size: 11px;
        font-weight: 600;
        border-radius: 20px;
        padding: 4px 10px;
    }

    .badge-primary {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .badge-success {
        background: #dcfce7;
        color: #15803d;
    }

    .badge-default {
        background: #f3f4f6;
        color: #374151;
    }

    .info-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px;
        height: 100%;
    }

    .card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 14px;
    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
    }

    .card-subtitle {
        font-size: 13px;
        color: #6b7280;
    }

    .info-row {
        display: flex;
        gap: 16px;
        padding: 7px 0;
        border-top: 1px dashed #f1f5f9;
    }

    .info-row:first-child {
        border-top: none;
    }

    .info-label {
        width: 120px;
        flex-shrink: 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .info-value {
        flex: 1;
        color: #111827;
        font-size: 14px;
        word-break: break-word;
    }

    .address-block {
        margin-bottom: 12px;
        color: #374151;
        line-height: 1.6;
    }

    .tax-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .tax-item {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        background: #fff;
    }

    .tax-item label {
        display: block;
        color: #6b7280;
        font-size: 11px;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .tax-item div {
        color: #111827;
        font-weight: 600;
        font-size: 14px;
    }

    .doc-btn {
        margin-top: 14px;
    }

    .doc-btn .btn {
        border-radius: 6px;
    }

    .alert {
        border-radius: 10px;
    }

    @media(max-width:768px) {

        .hero-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .line1 {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .info-row {
            flex-direction: column;
            gap: 3px;
        }

        .info-label {
            width: 100%;
        }

        .tax-grid {
            grid-template-columns: 1fr;
        }

    }
</style>
@endsection
@push('scripts')

<script type="module">
    window.bulkActionUrl = "{{ route('admin.bulkAction') }}";

    $('#backoffice-form').on('submit', function(e) {
        e.preventDefault();
    });

    $(document).ready(function() {
        commonAjax.initTableCheckbox('#checkboxall', '.chkItem');
        commonAjax.initSelect2('#selOrgType', 'Select Organization Type');
        commonAjax.loadOrganizationTypeList();
        commonAjax.initClearableInputs();
        getDataTableView();
    });


    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
        getDataTableView(true);
    });

    window.getDataTableView = function(reset = true) {

        //  If table already initialized
        if (window.dataTableInstance && reset) {

            // Clear saved state
            window.dataTableInstance.state.clear();

            // Reset length dropdown UI
            $('#pageSizeDatatable').val(10);

            // Reset page length internally
            window.dataTableInstance.page.len(10);

            // Force first page
            window.dataTableInstance.page(0);
        }

        $('#pageSizeDatatable').val(10);
        let txtSearch = '';
        let selStatus = '';
        let selOrgType = '';

        if ($('#txtSearch').val() != '') {
            txtSearch = $('#txtSearch').val();
        }

        if ($('#selOrgType').val() != '') {
            selOrgType = $('#selOrgType').val();
        }

        if ($('#selStatus').val() != '') {
            selStatus = $('#selStatus').val();
        }


        let tableId = 'datatable';
        let orderBy = [2, 'asc'];
        let searchParams = {
            txtSearch: txtSearch,
            selStatus: selStatus,
            selOrgType: selOrgType
        };
        let displayColumns = [1, 2, 3, 4, 5, 6, 7];
        let dataTableColumns = [{
                data: '',
                render: function(data, type, row) {
                    return '<div class="checkbox"><input class="inverted chkItem" type="checkbox" id="check' + row.id +
                        '" name="chkStd' + row.id + '" value="' + row.id +
                        '" ></div>';
                },
                className: "noPrint text-center"
            },
            {
                data: 'slNo',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center"
            },
            {
                data: 'org_name',
                defaultContent: "--"
            },
            {
                data: 'org_type',
                defaultContent: "--"
            },
            {
                data: 'unique_id',
                defaultContent: "--"

            },
            {
                data: 'parent',
                defaultContent: "--"

            },
            {
                data: 'website_url',
                render: function(data) {
                    return data ? `<a href="${data}" target="_blank">${data}</a>` : '--';
                }
            },
            {
                data: null,
                render: function(data, type, row) {

                    let createdBy = row.created_by_name ?? '--';
                    let createdAt = row.created_date ?? '--';

                    let updatedBy = row.updated_by_name ? row.updated_by_name : '--';
                    let updatedAt = (row.updated_date) ? row.updated_date : '--';

                    // Show updated date if exists, else created date
                    let displayDate = (updatedAt != '--') ? updatedAt : createdAt;

                    return `
                        <span
                            class="fw-semibold text-decoration-underline cursor-pointer"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            data-bs-html="true"
                            title="
                                <div class='audit-box'>
                                    <div><strong>Created By:</strong> ${createdBy}</div>
                                    <div><strong>Created At:</strong> ${createdAt}</div>
                                    <hr class='my-1'>
                                    <div><strong>Updated By:</strong> ${updatedBy}</div>
                                    <div><strong>Updated At:</strong> ${updatedAt}</div>
                                </div>
                            ">
                            ${displayDate}
                        </span>
                    `;
                }
            },
            {
                data: 'is_active',
                render: function(data, type, row) {
                    var cls = ((row.is_active == 'Active') ? 'badge bg-success' : 'badge bg-danger');
                    return '<span class="' + cls + '">' + row.is_active + '</span>';
                },
                className: "text-center"
            },
            {
                data: '',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {

                    let editUrl = $('#' + tableId).data('edit-url');
                    let addressUrl = $('#' + tableId).data('address-url');
                    let bankUrl = $('#' + tableId).data('bank-url');
                    let contactUrl = $('#' + tableId).data('contact-url');
                    let taxUrl = $('#' + tableId).data('tax-url');
                    let documentUrl = $('#' + tableId).data('document-url');
                    let viewUrl = $('#' + tableId).data('view-url');

                    return `
                    <div class="d-flex justify-content-center align-items-center gap-1">

                            <span class="btn btn-sm btn-primary btnViewCategory"
                                data-id="${row.enc_id}">
                                <i class="fa fa-eye"></i> View
                            </span>

                        <!-- Edit Button -->
                        <a class="btn btn-sm btn-info"
                            href="${editUrl.replace('ID', row.enc_id)}">
                            <i class="fa fa-edit"></i> Edit
                        </a>

                        <!-- View Log Button -->
                        <a href="javascript:void(0);"
                            class="btn btn-sm btn-success btn-view-log"
                            data-table="mst_organization"
                            data-id="${row.enc_id}">
                            <i class="fa fa-history"></i>
                        </a>

                        <!-- Three Dot Menu -->
                        <div class="dropdown">

                            <button class="btn btn-sm btn-secondary"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">

                                <li>
                                    <a class="dropdown-item"
                                        href="${addressUrl.replace('ID', row.enc_id)}">
                                        <i class="fa fa-map-marker-alt me-2"></i>
                                        Add / Edit Address
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="${bankUrl.replace('ID', row.enc_id)}">
                                        <i class="fa fa-university me-2"></i>
                                        Add / Edit Bank Account
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="${contactUrl.replace('ID', row.enc_id)}">
                                        <i class="fa fa-phone me-2"></i>
                                        Add / Edit Contacts
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="${taxUrl.replace('ID', row.enc_id)}">
                                        <i class="fa fa-file-invoice-dollar me-2"></i>
                                        Add / Edit Tax Details
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="${documentUrl.replace('ID', row.enc_id)}">
                                        <i class="fa fa-file-invoice-dollar me-2"></i>
                                        Add / Edit Documents
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>`;
                },
                className: "text-center noPrint"
            }
        ]

        loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns);
    }

    $(document).on('click', '.btnViewCategory', function(e) {

        e.preventDefault();
        let encId = $(this).data('id');
        let url = $('#datatable')
            .data('view-url')
            .replace('ID', encId);

        console.log(url);

        $('#viewLoader').show();
        $('#viewContent').hide();
        const modal = new bootstrap.Modal(document.getElementById('organizationViewModal'));
        modal.show();

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                console.log(res);

                if (!res.status) {
                    Swal.fire('Error', 'Unable to load organization.', 'error');
                    return;
                }
                renderOrganization(res);
            },

            error: function(xhr) {
                console.log(xhr);
                console.log(xhr.responseText);

                Swal.fire('Error', 'Something went wrong.', 'error');
            },
            complete: function() {
                $('#viewLoader').hide();
                $('#viewContent').show();
            }
        });

    });

    function clearViewSections() {
        $('#organizationInfo').html('');
        $('#contactCards').html('');
        $('#addressCards').html('');
        $('#bankCards').html('');
        $('#taxDetails').html('');
        $('#documentCards').html('');
    }

    function renderOrganization(res) {

        clearViewSections();
        renderHero(res.organization);
        renderContacts(res.contacts);
        renderAddresses(res.addresses);
        renderBanks(res.banks);
        renderTax(res.tax);
        renderDocuments(res.documents);

    }
    $('#organizationViewModal').on('hidden.bs.modal', function() {
        clearViewSections();
        $('#viewLoader').show();
        $('#viewContent').hide();
    });

    function renderHero(org) {

        if (!org) {
            $('#organizationHero').html('');
            return;
        }

        let logo = org.logo ?
            `<img src="/uploads/organization/${org.logo}" alt="">` :
            `<i class="fa fa-building fa-lg text-secondary"></i>`;

        let statusBadge = org.active_status == 1 ?
            `<span class="badge bg-success rounded-pill">Active</span>` :
            `<span class="badge bg-danger rounded-pill">Inactive</span>`;

        let website = org.website_url ?
            `<a href="${org.website_url}" target="_blank">
                <i class="fa fa-globe me-1"></i>${org.website_url}
           </a>` :
            '';

        let email = org.email ?
            `<span>
                <i class="fa fa-envelope me-1"></i>${org.email}
           </span>` :
            '';

        let mobile = org.mobile ?
            `<span>
                <i class="fa fa-phone me-1"></i>${org.mobile}
           </span>` :
            '';
        $('#organizationHero').html(`
            <div class="hero-card">
                <div class="hero-row">
                    <div class="hero-logo">
                        ${logo}
                    </div>
                    <div class="hero-info">
                        <div class="line1">
                            <div>
                                <div class="org-name">
                                    ${org.organization_name ?? '--'}
                                </div>
                                <div class="line2">
                                    ${org.organization_type_name ?? '--'}
                                    &nbsp;&nbsp;•&nbsp;&nbsp;
                                    ${org.organization_code ?? '--'}
                                    &nbsp;&nbsp;•&nbsp;&nbsp;
                                    UID : ${org.unique_id ?? '--'}

                                </div>

                            </div>

                            ${statusBadge}

                        </div>

                        <div class="line3">

                            ${website}

                            ${email}

                            ${mobile}

                        </div>

                    </div>

                </div>

            </div>

            `);

    }

    function renderContacts(contacts) {

        if (!contacts || !contacts.length) {

            $('#contactCards').html(`
            <div class="col-12">
                <div class="alert alert-light border">
                    No Contact Details Available
                </div>
            </div>
        `);

            return;
        }

        let html = '';

        $.each(contacts, function(i, row) {

            html += `

        <div class="col-lg-6">

            <div class="info-card">

                <div class="card-head">

                    <div>

                        <div class="card-title">

                            ${row.fullname ?? '--'}

                        </div>

                        <div class="card-subtitle">

                            ${row.contact_type_name ?? '--'}

                        </div>

                    </div>

                    ${
                        row.is_primary==1
                        ?'<span class="badge bg-success rounded-pill">Primary</span>'
                        :''
                    }

                </div>

                <div class="info-row">

                    <div class="info-label">

                        Designation

                    </div>

                    <div class="info-value">

                        ${row.designation ?? '--'}

                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">

                        Mobile

                    </div>

                    <div class="info-value">

                        ${
                            row.mobile
                            ?`<a href="tel:${row.mobile}">
                                ${row.mobile}
                              </a>`
                            :'--'
                        }

                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">

                        Alternate

                    </div>

                    <div class="info-value">

                        ${
                            row.alternate_mobile
                            ?`<a href="tel:${row.alternate_mobile}">
                                ${row.alternate_mobile}
                              </a>`
                            :'--'
                        }

                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">

                        Email

                    </div>

                    <div class="info-value">

                        ${
                            row.email
                            ?`<a href="mailto:${row.email}">
                                ${row.email}
                              </a>`
                            :'--'
                        }

                    </div>

                </div>

            </div>

        </div>

        `;

        });

        $('#contactCards').html(html);

    }

    function renderAddresses(addresses) {

        if (!addresses || !addresses.length) {

            $('#addressCards').html(`
            <div class="col-12">
                <div class="alert alert-light border">
                    No Address Available
                </div>
            </div>
        `);

            return;
        }

        let html = '';
        $.each(addresses, function(i, row) {
            html += `
        <div class="col-lg-6">
            <div class="info-card">
                <div class="card-head">
                    <div>
                        <div class="card-title">
                            ${row.address_type_name ?? 'Address'}
                        </div>
                        <div class="card-subtitle">
                            ${row.city_name ?? '--'}, ${row.state_name ?? '--'}
                        </div>
                    </div>
                    ${ row.is_default==1 ?'<span class="badge bg-primary rounded-pill">Default</span>':'' }
                </div>
               <div class="address-block">
                    <div class="info-row">
                        <div class="info-label"> Address Line 1 </div>
                        <div class="info-value"> ${row.address1 ?? '--'} </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label"> Address Line 2 </div>
                        <div class="info-value"> ${row.address2 ?? '--'} </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label"> Landmark </div>
                        <div class="info-value"> ${row.landmark ?? '--'} </div>
                    </div>

                </div>
                <div class="info-row">
                    <div class="info-label"> City </div>
                    <div class="info-value"> ${row.city_name ?? '--'} </div>
                </div>
                <div class="info-row">
                    <div class="info-label"> District </div>
                    <div class="info-value"> ${row.district_name ?? '--'} </div>
                </div>
                <div class="info-row">
                    <div class="info-label"> State </div>
                    <div class="info-value"> ${row.state_name ?? '--'} </div>
                </div>
                <div class="info-row">
                    <div class="info-label"> Country </div>
                    <div class="info-value"> ${row.country_name ?? 'India'} </div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        Pincode
                    </div>
                    <div class="info-value">
                        ${row.pincode ?? '--'}
                    </div>
                </div>
            </div>
        </div>
        `;
        });
        $('#addressCards').html(html);
    }

    function renderBanks(banks) {

        if (!banks || !banks.length) {

            $('#bankCards').html(`
            <div class="col-12">
                <div class="alert alert-light border">
                    No Bank Accounts Available
                </div>
            </div>
        `);

            return;
        }

        let html = '';

        $.each(banks, function(i, row) {

          let accountNumber = row.account_number ?? '--';

            html += `

        <div class="col-lg-6">
            <div class="info-card">
                <div class="card-head">
                    <div>
                        <div class="card-title"> ${row.bank_name ?? '--'}</div>
                        <div class="card-subtitle"> ${row.branch_name ?? '--'} </div>
                    </div>
                    ${ row.is_primary==1 ?'<span class="badge bg-success rounded-pill">Primary</span>' :'' }
                </div>
                <div class="info-row">
                    <div class="info-label"> Account Holder </div>
                    <div class="info-value"> ${row.account_holder ?? '--'} </div>
                </div>
                <div class="info-row">
                    <div class="info-label"> Account No. </div>
                    <div class="info-value"> ${accountNumber} </div>
                </div>
                <div class="info-row">
                    <div class="info-label"> IFSC </div>
                    <div class="info-value"> ${row.ifsc ?? '--'} </div>
                </div>
                <div class="info-row">
                    <div class="info-label"> UPI ID </div>
                    <div class="info-value"> ${row.upi_id ?? '--'} </div>
                </div>
            </div>
        </div>
        `;
        });
        $('#bankCards').html(html);

    }

    function renderTax(tax) {

        if (!tax) {

            $('#taxDetails').html(`
            <div class="alert alert-light border">
                No Tax Details Available
            </div>
        `);

            return;
        }

        $('#taxDetails').html(`

        <div class="info-card">
            <div class="card-head">
                <div>
                    <div class="card-title"> Tax Details </div>
                    <div class="card-subtitle"> Government Registration Information </div>
                </div>
            </div>
            <div class="tax-grid">
                <div class="tax-item">
                    <label>GST Number</label>
                    <div>${tax.gst_number ?? '--'}</div>
                </div>
                <div class="tax-item">
                    <label>PAN Number</label>
                    <div>${tax.pan_number ?? '--'}</div>
                </div>
                <div class="tax-item">
                    <label>TAN Number</label>
                    <div>${tax.tan_number ?? '--'}</div>
                </div>
                <div class="tax-item">
                    <label>CIN Number</label>
                    <div>${tax.cin_number ?? '--'}</div>
                </div>
                <div class="tax-item">
                    <label>MSME Number</label>
                    <div>${tax.msme_number ?? '--'}</div>
                </div>
                <div class="tax-item">
                    <label>Trade Licence</label>
                    <div>${tax.trade_license_number ?? '--'}</div>
                </div>
                <div class="tax-item" style="grid-column:span 3;">
                    <label>GST Registered Name</label>
                    <div> ${tax.gst_registered_name ?? '--'} </div>
                </div>
                <div class="tax-item" style="grid-column:span 3;">
                    <label>GST Registered Address</label>
                    <div> ${tax.gst_registered_address ?? '--'} </div>
                </div>
            </div>
        </div>
        `);
    }

    function renderDocuments(documents) {

        if (!documents || !documents.length) {

            $('#documentCards').html(`
            <div class="col-12">
                <div class="alert alert-light border">
                    No Documents Available
                </div>
            </div>
        `);

            return;
        }

        let html = '';

        $.each(documents, function(i, row) {

            let fileBtn = '';
            if (row.file_path) {
                fileBtn = `
                <a href="/${row.file_path}"
                    target="_blank"
                    class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-eye me-1"></i>
                    View File
                </a>
            `;
            } else {
                fileBtn = `
                <span class="text-muted small">
                    No File Uploaded
                </span>
            `;
            }
            html += `
        <div class="col-lg-6">
            <div class="info-card">
                <div class="card-head">
                    <div>
                        <div class="card-title">
                            ${row.document_name ?? '--'}
                        </div>
                        <div class="card-subtitle">
                            Document Information
                        </div>
                    </div>
                    ${fileBtn}
                </div>
                <div class="info-row">
                    <div class="info-label">
                        Document No.
                    </div>
                    <div class="info-value">
                        ${row.document_number ?? '--'}
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        Issue Date
                    </div>
                    <div class="info-value">
                        ${row.issue_date ?? '--'}
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                     Expiry Date
                    </div>
                    <div class="info-value">
                        ${row.expiry_date ?? '--'}
                    </div>
                </div>
            </div>
        </div>`;
        });
        $('#documentCards').html(html);

    }
</script>
@endpush