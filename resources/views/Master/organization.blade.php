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
<div class="modal fade"
    id="organizationViewModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header -->
            <div class="modal-header  text-white" style="background:#0f4c81;color:#fff;">
                <h5 class="modal-title">
                    <i class="fa fa-building me-2"></i>
                    Organization Details
                </h5>
                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body bg-light">
                <!-- Loader -->
                <div id="viewLoader"
                    class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <h6 class="mt-3">
                        Loading Organization...
                    </h6>
                </div>
                <!-- Actual Content -->
                <div id="viewContent" style="display:none;">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header  text-white" style="background:#0f4c81;color:#fff;">
                            <strong> Organization Information</strong>
                        </div>
                        <div class="card-body">
                            <div class="row g-3" id="organizationInfo">
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm mb-3">
                        <div class="card-header  text-white" style="background:#198754;color:#fff;">
                            <strong> Contacts </strong>
                        </div>
                        <div class="card-body">
                            <div id="contactCards" class="row g-3">
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm mb-3">
                        <div class="card-header text-white" style="background:#0ea5e9;color:#fff;">
                            <strong>
                                Addresses
                            </strong>
                        </div>
                        <div class="card-body">
                            <div id="addressCards" class="row g-3">
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm mb-3">
                        <div class="card-header " style="background:#f59e0b;color:#fff;">
                            <strong> Bank Accounts </strong>
                        </div>
                        <div class="card-body">
                            <div id="bankCards" class="row g-3">
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm mb-3">
                        <div class="card-header text-white" style="background:#374151;color:#fff;">
                            <strong> Tax Details </strong>
                        </div>
                        <div class="card-body">
                            <div id="taxDetails">
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background:#6b7280;color:#fff;">
                            <strong> Documents</strong>
                        </div>
                        <div class="card-body">
                            <div id="documentCards" class="row g-3">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <div class="modal-footer">
                <button
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<style>
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
        renderOrganizationInfo(res.organization);
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

    function renderOrganizationInfo(org) {

        if (!org) {
            $('#organizationInfo').html(`
            <div class="col-12">
                <div class="alert alert-warning rounded-4">
                    <i class="fa fa-circle-info me-2"></i>Organization information not found.
                </div>
            </div>
        `);
            return;
        }

        let logo = org.logo ?
            `<img src="/uploads/organization/${org.logo}" class="org-profile-img">` :
            `<div class="org-profile-placeholder"><i class="fa fa-building"></i></div>`;
        $('#organizationInfo').html(`
        <div class="col-12">
            <div class="modern-card">
                <div class="d-flex align-items-center flex-wrap gap-4">
                    ${logo}
                    <div class="flex-grow-1">
                        <h3 class="org-title mb-1">
                            ${org.organization_name ?? '--'}
                        </h3>
                        <div class="text-muted mb-3">
                            ${org.organization_type_name ?? '--'}
                        </div>
                        <div class="d-flex flex-wrap gap-2">

                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                ${org.organization_code ?? '--'}
                            </span>
                            <span class="badge bg-success rounded-pill px-3 py-2">
                                ${org.unique_id ?? '--'}
                            </span>
                        </div>
                    </div>
                </div>
                <hr class="my-4">

                <div class="row g-3">

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>Parent Organization</label>
                            <div>${org.parent_name ?? '--'}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>Website</label>
                            <div>
                                ${
                                    org.website_url
                                    ? `<a href="${org.website_url}" target="_blank">${org.website_url}</a>`
                                    : '--'
                                }
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>Email</label>
                            <div>${org.email ?? '--'}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>Phone</label>
                            <div>${org.mobile ?? '--'}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>Status</label>
                            <div>
                                ${
                                    org.active_status==1
                                    ? '<span class="badge bg-success rounded-pill">Active</span>'
                                    : '<span class="badge bg-danger rounded-pill">Inactive</span>'
                                }
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>Created On</label>
                            <div>${org.created_at ?? '--'}</div>
                        </div>
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
                <div class="alert alert-warning rounded-4 shadow-sm">
                    <i class="fa fa-circle-info me-2"></i>
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

            <div class="modern-card contact-card">

                <div class="modern-header">

                    <div>

                        <div class="avatar-circle">

                            <i class="fa fa-user"></i>

                        </div>

                    </div>

                    <div class="flex-grow-1 ms-3">

                        <h5>

                            ${row.fullname ?? '--'}

                        </h5>

                        <small>

                            ${row.contact_type_name ?? '--'}

                        </small>

                    </div>

                    ${
                        row.is_primary == 1
                        ?
                        `<span class="badge bg-success px-3 py-2 rounded-pill">
                            Primary
                        </span>`
                        :
                        ''
                    }

                </div>

                <div class="modern-body">

                    <div class="info-item">

                        <span>

                            <i class="fa fa-briefcase"></i>

                            Designation

                        </span>

                        <strong>

                            ${row.designation ?? '--'}

                        </strong>

                    </div>

                    <div class="info-item">

                        <span>

                            <i class="fa fa-phone"></i>

                            Mobile

                        </span>

                        <strong>

                            ${row.mobile ?? '--'}

                        </strong>

                    </div>

                    <div class="info-item">

                        <span>

                            <i class="fa fa-mobile-screen"></i>

                            Alternate

                        </span>

                        <strong>

                            ${row.alternate_mobile ?? '--'}

                        </strong>

                    </div>

                    <div class="info-item">

                        <span>

                            <i class="fa fa-envelope"></i>

                            Email

                        </span>

                        <strong class="text-primary">

                            ${row.email ?? '--'}

                        </strong>

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
                <div class="alert alert-warning rounded-4 shadow-sm">
                    <i class="fa fa-circle-info me-2"></i>No Address Available
                </div>
            </div>
        `);
            return;
        }

        let html = '';

        $.each(addresses, function(i, row) {

            html += `
        <div class="col-lg-6">
            <div class="modern-card address-card">

                <div class="modern-header">
                    <div class="avatar-circle bg-info">
                        <i class="fa fa-location-dot"></i>
                    </div>

                    <div class="flex-grow-1 ms-3">
                        <h5>${row.address_type_name ?? '--'}</h5>
                        <small>${row.city_name ?? '--'}, ${row.state_name ?? '--'}</small>
                    </div>

                    ${row.is_default==1?'<span class="badge bg-primary rounded-pill px-3">Default</span>':''}
                </div>

                <div class="modern-body">

                    <div class="info-item">
                        <span><i class="fa fa-map"></i> Address</span>
                        <strong>${row.address1 ?? '--'} ${row.address2 ?? ''}</strong>
                    </div>

                    <div class="info-item">
                        <span><i class="fa fa-location-crosshairs"></i> Landmark</span>
                        <strong>${row.landmark ?? '--'}</strong>
                    </div>

                    <div class="row g-3 mt-1">

                        <div class="col-6">
                            <div class="mini-box">
                                <label>City</label>
                                <div>${row.city_name ?? '--'}</div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mini-box">
                                <label>District</label>
                                <div>${row.district_name ?? '--'}</div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mini-box">
                                <label>State</label>
                                <div>${row.state_name ?? '--'}</div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mini-box">
                                <label>Country</label>
                                <div>${row.country_name ?? 'India'}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mini-box">
                                <label>Pincode</label>
                                <div>${row.pincode ?? '--'}</div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>`;
        });

        $('#addressCards').html(html);

    }

    function renderBanks(banks) {

        if (!banks || !banks.length) {
            $('#bankCards').html(`
            <div class="col-12">
                <div class="alert alert-warning rounded-4 shadow-sm">
                    <i class="fa fa-circle-info me-2"></i>No Bank Accounts Available
                </div>
            </div>
        `);
            return;
        }

        let html = '';

        $.each(banks, function(i, row) {

            html += `
        <div class="col-lg-6">
            <div class="modern-card bank-card">

                <div class="modern-header">

                    <div class="avatar-circle bg-warning text-dark">
                        <i class="fa fa-building-columns"></i>
                    </div>

                    <div class="flex-grow-1 ms-3">
                        <h5>${row.bank_name ?? '--'}</h5>
                        <small>${row.branch_name ?? 'Main Branch'}</small>
                    </div>

                    ${row.is_primary==1?'<span class="badge bg-success rounded-pill px-3">Primary</span>':''}

                </div>

                <div class="modern-body">

                    <div class="info-item">
                        <span><i class="fa fa-user"></i>&nbsp&nbsp; Account Holder Name</span>
                        <strong>${row.account_holder ?? '--'}</strong>
                    </div>

                    <div class="info-item">
                        <span><i class="fa fa-credit-card"></i>&nbsp&nbsp; Account Number</span>
                        <strong>${row.account_number ?? '--'}</strong>
                    </div>

                    <div class="row g-3 mt-1">

                        <div class="col-6">
                            <div class="mini-box">
                                <label>IFSC</label>
                                <div>${row.ifsc ?? '--'}</div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mini-box">
                                <label>UPI ID</label>
                                <div>${row.upi_id ?? '--'}</div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>`;
        });

        $('#bankCards').html(html);

    }

    function renderTax(tax) {

        if (!tax) {
            $('#taxDetails').html(`
            <div class="alert alert-warning rounded-4 shadow-sm">
                <i class="fa fa-circle-info me-2"></i>No Tax Details Available
            </div>
        `);
            return;
        }

        $('#taxDetails').html(`

        <div class="modern-card">

            <div class="modern-header">

                <div class="avatar-circle bg-dark">
                    <i class="fa fa-file-invoice"></i>
                </div>

                <div class="ms-3">
                    <h5>Tax Information</h5>
                    <small>Government Registration Details</small>
                </div>

            </div>

            <div class="modern-body">

                <div class="row g-3">

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>GST Number</label>
                            <div>${tax.gst_number ?? '--'}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>PAN Number</label>
                            <div>${tax.pan_number ?? '--'}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>TAN Number</label>
                            <div>${tax.tan_number ?? '--'}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>CIN Number</label>
                            <div>${tax.cin_number ?? '--'}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>MSME Number</label>
                            <div>${tax.msme_number ?? '--'}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-box">
                            <label>Trade Licence</label>
                            <div>${tax.trade_license_number ?? '--'}</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mini-box">
                            <label>GST Registered Name</label>
                            <div>${tax.gst_registered_name ?? '--'}</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mini-box">
                            <label>GST Registered Address</label>
                            <div>${tax.gst_registered_address ?? '--'}</div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        `);

    }

    function renderDocuments(documents) {

        if (!documents || !documents.length) {
            $('#documentCards').html(`
            <div class="col-12">
                <div class="alert alert-warning rounded-4 shadow-sm">
                    <i class="fa fa-circle-info me-2"></i>No Documents Available
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
                <a href="/${row.file_path}" target="_blank" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="fa fa-eye me-1"></i>View File
                </a>
            `;
            } else {
                fileBtn = `<span class="text-muted">No File Uploaded</span>`;
            }

            html += `
        <div class="col-lg-6">

            <div class="modern-card document-card">

                <div class="modern-header">

                    <div class="avatar-circle bg-secondary">
                        <i class="fa fa-file-lines"></i>
                    </div>

                    <div class="flex-grow-1 ms-3">
                        <h5>${row.document_name ?? '--'}</h5>
                        <small>Document Information</small>
                    </div>

                </div>

                <div class="modern-body">

                    <div class="info-item">
                        <span><i class="fa fa-hashtag"></i>Document Number</span>
                        <strong>${row.document_number ?? '--'}</strong>
                    </div>

                    <div class="row g-3 mt-1">

                        <div class="col-6">
                            <div class="mini-box">
                                <label>Issue Date</label>
                                <div>${row.issue_date ?? '--'}</div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mini-box">
                                <label>Expiry Date</label>
                                <div>${row.expiry_date ?? '--'}</div>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        ${fileBtn}
                    </div>

                </div>

            </div>

        </div>`;
        });

        $('#documentCards').html(html);

    }
</script>
@endpush