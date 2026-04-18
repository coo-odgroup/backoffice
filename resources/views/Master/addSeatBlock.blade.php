    @extends('admin.layouts.master')
    @section('page_title', 'Seat Block')
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
            <li class="breadcrumb-item active">{{ $data['strPage'] }} @yield('page_title')</li>
        </ol>
    </nav>

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 id="page_title">@yield('page_title')</h5>
        <div>
            <a href="{{ route('seat-block.index') }}" class="btn btn-success btn-sm">
                View @yield('page_title')
            </a>
        </div>
    </div>

    <form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form">
        {{csrf_field()}}

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="mb-3">
                            <div class="card-body">
                                <div class="row">

                                    <!-- Alerts -->
                                    @if (session('message'))
                                    <div class="alert alert-{{ session('level') ?? 'success' }} alert-dismissible fade show">
                                        {{ session('message') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    @endif

                                    @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    @endif

                                    <div class="col-12">
                                        <div class="row">

                                            <!-- LEFT COLUMN -->
                                            <div class="col-md-5">
                                                <div class="p-3 border rounded bg-white">
                                                    <div class="mb-2">
                                                        <label for="operator">Operator <span class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm" id="operator" name="operator"></select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="bus">Bus <span class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm" id="bus" name="bus"></select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="reason">Reason <span class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm" id="reason" name="reason"></select>
                                                    </div>
                                                </div>

                                                <div class="p-3 border rounded bg-white mt-2">
                                                    <table class="table table-hover table-bordered align-middle table-sm table-responsive">
                                                        <thead class="table-secondary">
                                                            <tr>
                                                                <th>Sl No.</th>
                                                                <th>Date</th>
                                                                <th>Seats Blocked</th>
                                                                <th>Reason</th>
                                                                <th>Cacelled By</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>1</td>
                                                                <td>03-Apr-2026</td>
                                                                <td>5,6,SL3,SL4,SL5,SL6,SL7</td>
                                                                <td>Request From Owner</td>
                                                                <td>John Doe<br>23-Apr-2026 10:45:47</td>
                                                            </tr>
                                                            <tr>
                                                                <td>2</td>
                                                                <td>03-Apr-2026</td>
                                                                <td>5,6,SL3,SL4,SL5,SL6,SL7</td>
                                                                <td>Request From Owner</td>
                                                                <td>John Doe<br>23-Apr-2026 10:45:47</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>


                                            <div class="col-md-7">
                                                <div class="border rounded bg-white">
                                                    <div class="card-header">
                                                        <strong>Schedule Date List</strong>
                                                    </div>

                                                    <div class="card-body" id="scheduleContainer">
                                                        <div class="text-center text-muted">
                                                            Please select operator and bus
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="border rounded bg-white mt-2">
                                                    <div class="card-header">
                                                        <strong>Seat layout: [ Seat layout Name ]</strong>
                                                    </div>

                                                    <div class="card-body">

                                                        <div class="bus-layout">
                                                            <div class="berth-row">
                                                                <div class="berth-label">Upper Berth</div>
                                                                <div class="layout-box" style="grid-template-columns: repeat(10, 42px);">
                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL11</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL12</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL13</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL14</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL15</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL6</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL7</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL8</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL9</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL10</span>
                                                                    </label>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL1</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL2</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL3</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL4</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="bus-sleeper"></span>
                                                                        <span class="seat-number">SL5</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="berth-row">
                                                                <div class="berth-label">Lower Berth</div>
                                                                <div class="layout-box" style="grid-template-columns: repeat(10, 42px);">
                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">20</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">21</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">22</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">23</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">24</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">25</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">26</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">27</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">28</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">29</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">10</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">11</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">12</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">13</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">14</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">15</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">16</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">17</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">18</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">19</span>
                                                                    </label>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <div class="empty-seat"></div>
                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">9</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">1</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">2</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">3</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">4</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">5</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">6</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">7</span>
                                                                    </label>

                                                                    <label class="seat-wrap">
                                                                        <span class="bus-seat"></span>
                                                                        <span class="seat-number">8</span>
                                                                    </label>

                                                                    <label class="seat-wrap sleeper-wrap">
                                                                        <span class="horizontal_exit_prv"></span>
                                                                        <span class="seat-number">EXIT</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="row mt-2">
                                        <div class="col-12 d-flex gap-2">
                                            <button class="btn btn-primary btn-sm" type="submit">
                                                {{ $data['strSubmit'] }}
                                            </button>
                                            <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                                {{ $data['strReset'] }}
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
    </form>

    @endsection

    @push('scripts')
    <script type="module">
        $(document).ready(function() {

            commonAjax.initSelect2('#operator', 'Select Operator');
            commonAjax.initSelect2('#bus', 'Select Bus');
            commonAjax.initSelect2('#reason', 'Select Reason');

            commonAjax.loadBusOperatorDropdown('');
            commonAjax.loadAnnextureList('REASON', '', '#reason');

        });


        $('#operator').on('change', function() {

            let operator_id = $(this).val();

            $('#bus').html('');
            $('#scheduleContainer').html(`
        <div class="text-center text-muted">
            Please select bus
        </div>
    `);

            commonAjax.loadBusListByOperator('#bus', operator_id);

        });
 

        /* Bus Change */
        $('#bus').on('change', function() {

            loadSeatBlockSchedules();

        });



        function loadSeatBlockSchedules() {
            let operator = $('#operator').val();
            let bus = $('#bus').val();

            if (!operator || !bus) {
                $('#scheduleContainer').html(`
            <div class="text-center text-muted">
                Please select operator and bus
            </div>
        `);
                return;
            }

            let today = new Date();
            let year = today.getFullYear();
            let month = today.getMonth() + 1;

            $('#scheduleContainer').html(`
        <div class="text-center p-4">
            <div class="spinner-border text-primary"></div>
            <p>Loading schedules...</p>
        </div>
    `);


            $.ajax({
                type: 'POST',
                url: '/admin/get-bus-schedule-by-month',
                data: {
                    operator_id: operator,
                    bus_ids: bus,
                    year: year,
                    month: month,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function(res) {
                    if (!res.status || !res.data) {
                        $('#scheduleContainer').html(`
                    <div class="text-danger text-center">
                        No Schedule Found
                    </div>
                `);
                        return;
                    }

                    renderSchedule(res.data);
                }
            });

        }



        function renderSchedule(data) {
            let html = '';

            Object.keys(data).forEach(function(bus_id) {

                let bus = data[bus_id];

                html += `
            <div class="mb-4">
                <div class="mb-2">
                    <strong>${bus.bus_name} | ${bus.bus_number}</strong>
                </div>

                <div class="row">
        `;

                bus.dates.forEach(function(date) {

                    html += `
                <div class="col-md-4 mb-2">
                    <label class="w-100 border rounded p-2 text-center">

                        <input type="checkbox"
                               name="dates[]"
                               value="${date}"
                               class="schedule-checkbox"
                               data-bus="${bus_id}">

                        ${formatDate(date)}

                    </label>
                </div>
            `;
                });

                html += `
                </div>
            </div>
        `;

            });

            $('#scheduleContainer').html(html);
        }



        function formatDate(dateStr) {
            let d = new Date(dateStr);

            return d.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }
    </script>
    @endpush