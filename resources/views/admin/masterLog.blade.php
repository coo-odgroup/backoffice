@extends('admin.layouts.master')
@section('page_title', 'Cities')
@section('content')

<?php $page_name = 'All ' . trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y']; ?>
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">Cities</li>
    </ol>
</nav>

<div class="log-wrapper">

    <!-- PAGE HEADER -->
    <div class="log-header">

        <div class="log-header-left">
            <div class="log-avatar">A</div>

            <div>
                <div class="log-title">Audit Log</div>
                <div class="log-sub">Minimumorder</div>
            </div>
        </div>

        <button class="log-track-btn d-flex align-items-center gap-2">
            <i class="fa-regular fa-clipboard"></i>
            Tracking 19 events
        </button>

    </div>


    <!-- STAT CARDS -->
    <div class="log-stats">

        <div class="log-stat-card">
            <div class="log-stat-title">TOTAL CHANGES</div>
            <div class="log-stat-number total-count">0</div>
        </div>

        <div class="log-stat-card green">
            <div class="log-stat-title">CREATES</div>
            <div class="log-stat-number create-count">0</div>
        </div>

        <div class="log-stat-card blue">
            <div class="log-stat-title">UPDATES</div>
            <div class="log-stat-number update-count">0</div>

        </div>

        <div class="log-stat-card red">
            <div class="log-stat-title">DELETES</div>
            <div class="log-stat-number delete-count">0</div>
        </div>

    </div>


    <!-- SEARCH -->
    <div class="log-search-box">

        <input type="text" id="txtSearch" placeholder="Search by product ID, user, app, or field name...">

        <button class="log-search-btn" id="btnSearch">
            Search
        </button>
        <div class="log-quick-filter">
            <span>Last 24h</span>
            <span>7 days</span>
            <span>30 days</span>
            <span>1 year</span>
        </div>

    </div>




    <!-- FILTERS -->
    <div class="log-filters">

        <select id="action">
            <option value="">All events</option>
            <option value="INSERT">Create</option>
            <option value="UPDATE">Update</option>
            <option value="DELETE">Delete</option>
            <option value="SOFT_DELETE">Soft Delete</option>
            <option value="STATUS_CHANGE">Status Change</option>
        </select>


        <select>
            <option>All users</option>
        </select>

        <input type="date" id="from_date">
        <input type="date" id="to_date">

        <button class="log-clear-btn" id="btnReset">
            Clear All
        </button>

    </div>


    <!-- TABLE -->
    <div class="log-table-card">

        <div class="log-table-header">

            <div>
                <strong>Audit Entries</strong>
                <span>19 total</span>
            </div>

            <div>
                <button class="log-refresh-btn"><i class="fa fa-sync"></i>Refresh</button>
                <button class="log-export-btn"><i class="fa fa-file-export"></i>Export CSV</button>
            </div>

        </div>


        <table class="log-table" id="logTable">

            <thead>
                <tr>
                    <th>TYPE</th>
                    <th>ENTITY</th>
                    <th>EVENT</th>
                    <th>CHANGED BY</th>
                    <th>TIMESTAMP</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>Product</td>
                    <td>10078814404740</td>
                    <td><span class="log-badge blue">update</span></td>
                    <td>Staff Member</td>
                    <td>3 weeks ago</td>
                    <td>
                        <button class="fa fa-chevron-down view_btn"></button>
                        <button class="fa fa-eye view_btn"></button>
                    </td>
                </tr>

                <tr>
                    <td>Inventory</td>
                    <td>53011545096324</td>
                    <td><span class="log-badge blue">update</span></td>
                    <td>Unknown</td>
                    <td>3 weeks ago</td>
                    <td>
                        <button class="fa fa-chevron-down view_btn"></button>
                        <button class="fa fa-eye view_btn"></button>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>
@endsection

