@extends('admin.layouts.master')
@section('page_title', 'Cities')
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
        <li class="breadcrumb-item active">Cities</li>
    </ol>
</nav>

<!-- Card Design -->
<div class="row g-4 mt-2 dashboard-panels">

    <div class="col-xl-3 col-lg-3 col-md-6">
        <a href="#" class="panel-link">
            <div class="panel-card">
                <div class="panel-icon-box operator">
                    <i class="fas fa-bus"></i>
                </div>
                <div class="panel-title">Operator Panel</div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6">
        <a href="#" class="panel-link">
            <div class="panel-card">
                <div class="panel-icon-box agent">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="panel-title">Agent Panel</div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6">
        <a href="#" class="panel-link">
            <div class="panel-card">
                <div class="panel-icon-box user">
                    <i class="fas fa-users"></i>
                </div>
                <div class="panel-title">User Panel</div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6">
        <a href="#" class="panel-link">
            <div class="panel-card">
                <div class="panel-icon-box admin">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="panel-title">Admin Panel</div>
            </div>
        </a>
    </div>

</div>
@endsection
@push('scripts')

@endpush
