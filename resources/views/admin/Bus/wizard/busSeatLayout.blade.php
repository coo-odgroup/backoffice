@extends('admin.layouts.master')
@section('page_title', 'Bus Seat Layout')
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
        <!-- <button type="button" id="btnToggleFilter" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-magnifying-glass me-1"></i>
            <span class="btn-text">Filter</span>
        </button> -->
        <a href="{{ route('seatlayout.index') }}" class="btn btn-success btn-sm">
            Back
        </a>
    </div>
</div>

<div class="seat-main-card">

    <!-- LEFT : SEAT LAYOUT -->
    <div class="seat-left">

        <!-- TAB BUTTONS -->
        <div class="seat-tabs">
            <button type="button" class="seat-tab-btn active" data-target="upper-berth-box">Upper Berth</button>
            <button type="button" class="seat-tab-btn" data-target="lower-berth-box">Lower Berth</button>
        </div>

        <div class="bus-layout">

            <!-- UPPER BERTH -->
            @if(!empty($layout['UPPER']))
            <div class="berth-row" id="upper-berth-box">
                <div class="berth-label">Upper Berth</div>
                <div class="layout-box" style="grid-template-columns: repeat({{ $maxCols['UPPER'] }}, 42px);">

                    @foreach($layout['UPPER'] as $row)
                    @foreach($row as $seat)

                    @if($seat->seat_class == 0 || $seat->seat_text == null)
                    <div class="empty-seat"></div>

                    @elseif($seat->seat_class == 2)
                    <label class="seat-wrap sleeper-wrap">
                        <span class="bus-sleeper"></span>
                        <span class="seat-number">{{ $seat->seat_text }}</span>
                    </label>

                    @else
                    <label class="seat-wrap">
                        <span class="bus-seat"></span>
                        <span class="seat-number">{{ $seat->seat_text }}</span>
                    </label>
                    @endif

                    @endforeach
                    @endforeach

                </div>
            </div>
            @endif


            <!-- LOWER BERTH -->
            @if(!empty($layout['LOWER']))
            <div class="berth-row" id="lower-berth-box">
                <div class="berth-label">Lower Berth</div>
                <div class="layout-box" style="grid-template-columns: repeat({{ $maxCols['LOWER'] }}, 42px);">

                    @php
                       $skip = [];
                    @endphp

                    @foreach($layout['LOWER'] as $rIndex => $row)
                    @foreach($row as $cIndex => $seat)

                     @php
                        // Check if this cell should be skipped
                        if (isset($skip[$rIndex][$cIndex])) {
                            continue;
                        }
                    @endphp

                    @if($seat->seat_class == 0 || $seat->seat_text == null)
                    <div class="empty-seat"></div>

                    @elseif($seat->seat_class == 3)
                    @php
                        $isExit = strtoupper($seat->seat_text) === 'EXIT';
                        $isToilet = strtoupper($seat->seat_text) === 'TOILET';
                    @endphp

                    <label class="seat-wrap vertical-sleeper-wrap">

                        @if($isExit)
                            <span class="vertical_exit_prv"></span>

                        @elseif($isToilet)
                            <span class="vertical_toilet_prv"></span>

                        @else
                            <span class="bus-vertical-sleeper"></span>
                        @endif
                    <!-- <label class="seat-wrap vertical-sleeper-wrap">
                        <span class="bus-vertical-sleeper"></span> -->
                        <span class="seat-number">{{ $seat->seat_text }}</span>
                    </label>
                    @php
                        // Mark next row same column as skipped
                        $skip[$rIndex + 1][$cIndex] = true;
                    @endphp

                    @elseif($seat->seat_class == 2)
                    <label class="seat-wrap sleeper-wrap">
                        <span class="bus-sleeper"></span>
                        <span class="seat-number">{{ $seat->seat_text }}</span>
                    </label>

                    @else
                    <label class="seat-wrap">
                        <span class="bus-seat"></span>
                        <span class="seat-number">{{ $seat->seat_text }}</span>
                    </label>
                    @endif

                    @endforeach
                    @endforeach

                </div>
            </div>
            @endif


        </div>
    </div>

</div>

<!-- TABLE -->

@endsection
@push('scripts')

<script type="module">
    window.bulkActionUrl = "{{ route('admin.bulkAction') }}";

    $('#backoffice-form').on('submit', function(e) {
        e.preventDefault();
    });

    $(document).ready(function() {
        commonAjax.initTableCheckbox('#checkboxall', '.chkItem');
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

        if ($('#txtSearch').val() != '') {
            txtSearch = $('#txtSearch').val();
        }
        if ($('#selStatus').val() != '') {
            selStatus = $('#selStatus').val();
        }

        let tableId = 'datatable';
        let orderBy = [2, 'asc'];
        let searchParams = {
            txtsearch: txtSearch,
            selstatus: selStatus
        };
        let displayColumns = [1, 2, 3, 4, 5, 6];
        let dataTableColumns = [{
                data: '',
                render: function(data, type, row) {
                    return '<input class="form-check-input chkItem" type="checkbox" id="check' + row.bustype_id +
                        '" name="chkStd' + row.bustype_id + '" value="' + row.bustype_id +
                        '" >';
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
                data: 'bus_type',
                defaultContent: "--"
            },
            {
                data: 'class_id',
                defaultContent: "--",
                render: function(data, type, row) {
                    if (data == 1) {
                        return 'AC';
                    } else if (data == 2) {
                        return 'NON AC';
                    } else {
                        return '--';
                    }
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
                        <small
                            class="text-primary fw-semibold"
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
                        </small>
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
                render: function(data, type, row) {

                    let editUrl = $('#' + tableId).data('edit-url');

                    if (!editUrl) return '';

                    return `
                        <a class="btn btn-sm btn-info"
                        href="${editUrl.replace('ID', row.enc_bustype_id)}">
                        <i class="fa fa-edit"></i> Edit
                        </a>

                        <a href="javascript:void(0);"
                            class="btn btn-sm btn-success btn-view-log"
                            data-table="mst_bus_type"
                            data-id="${row.enc_bustype_id}">
                                <i class="fa fa-history"></i> View Log
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