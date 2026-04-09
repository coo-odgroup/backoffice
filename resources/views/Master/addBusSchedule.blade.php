    @extends('admin.layouts.master')
    @section('page_title', 'Bus Schedule')
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
            <a href="{{ route('bus-schedule.index') }}" class="btn btn-success btn-sm">
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
                                                        <label for="operator">Operator<span class="text-danger">*</span></label>
                                                        <select class="form-select form-select-sm" id="operator" name="operator"></select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="bus">Bus</label>
                                                        <select class="form-select form-select-sm" id="bus" name="bus"></select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="running_cycle">Running Cycle</label>
                                                        <select class="form-select form-select-sm" id="running_cycle" name="running_cycle">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                        </select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="date">Date</label>
                                                        <input type="date" name="date" id="date" class="form-control form-control-sm">
                                                    </div>
                                                </div>

                                            </div>

                                            <!-- RIGHT COLUMN -->
                                            <div class="col-md-7">
                                                <div class="border rounded bg-white">
                                                    <div class="card-header">
                                                        <strong>Date Schedule List</strong>
                                                    </div>

                                                    <div class="card-body">

                                                        @if(!empty($data['scheduleDates']) && count($data['scheduleDates']) > 0)

                                                        @php
                                                        $dates = $data['scheduleDates'];
                                                        $chunkSize = ceil(count($dates) / 3);
                                                        $chunks = array_chunk($dates, $chunkSize);
                                                        @endphp

                                                        <div class="row">
                                                            @foreach($chunks as $chunk)
                                                            <div class="col-4">
                                                                @foreach($chunk as $date)
                                                                <div class="border p-1 text-center rounded bg-light mb-2">
                                                                    {{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                            @endforeach
                                                        </div>

                                                        @else
                                                        <div class="text-center text-muted">
                                                            Bus is not scheduled
                                                        </div>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="row mt-3">
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

    <style>
        .selected-tag {
            display: inline-flex;
            align-items: center;
            background: #ffc107;
            padding: 5px 10px;
            border-radius: 20px;
            margin: 3px
        }

        .selected-tag .remove {
            margin-left: 6px;
            cursor: pointer
        }
    </style>

    @endsection

    @push('scripts')
    <script type="module">
        let selectedOperators = [];

        $(document).ready(function() {

            let slab_id = "{{ $data['row']['slab_id'] ?? '' }}";


            commonAjax.initSelect2('#bus', 'Select Bus');
            commonAjax.initSelect2('#operator', 'Select Bus Operator');
            commonAjax.loadTicketFareSlabList('#slab', slab_id);
            commonAjax.loadBusOperatorDropdown();

            setTimeout(() => {

                if (selectedOperator) {

                    // set operator first
                    $('#operator').val(selectedOperator).trigger('change.select2');

                    // load buses after operator set
                    setTimeout(() => {

                        commonAjax.loadBusListByOperator('#bus', selectedOperator);

                        // then set bus
                        setTimeout(() => {
                            if (selectedBus) {
                                $('#bus').val(selectedBus).trigger('change.select2');
                            }
                        }, 400);

                    }, 300);
                }

            }, 300);

            commonAjax.initClearableInputs();

            $('#bus').on('focus', function() {

                let operator_id = $('#operator').val();

                if (!operator_id) {
                    commonAjax.viewAlert("Please select operator first", "warning");
                    $(this).blur();
                }
            });

            let existingOperators = @json($data['row']['operators'] ?? []);

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

        $('#operator').on('change', function() {

            let operator_id = $(this).val();
            let text = $("#operator option:selected").text();

            commonAjax.loadBusListByOperator('#bus', operator_id);

            if (selectedOperators.some(op => op.id == operator_id)) return;

            let operator = {
                id: operator_id,
                text
            };

            selectedOperators.push(operator);

            renderOperators();
        });


        $('#bus').on('change', function() {

            let bus_id = $(this).val();
            let operator_id = $('#operator').val();

            if (!bus_id) return;

            let url = new URL(window.location.href);

            url.searchParams.set('bus', bus_id);
            url.searchParams.set('operator', operator_id);

            window.location.href = url.toString();
        });

        let selectedOperator = "{{ request('operator') ?? old('operator') }}";
        let selectedBus = "{{ request('bus') ?? old('bus') }}";

        function restoreSelection() {

            if (!selectedOperator) return;

            // wait until operator options loaded
            if ($('#operator option[value="' + selectedOperator + '"]').length === 0) {
                setTimeout(restoreSelection, 200);
                return;
            }

            // set operator
            $('#operator').val(selectedOperator).trigger('change');

            // load buses
            commonAjax.loadBusListByOperator('#bus', selectedOperator);

            // wait and set bus
            setTimeout(() => {

                if (selectedBus) {
                    $('#bus').val(selectedBus).trigger('change.select2');
                }

            }, 400);
        }

        // start restore
        setTimeout(restoreSelection, 300);
    </script>
    @endpush