<script>
    // LOAD DATA
    function loadAuditLogs(page = 1) {

        let range = $('.log-quick-filter span.active').data('range') || '';

        $.ajax({
            url: "/admin/audit-logs-data?page=" + page,
            type: "GET",
            data: {
                search: $('#txtSearch').val(),
                action: $('#action').val(),
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                range: range
            },
            success: function(res) {

                // STATS
                $('.total-count').text(res.stats.total);
                $('.create-count').text(res.stats.create);
                $('.update-count').text(res.stats.update);
                $('.delete-count').text(res.stats.delete);

                // TABLE
                let html = '';

                if (res.data.data.length === 0) {
                    html = `<tr><td colspan="6" class="text-center">No data found</td></tr>`;
                }

                res.data.data.forEach(row => {

                    let badgeClass = 'blue';
                    if (row.action === 'INSERT') badgeClass = 'green';
                    else if (row.action === 'UPDATE') badgeClass = 'blue';
                    else if (row.action === 'DELETE') badgeClass = 'red';
                    else badgeClass = 'gray';
                    html += `
                <tr>
                    <td>${row.table_name}</td>
                    <td>${row.record_id}</td>
                    <td><span class="log-badge ${badgeClass}">${row.action}</span></td>
                    <td>${row.created_by ?? '--'}</td>
                    <td>${function formatDate(date) {
                            let d = new Date(date);
                            let now = new Date();

                            let diff = (now - d) / 1000;

                            if (diff < 60) return "Just now";
                            if (diff < 3600) return Math.floor(diff / 60) + " min ago";
                            if (diff < 86400) return Math.floor(diff / 3600) + " hrs ago";

                            return d.toLocaleDateString();
                        }}</td>
                    <td>
                        <button class="btn btn-sm btn-info viewDetails" data-id="${row.id}">
                            <i class="fa fa-eye"></i>
                        </button>
                    </td>
                </tr>`;
                });

                $('#logTable tbody').html(html);

                // ✅ UPDATE COUNT HEADER
                $('.log-table-header span').text(res.stats.total + ' total');

                // ✅ PAGINATION (simple)
                renderPagination(res.data);
            }
        });
    }

    // 📅 FORMAT DATE
    function formatDate(date) {
        return new Date(date).toLocaleString();
    }

    // 📄 PAGINATION
    function renderPagination(pagination) {

        let html = '';

        for (let i = 1; i <= pagination.last_page; i++) {
            html += `<button class="btn btn-sm ${i === pagination.current_page ? 'btn-primary' : 'btn-light'} page-btn" data-page="${i}">
                    ${i}
                 </button>`;
        }

        $('#customPagination').html(html);
    }


    // 🔍 Search
    $('#btnSearch').click(() => loadAuditLogs());

    // ⌨️ Enter key search
    $('#txtSearch').on('keyup', function(e) {
        if (e.key === 'Enter') loadAuditLogs();
    });

    // 🔁 Refresh
    $('.log-refresh-btn').click(() => loadAuditLogs());

    // ❌ Clear Filters
    $('#btnReset').click(function() {
        $('#txtSearch').val('');
        $('#action').val('');
        $('#from_date').val('');
        $('#to_date').val('');
        $('.log-quick-filter span').removeClass('active');
        loadAuditLogs();
    });

    // 📅 Quick Filters
    $('.log-quick-filter span').click(function() {

        $('.log-quick-filter span').removeClass('active');
        $(this).addClass('active');

        let text = $(this).text().toLowerCase();

        if (text.includes('24')) $(this).data('range', '24h');
        else if (text.includes('7')) $(this).data('range', '7d');
        else if (text.includes('30')) $(this).data('range', '30d');
        else if (text.includes('year')) $(this).data('range', '1y');

        loadAuditLogs();
    });

    // 📄 Pagination click
    $(document).on('click', '.page-btn', function() {
        let page = $(this).data('page');
        loadAuditLogs(page);
    });

    // 👁 VIEW DETAILS
    $(document).on('click', '.viewDetails', function() {

        let id = $(this).data('id');

        $.get('/admin/audit-log/' + id, function(res) {

            alert(JSON.stringify(res, null, 2)); // replace with modal later
        });
    });

    // 📥 EXPORT CSV
    $('.log-export-btn').click(function() {
        window.location.href = "/admin/audit-logs-export";
    });

    // 🚀 INITIAL LOAD
    $(document).ready(function() {
        loadAuditLogs();
    });