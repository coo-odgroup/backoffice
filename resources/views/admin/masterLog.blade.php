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

<div class="audit-wrapper">

    <!-- HEADER -->
    <div class="audit-top-card">

        <div class="row align-items-center">

            <div class="col-md-8">

                <div class="audit-left">

                    <img src="https://i.pravatar.cc/100">

                    <div>
                        <div class="audit-title">Central Audit Log Monitor</div>
                        <div class="audit-sub">Track all master table changes by team members</div>
                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="audit-stats">

                    <div class="stat-box">
                        <div class="stat-number text-success">284</div>
                        <div class="stat-label">CHANGES TODAY</div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-number text-info">803</div>
                        <div class="stat-label">UPDATES</div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-number text-danger">1207</div>
                        <div class="stat-label">INSERTS</div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-number text-danger">32</div>
                        <div class="stat-label">DELETES</div>
                    </div>

                </div>

                <div class="audit-info">

                    <div class="info-box"><i class="fas fa-map-marker-alt"></i> Boston</div>
                    <div class="info-box">Rate: $250</div>
                    <div class="info-box"><i class="fas fa-credit-card"></i> Payment Logs</div>
                    <div class="info-box"><i class="fas fa-cogs"></i> System Logs</div>

                </div>

            </div>

        </div>

    </div>


    <!-- TABS -->
    <div class="audit-tabs">
        <ul>
            <li class="active">Overview</li>
            <li>Master Data Logs</li>
            <li>Booking Logs</li>
            <li>Payment Logs</li>
            <li>System Logs</li>
        </ul>
    </div>


    <!-- MAIN DASHBOARD -->
    <div class="row mt-4">

        <!-- LEFT -->
        <div class="col-lg-8">

            <div class="row g-4">

                <div class="col-12">
                    <div class="audit-card">

                        <div class="card-title">Audit Log Activity (Last 7 Days)</div>

                        <div class="graph-box">
                            <img src="/assets/img/presentation_chart.jpg" alt="chart">
                        </div>

                    </div>
                </div>


                <div class="col-md-6">

                    <div class="audit-card">

                        <div class="card-title">Recently Modified Tables</div>

                        <div class="item-row">

                            <div class="item-left">
                                <i class="fas fa-folder text-warning"></i>
                                <div>
                                    <div class="item-title">mst_cities</div>
                                    <div class="item-sub">Updated 15 times today</div>
                                </div>
                            </div>

                            <div class="item-right">
                                <span>Updated 9 times</span>
                                <i class="fas fa-chevron-right"></i>
                            </div>

                        </div>


                        <div class="item-row">

                            <div class="item-left">
                                <i class="fas fa-folder text-warning"></i>
                                <div>
                                    <div class="item-title">mst_districts</div>
                                    <div class="item-sub">Updated 9 times today</div>
                                </div>
                            </div>

                            <div class="item-right">
                                <span>Updated 9 times</span>
                                <i class="fas fa-chevron-right"></i>
                            </div>

                        </div>


                        <div class="item-row">

                            <div class="item-left">
                                <i class="fas fa-folder text-warning"></i>
                                <div>
                                    <div class="item-title">mst_routes</div>
                                    <div class="item-sub">Updated 7 times today</div>
                                </div>
                            </div>

                            <div class="item-right">
                                <span>Updated 7 times</span>
                                <i class="fas fa-chevron-right"></i>
                            </div>

                        </div>

                    </div>

                </div>



                <div class="col-md-6">

                    <div class="audit-card">

                        <div class="card-title">Active Admin Users</div>


                        <div class="item-row">

                            <div class="item-left">
                                <img src="https://i.pravatar.cc/40">
                                <div>
                                    <div class="item-title">Admin</div>
                                    <div class="item-sub">15 changes</div>
                                </div>
                            </div>

                            <div class="item-right">
                                <span class="user-count">15 Changes</span>
                                <span class="badge bg-success fa fa-plus"></span>
                            </div>

                        </div>


                        <div class="item-row">

                            <div class="item-left">
                                <img src="https://i.pravatar.cc/41">
                                <div>
                                    <div class="item-title">Manager</div>
                                    <div class="item-sub">9 changes</div>
                                </div>
                            </div>

                            <div class="item-right">
                                <span class="user-count">9 Changes</span>
                                <span class="badge bg-danger fa fa-minus"></span>
                            </div>

                        </div>


                        <div class="item-row">

                            <div class="item-left">
                                <img src="https://i.pravatar.cc/42">
                                <div>
                                    <div class="item-title">Operator</div>
                                    <div class="item-sub">4 changes</div>
                                </div>
                            </div>

                            <div class="item-right">
                                <span class="user-count">4 Changes</span>
                                <span class="badge bg-success fa fa-plus"></span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- RIGHT -->
        <div class="col-lg-4">

            <div class="audit-card mb-4">

                <div class="card-title">Recent Activity</div>

                <div class="item-row">

                    <div class="item-left">
                        <img src="https://i.pravatar.cc/40">
                        <div>
                            <div class="item-title">Admin updated mst_cities</div>
                            <div class="item-sub">2 minutes ago</div>
                        </div>
                    </div>

                    <i class="fas fa-pen text-success"></i>

                </div>


                <div class="item-row">

                    <div class="item-left">
                        <img src="https://i.pravatar.cc/41">
                        <div>
                            <div class="item-title">Manager deleted mst_routes</div>
                            <div class="item-sub">1 hour ago</div>
                        </div>
                    </div>

                    <i class="fas fa-trash text-danger"></i>

                </div>


                <div class="item-row">

                    <div class="item-left">
                        <img src="https://i.pravatar.cc/42">
                        <div>
                            <div class="item-title">Operator updated mst_boarding_points</div>
                            <div class="item-sub">2 hours ago</div>
                        </div>
                    </div>

                    <i class="fas fa-pen text-success"></i>

                </div>

            </div>



            <div class="audit-card">

                <div class="card-title">System Alerts</div>

                <div class="item-row">

                    <div class="item-left">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <span>50 records deleted today</span>
                    </div>

                    <div class="item-right">
                        <span class="alert-time">1 hour ago</span>
                    </div>

                </div>

                <div class="item-row">

                    <div class="item-left">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <span>Over 100 updates from IP</span>
                    </div>

                    <div class="item-right">
                        <span class="alert-time">3 hours ago</span>
                    </div>

                </div>

                <div class="item-row">
                    <div class="item-left">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <span>High deletion detected</span>
                    </div>

                    <div class="item-right">
                        <span class="alert-time">5 hours ago</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection