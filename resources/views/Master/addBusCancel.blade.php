    @extends('admin.layouts.master')
    @section('page_title', 'Bus Cancel')
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
            <a href="{{ route('bus-cancel.index') }}" class="btn btn-success btn-sm">
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
                                                        <div class="row">
                                                            <div class="col-xl-6">
                                                                <label for="year">Year <span class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm" id="year" name="year">
                                                                    @for ($i = date('Y'); $i <= date('Y') + 1; $i++)
                                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                                        @endfor
                                                                </select>
                                                            </div>
                                                            <div class="col-xl-6">
                                                                <label for="month">Month <span class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm" id="month" name="month">
                                                                    @for ($m = 1; $m <= 12; $m++)
                                                                        <option value="{{ $m }}">
                                                                              {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                                                                        </option>
                                                                        @endfor
                                                                </select>
                                                            </div>
                                                        </div>
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
                                                                <th>Bus Name/No</th>
                                                                <th>Bus Cancelled Dates</th>
                                                                <th>Reason</th>
                                                                <th>Cacelled By</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>1</td>
                                                                <td>DILKHUS | OD 02 AS 5297</td>
                                                                <td>03-Apr-2026<br>04-Apr-2026</td>
                                                                <td>Request From Owner</td>
                                                                <td>John Doe<br>23-Apr-2026 10:45:47</td>
                                                            </tr>
                                                            <tr>
                                                                <td>2</td>
                                                                <td>03-Apr-2026</td>
                                                                <td>03-Apr-2026<br>04-Apr-2026</td>
                                                                <td>Request From Owner</td>
                                                                <td>John Doe<br>23-Apr-2026 10:45:47</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>

                                            <!-- RIGHT COLUMN -->
                                            <div class="col-md-7">
                                                <div class="border rounded bg-white">
                                                    <div class="card-header">
                                                        <strong>Date Schedule List</strong>
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

                                                        <div class="row">
                                                            <div class="mb-3">
                                                                <strong>DILKHUS | OD 02 AS 5299 | [Bhubaneswar >> Jharsuguda]</strong>
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
            commonAjax.loadBusOperatorDropdown();
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
       

        $(document).on('click', '.btn-remove', function() {
            $(this).closest('.dynamic-item').remove();
        });

        // FROM DATE CHANGE
        




       


      
    </script>
    @endpush