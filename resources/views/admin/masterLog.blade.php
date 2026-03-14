@extends('admin.layouts.master')
@section('page_title', 'Cities')

@section('content')

<?php $page_name = 'All ' . trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y']; ?>

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
            <div class="log-stat-number">19</div>
        </div>

        <div class="log-stat-card green">
            <div class="log-stat-title">CREATES</div>
            <div class="log-stat-number">0</div>
        </div>

        <div class="log-stat-card blue">
            <div class="log-stat-title">UPDATES</div>
            <div class="log-stat-number">19</div>
        </div>

        <div class="log-stat-card red">
            <div class="log-stat-title">DELETES</div>
            <div class="log-stat-number">0</div>
        </div>

    </div>


    <!-- SEARCH -->
    <div class="log-search-box">

        <input type="text" placeholder="Search by product ID, user, app, or field name...">

        <button class="log-search-btn">
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

        <select>
            <option>All types</option>
        </select>

        <select>
            <option>All events</option>
        </select>

        <select>
            <option>All users</option>
        </select>

        <input type="date">
        <input type="date">

        <button class="log-clear-btn">
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


        <table class="log-table">

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