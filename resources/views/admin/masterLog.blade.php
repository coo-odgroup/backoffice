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


        <table class="log-table"  id="logTable">

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
let table;

$(document).ready(function() {

    table = $('#logTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/master-log-list',
            type: 'POST',
            data: function(d) {
                d.txtSearch = $('#txtSearch').val();
                d.from_date = $('#from_date').val();
                d.to_date   = $('#to_date').val();
                d.action    = $('#action').val();
                d._token    = $('meta[name="csrf-token"]').attr('content');
            },
            dataSrc: function(res) {

                // 🔢 COUNTS
                $('.total-count').text(res.counts.total);
                $('.create-count').text(res.counts.creates);
                $('.update-count').text(res.counts.updates);
                $('.delete-count').text(res.counts.deletes);

                return res.data;
            }
        },

        columns: [
            { data: 'table_name' },
            { data: 'record_id' },
            { data: 'action_badge', orderable: false },
            { data: 'created_by_name', defaultContent: 'System' },
            { data: 'created_date' },
            {
                data: 'id',
                orderable: false,
                render: function(data) {
                    return `<button class="fa fa-eye view_btn" onclick="viewLog(${data})"></button>`;
                }
            }
        ]
    });

    // 🔍 SEARCH
    $('#btnSearch').click(function() {
        table.ajax.reload();
    });

    // 🔄 RESET
    $('#btnReset').click(function() {
        $('#txtSearch').val('');
        $('#from_date').val('');
        $('#to_date').val('');
        $('#action').val('');
        table.ajax.reload();
    });

});