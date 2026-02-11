@extends('admin.layouts.master')

@section('title', 'Users')

@section('content')
<!-- Loading Container -->
<!-- @include('admin.inc.loader') -->


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
                <h5>Booking Report</h5>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="toggleFilter()">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                    </button>
                    <button class="btn btn-success btn-sm">+ Add Bus
                    </button>
                </div>
            </div>

            <!-- TABLE -->
            <div class="card">
                <div class="card-body">
                    <!-- FILTER -->
                    <div class="mb-3 border-bottom" id="filterBox">
                        <div class="card-body">
                            <div class="row">
                                <!-- FILTER FIELDS -->
                                <div class="col-12">
                                    <div class="row">

                                        <div class="col-12 col-sm-12 col-md-4 col-lg-2 mb-2">
                                            <label>Select Operator</label>
                                            <select class="form-control">
                                                <option>Select Operator</option>
                                            </select>
                                        </div>

                                        <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                                            <label>From Date</label>
                                            <input type="date" class="form-control">
                                        </div>

                                        <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                                            <label>To Date</label>
                                            <input type="date" class="form-control">
                                        </div>

                                        <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                                            <label>Please Select</label>
                                            <select class="form-control">
                                                <option>Journey Date</option>
                                            </select>
                                        </div>

                                        <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                                            <label>Select Booking</label>
                                            <select class="form-control">
                                                <option>Select Booking From</option>
                                            </select>
                                        </div>

                                        <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                                            <label>Extra Filter</label>
                                            <select class="form-control">
                                                <option>Example</option>
                                            </select>
                                        </div>

                                        <div class="col-6 col-sm-6 col-md-4 col-lg-2 mb-2">
                                            <label>Extra Filter</label>
                                            <select class="form-control">
                                                <option>Example</option>
                                            </select>
                                        </div>


                                    </div>
                                </div>

                                <!-- BUTTONS -->
                                <div class="col-12 mt-3 d-flex justify-content-end flex-wrap action-btns">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-check me-1"></i>Submit
                                    </button>
                                    <button class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-rotate-left me-1"></i>Reset
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Table start -->
                    <div class="d-flex justify-content-between mb-2">
                        <select class="form-control page-size">
                            <option>5</option>
                            <option>10</option>
                            <option>25</option>
                        </select>

                        <div>
                            <button class="btn btn-success btn-sm">
                                <i class="fa-solid fa-file-excel me-1"></i>
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-print me-1"></i>
                            </button>
                            <button class="btn btn-warning btn-sm text-white">
                                <i class="fa-solid fa-file-pdf me-1"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-2 booking-info">
                        <div><b>Total Booking :</b> 55</div>
                        <div><b>Journey Date :</b> 25-Jan-2026</div>

                        <div class="alert alert-info mt-2 text-center">
                            ℹ Showing booking data based on selected filters
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Points</th>
                                    <th>Notes</th>
                                    <th>Notes</th>
                                    <th>Notes</th>
                                    <th>Notes</th>
                                    <th>Notes</th>
                                    <th>Notes</th>
                                    <th>Notes</th>
                                    <th>Notes</th>
                                    <th>Notes</th>
                                    <th>Notes</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>alex</td>
                                    <td>Alex Nilson</td>
                                    <td>1234</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="btn btn-info btn-sm">
                                                <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-trash me-1"></i>Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>alex</td>
                                    <td>Alex Nilson</td>
                                    <td>1234</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="btn btn-info btn-sm">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>alex</td>
                                    <td>Alex Nilson</td>
                                    <td>1234</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>power user</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="btn btn-info btn-sm">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <div></div>
                        <div><b>Total :</b> 78</div>
                    </div>

                    <ul class="pagination justify-content-end mt-2">
                        <li class="page-item disabled"><a class="page-link">Prev</a></li>
                        <li class="page-item active"><a class="page-link">1</a></li>
                        <li class="page-item"><a class="page-link">2</a></li>
                        <li class="page-item"><a class="page-link">3</a></li>
                        <li class="page-item"><a class="page-link">Next</a></li>
                    </ul>

                </div>
            </div>

@endsection

@push('scripts')



<script>
        $(document).ready(function() {
              // By default hide filter
            $("#filterBox").hide();

            // Toggle on button click
            window.toggleFilter = function () {
                $("#filterBox").slideToggle(300);
            };
           // getDataTableView();
      });
       function toggleFilter() {
            console.log("toggleFilter called");
            document.getElementById("filterBox").classList.toggle("d-none");
        }

        document.getElementById("menu-toggle").addEventListener("click", function () {
            document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
        });

     

      function getDataTableView() {
            console.log("getDataTableView called");
            txtService = '';
            selStatus  = 0;

            if($('#txtService').val() != '') {
                txtService = $('#txtService').val();
            }
            if($('#selStatus').val() != 0) {
                selStatus = $('#selStatus').val();
            }

            let tableId = 'datatable';
            let orderBy = [2, 'asc'];
            let searchParams = {
                txtservice: txtService,
                selstatus: selStatus,
            };
            let displayColumns = [1,2,3,4,5,6];
            let dataTableColumns = [
                {
                    data:'',
                    render:function (data, type, row) {
                        return '<input class="form-check-input chkItem" type="checkbox" id="check'+row.service_id+'" name="chkStd'+row.service_id+'" value="'+row.service_id+'" onclick="checkFun(this.id)">';
                    },
                    className: "noPrint text-center"
                },
                {
                    data: 'slNo',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    className: "text-center"
                },
                { data: 'service_name', defaultContent: "--"},
                { data: 'service_alias', defaultContent: "--"},
                { data: 'cat_name', defaultContent: "--"},
                {
                    data: 'is_seat_type',
                    render:function (data, type, row) {
                        if (row.is_seat_type == 1) {
                           var st = 'Yes';
                        } else if (row.is_seat_type == 2) {
                            var st = 'No';
                        } else {
                            var st = '--';
                        }
                        return '<span>'+st+'</span>';
                    },
                    className: "text-center"
                },
                {
                    data: 'is_component',
                    render:function (data, type, row) {
                        if (row.is_component == 1) {
                           var cmp = 'Yes';
                        } else if (row.is_component == 2) {
                            var cmp = 'No';
                        } else {
                            var cmp = '--';
                        }
                        return '<span>'+cmp+'</span>';
                    },
                    className: "text-center"
                },
                {
                    data:'',
                    render:function (data, type, row) {
                        if (row.cmp_count > 0) {
                            return '<a href="javascript:void(0);" onclick="getComponentList('+"'"+row.enc_service_id+"'"+')">'+row.cmp_count+'</a>';
                        } else {
                            return '--';
                        }
                    },
                    className: "noPrint text-center"
                },
                { data: 'booking_engine', defaultContent: "--", className: "text-center"},
                { data: 'created_date', defaultContent: "--", className: "text-center text-nowrap"},
                { data: 'full_name', defaultContent: "--"},
                {
                    data: 'is_active',
                    render:function (data, type, row) {
                        var cls = ((row.is_active == 'Active') ? 'badge bg-success' : 'badge bg-danger');
                        return '<span class="'+cls+'">'+row.is_active+'</span>';
                    },
                    className: "text-center"
                },
                {
                    data:'',
                    render:function (data, type, row) {
                        return '<a class="btn btn-sm btn-info" title="Edit" href="' + $('#'+tableId).data('edit-url').replace("ID", row.enc_service_id) + '"><i class="bi bi-pencil-square"></i></a>';
                    },
                    className: "noPrint text-center"
                }
            ]

            loadDataTable(tableId, dataTableColumns, orderBy, searchParams, displayColumns );
        }


</script>

@endpush
