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
                                <div>
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
                                                    <div class="border rounded schedule-card">
                                                        <div class="card-header schedule-header">
                                                            <strong>Date Schedule List</strong>
                                                        </div>
                                                        <div class="card-body" id="scheduleContainer">

                                                            <div id="scheduleTemplate" style="display:none;">

                                                                @if(!empty($data['scheduleDates']) && count($data['scheduleDates']) > 0)

                                                                @php
                                                                $chunkSize = ceil(count($data['scheduleDates']) / 3);
                                                                $chunks = array_chunk($data['scheduleDates'], $chunkSize);
                                                                @endphp

                                                                <div class="row">
                                                                    @foreach($chunks as $chunk)
                                                                    <div class="col-4">
                                                                        @foreach($chunk as $date)
                                                                        <div class="date-tile text-center mb-2">
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

        </style>

        @endsection

        @push('scripts')
        <script type="module">
            let selectedOperators = [];

            $(document).ready(function() {

                let slab_id = "{{ $data['row']['slab_id'] ?? '' }}";


                $('#operator').select2({
                    placeholder: "Select Bus Operator",
                    dropdownParent: $('body')
                });

                $('#bus').select2({
                    placeholder: "Select Bus",
                    dropdownParent: $('body')
                });
                commonAjax.loadTicketFareSlabList('#slab', slab_id);
                commonAjax.loadBusOperatorDropdown();

                setTimeout(() => {
                    $('#operator').select2({
                        placeholder: "Select Bus Operator",
                        dropdownParent: $('body')
                    });

                    $('#bus').select2({
                        placeholder: "Select Bus",
                        dropdownParent: $('body')
                    });
                }, 300);

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


            $(document).on('click', '.btn-remove', function() {
                $(this).closest('.dynamic-item').remove();
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
                if (!bus_id) return;

                //  SHOW SPINNER FIRST
                            $('#scheduleContainer').html(`
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Loading schedule...</p>
                    </div>
                `);

                // AJAX CALL
                $.ajax({
                    type: "POST",
                    url: "/admin/get-schedule-dates",
                    data: {
                        bus_id: bus_id,
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        $('#scheduleContainer').html(response);
                    },
                    error: function() {
                        $('#scheduleContainer').html(`
                            <div class="text-danger text-center p-3">
                                Failed to load schedule
                            </div>
                        `);
                    }
                });

            });


            function formatDate(dateStr) {
                let d = new Date(dateStr);
                return d.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }


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