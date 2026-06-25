        @extends('admin.layouts.master')
        @section('page_title', 'Schema')
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
                <a href="{{ route('schema.index') }}" class="btn btn-success btn-sm">
                    View @yield('page_title')
                </a>
            </div>
        </div>

        <form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form">
            {{ csrf_field() }}

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3">
                                <div>
                                    <div class="row">

                                        <!-- Alerts -->
                                        @if (session('message'))
                                        <div
                                            class="alert alert-{{ session('level') ?? 'success' }} alert-dismissible fade show">
                                            {{ session('message') }}
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="alert"></button>
                                        </div>
                                        @endif

                                        @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="alert"></button>
                                        </div>
                                        @endif

                                        <div class="col-12">
                                            <div class="row">

                                                <!-- LEFT COLUMN -->
                                                <div class="col-md-12">
                                                    <div class="mb-2">
                                                        <div class="row">
                                                            <div class="col-xl-6">
                                                                <label for="schema_page_id">Schema Page <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm"
                                                                    id="schema_page_id" name="schema_page_id">
                                                                    <option value="">Select Schema Page</option>

                                                                </select>
                                                            </div>

                                                            <div class="col-xl-6">
                                                                <label for="schema_type_id">Schema Type <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm"
                                                                    id="schema_type_id" name="schema_type_id">
                                                                    <option value="">Select Schema Type</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="schema_content">Breadcrumb Schema<span
                                                                class="text-danger">*</span></label>
                                                        <textarea class="form-control form-control-sm clearable" rows="25" id="schema_content" name="schema_content">{{ old('schema_content', $data['row']->schema_content ?? '') }}</textarea>
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
            .date-pill {
                padding: 6px 10px;
                border-radius: 6px;
                border: 1px solid #adb5bd;
                background: #e9ecef;
                font-size: 12px;
                white-space: nowrap;
            }

            .date-box {
                padding: 8px;
                border-radius: 6px;
                border: 1px solid #adb5bd;
                background: #f8f9fa;
                font-size: 13px;
            }
        </style>

        @endsection

        @push('scripts')
        <script type="module">
            $(document).ready(function() {

                commonAjax.initSelect2('#schema_page_id', 'Select Type');
                commonAjax.initSelect2('#schema_type_id', 'Select Category');

                let typeVal = "{{ $data['row']->schema_page_id ?? '' }}";
                let categoryVal = "{{ $data['row']->schema_type_id ?? '' }}";

                commonAjax.loadAnnextureList([
                    'SCHEMA_PAGE',
                    'SCHEMA_TYPE',
                ], function(data) {

                    renderDropdown('#schema_page_id', data.SCHEMA_PAGE || [], typeVal);
                    renderDropdown('#schema_type_id', data.SCHEMA_TYPE || [], categoryVal);

                });
            });

            function renderDropdown(selector, items = [], selected = '') {
                let options = '<option value="">Select Option</option>';
                $.each(items, function(index, item) {

                    let isSelected = selected == item.annexture_value ? 'selected' : '';
                    options += ` <option value="${item.annexture_value}" ${isSelected}> ${item.annexture_name} </option> `;
                });
                $(selector).html(options).trigger('change');
            }


            $('#backoffice-form').on('submit', function(e) {

                e.preventDefault();
                let errorMsg = "";
                let isValid = true;

                if (!isValid) {
                    commonAjax.viewAlert(errorMsg, "warning");
                    return false;
                }
                commonAjax.confirmAlert('Are you sure to proceed !');

                $('#btnConfirmOk').off('click').on('click', function() {
                    $('#backoffice-form')[0].submit();
                });
            });
        </script>
        @endpush