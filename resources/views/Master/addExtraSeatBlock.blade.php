    @extends('admin.layouts.master')
    @section('page_title', 'Extra Seat Block')
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
            <a href="{{ route('extra-seat-block.index') }}" class="btn btn-success btn-sm">
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

                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="mb-3">
                                                                <strong>DILKHUS | OD 02 AS 5297 | [Bhubaneswar >> Jharsuguda]</strong>
                                                            </div>

                                                            <!-- Column 1 -->
                                                            <div class="col-4">
                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">10-Apr-2026</span>
                                                                </div>

                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">12-Apr-2026</span>
                                                                </div>

                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">14-Apr-2026</span>
                                                                </div>

                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">16-Apr-2026</span>
                                                                </div>
                                                            </div>

                                                            <!-- Column 2 -->
                                                            <div class="col-4">
                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">10-Apr-2026</span>
                                                                </div>

                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">12-Apr-2026</span>
                                                                </div>

                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">14-Apr-2026</span>
                                                                </div>

                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">16-Apr-2026</span>
                                                                </div>
                                                            </div>

                                                            <!-- Column 3 -->
                                                            <div class="col-4">
                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">10-Apr-2026</span>
                                                                </div>

                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">12-Apr-2026</span>
                                                                </div>

                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">14-Apr-2026</span>
                                                                </div>

                                                                <div class="checkbox mb-2">
                                                                    <input type="checkbox">
                                                                    <span class="form-check-label">16-Apr-2026</span>
                                                                </div>
                                                            </div>
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
        let selectedOperators = [];

        $(document).ready(function() {

            let slab_id = "{{ $data['row']['slab_id'] ?? '' }}";


            commonAjax.initSelect2('#bus', 'Select Bus');
            commonAjax.initSelect2('#operator', 'Select Operator');
            commonAjax.loadTicketFareSlabList('#slab', slab_id);
            commonAjax.loadBusOperatorList();
            commonAjax.initClearableInputs();

            $('#operator').on('change', function() {

                let id = $(this).val();
                let text = $("#operator option:selected").text();

                if (!id) return;
                if (selectedOperators.some(op => op.id == id)) return;

                let operator = {
                    id,
                    text
                };
                selectedOperators.push(operator);

                renderOperators();
                loadOperatorTable(operator);

                $(this).val('').trigger('change');
            });

            let existingOperators = @json($data['row']['operators'] ?? []);

            existingOperators.forEach(op => {
                selectedOperators.push({
                    id: op.id,
                    text: op.name
                });

                loadOperatorTable({
                    id: op.id,
                    text: op.name
                });
            });

            renderOperators();
        });

        function renderOperators() {

            let html = '';

            selectedOperators.forEach((op, index) => {
                html += `<span class="selected-tag" data-index="${index}">${op.text}<span class="remove">×</span></span>`;
            });

            $('#selectedOperators').html(html);
            $('#operator_ids').val(selectedOperators.map(op => op.id).join(','));

            $('#selectedOperatorsWrapper').toggle(selectedOperators.length > 0);
        }

        $(document).on('click', '.remove', function() {

            let index = $(this).closest('.selected-tag').data('index');
            let operator = selectedOperators[index];

            selectedOperators.splice(index, 1);
            $(`#table_${operator.id}`).remove();

            renderOperators();
        });

        $('#btnReset').click(function() {

            $('#backoffice-form')[0].reset();
            $('.form-select').val('').trigger('change');

            selectedOperators = [];
            renderOperators();
            $('#operatorTables').html('');
        });

        $('#backoffice-form').on('submit', function(e) {

            e.preventDefault();

            if (!validator.selectDropdown('slab', 'Select Ticket Fare Slab')) return;
            commonAjax.confirmAlert('Are you sure to proceed!');

            $('#btnConfirmOk').one('click', () => this.submit());
        });

        // add/remove rows
        $(document).on('click', '.btn-add', function() {
            $('#slabWrapper').append(`
            <div class="row mb-3 dynamic-item">
                <div class="col-md-2"><input type="number" name="starting_fare[]" placeholder="From Fare" class="form-control form-control-sm"></div>
                <div class="col-md-2"><input type="number" name="upto_fare[]" placeholder="To Fare" class="form-control form-control-sm"></div>
                <div class="col-md-2"><input type="number" name="commision[]" placeholder="Commission" class="form-control form-control-sm"></div>
                <div class="col-md-2"><input type="date" name="from_date[]" class="form-control form-control-sm from-date" min="{{ date('Y-m-d') }}"></div>
                <div class="col-md-2"><input type="date" name="to_date[]" class="form-control form-control-sm to-date" min="{{ date('Y-m-d') }}"></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm btn-remove mt-1">-</button></div>
            </div>
        `);
        });

        $(document).on('click', '.btn-remove', function() {
            $(this).closest('.dynamic-item').remove();
        });

        // FROM DATE CHANGE
        $(document).on('change', '.from-date', function() {

            let fromDate = $(this).val();
            let row = $(this).closest('.row');
            let toInput = row.find('.to-date');

            if (fromDate) {
                toInput.attr('min', fromDate);

                if (toInput.val() && toInput.val() < fromDate) {
                    toInput.val('');
                }
            }
        });




        // TO DATE CHANGE
        $(document).on('change', '.to-date', function() {

            let row = $(this).closest('.row');
            let fromDate = row.find('.from-date').val();
            let toDate = $(this).val();

            if (fromDate && toDate && toDate < fromDate) {
                alert('To Date cannot be less than From Date');
                $(this).val('');
            }
        });


        // UPDATED: no table if no data
        function loadOperatorTable(operator) {

            $.ajax({
                url: "/admin/get-operator-slab-data",
                type: "POST",
                data: {
                    operator_id: operator.id,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },

                success: function(res) {

                    // skip if no data
                    if (!res.status || res.data.length === 0) {
                        $(`#table_${operator.id}`).remove();
                        return;
                    }

                    let tableHtml = `
                    <div class="card mt-3 operator-table" id="table_${operator.id}">
                        <div class="card-header bg-warning">
                            <b>${operator.text}</b>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Slab</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Commission</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                    res.data.forEach(row => {
                        tableHtml += `
                        <tr>
                            <td>${row.slab_name}</td>
                            <td>${row.starting_fare}</td>
                            <td>${row.upto_fare}</td>
                            <td>${row.commision}</td>
                            <td>${row.from_date}</td>
                            <td>${row.to_date}</td>
                        </tr>`;
                    });

                    tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    </div>`;

                    $(`#table_${operator.id}`).remove();
                    $('#operatorTables').append(tableHtml);
                }
            });
        }
    </script>
    @endpush