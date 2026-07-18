    @extends('admin.layouts.master')
    @section('page_title', 'Organization Tax Details')
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

    <!-- Booking Report Card -->
    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 id="page_title">@yield('page_title')</h5>
        <div>
            <a href="{{ route('organization.index') }}" class="btn btn-success btn-sm">
                View Organization
            </a>
        </div>
    </div>

    <!-- TABLE -->
    @if(isset($data['row']))
    <form id="backoffice-form"
        method="POST"
        action="{{ route('organization-tax-details.edit', Crypt::encryptString($data['row']->id)) }}">
        @else
        <form id="backoffice-form"
            method="POST"
            action="{{ route('organization-tax-details.add') }}">
            @endif

            @csrf
            {{csrf_field()}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- FILTER -->
                            <div class="mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        @if (session('message'))

                                        <div class="alert alert-{{ session('level') ?? 'success' }} alert-dismissible fade show" role="alert">
                                            {{ session('message') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>

                                        @endif
                                        @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>

                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                        @endif

                                        <div id="addressContainer">

                                            @php
                                            $taxes = (!empty($data['taxes']) && $data['taxes']->count() > 0)
                                            ? $data['taxes']
                                            : collect([
                                            (object)[
                                            'gst_number' => '',
                                            'pan_number' => '',
                                            'tan_number' => '',
                                            'cin_number' => '',
                                            'msme_number' => '',
                                            'trade_license_number' => '',
                                            'gst_registered_name' => '',
                                            'gst_registered_address' => '',
                                            'gst_registration_date' => '',
                                            'gst_expiry_date' => '',
                                            ]
                                            ]);
                                            @endphp

                                            @foreach($taxes as $i => $tax)
                                            <div class="address-card card shadow-sm border-1 mb-4">
                                                <div class="card-header bg-primary d-flex align-items-center">

                                                    <!-- Left -->
                                                    <strong class="text-white tax-title">
                                                        <i class="fa-solid fa-percent"></i>
                                                           Tax Details
                                                    </strong>

                                                    <!-- Right -->

                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3 mt-1">
                                                        <div class="col border border-dark rounded p-3 me-3">
                                                            <div class="col-md-12 mt-4">
                                                                <label>GST Number <span class="text-danger">*</span></label>
                                                                <input
                                                                    type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="gst_number[]"
                                                                    value="{{ old('gst_number.'.$i, $tax->gst_number ?? '') }}">
                                                            </div>

                                                            <div class="col-md-12 mt-4">
                                                                <label>GST Registered Name</label>
                                                                <input
                                                                    type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="gst_registered_name[]"
                                                                    value="{{ old('gst_registered_name.'.$i, $tax->gst_registered_name ?? '') }}">
                                                            </div>



                                                            <div class="col-md-12 mt-4">
                                                                <label>GST Registration Date</label>
                                                                <input
                                                                    type="date"
                                                                    class="form-control form-control-sm"
                                                                    name="gst_registration_date[]"
                                                                    value="{{ old('gst_registration_date.'.$i, $tax->gst_registration_date ?? '') }}">
                                                            </div>

                                                            <div class="col-md-12 mt-4">
                                                                <label>GST Expiry Date</label>
                                                                <input
                                                                    type="date"
                                                                    class="form-control form-control-sm"
                                                                    name="gst_expiry_date[]"
                                                                    value="{{ old('gst_expiry_date.'.$i, $tax->gst_expiry_date ?? '') }}">
                                                            </div>

                                                            <div class="col-md-12 mt-4" row="4">
                                                                <label>GST Registered Address</label>
                                                                <textarea
                                                                    class="form-control form-control-sm"
                                                                    name="gst_registered_address[]"
                                                                    rows="4">{{ old('gst_registered_address.'.$i, $tax->gst_registered_address ?? '') }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col border border-dark rounded p-3 ">

                                                            <div class="col-md-12 mt-4">
                                                                <label>PAN Number <span class="text-danger">*</span></label>
                                                                <input
                                                                    type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="pan_number[]"
                                                                    value="{{ old('pan_number.'.$i, $tax->pan_number ?? '') }}">
                                                            </div>

                                                            <div class="col-md-12 mt-4">
                                                                <label>TAN Number</label>
                                                                <input
                                                                    type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="tan_number[]"
                                                                    value="{{ old('tan_number.'.$i, $tax->tan_number ?? '') }}">
                                                            </div>

                                                            <div class="col-md-12 mt-4">
                                                                <label>CIN Number</label>
                                                                <input
                                                                    type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="cin_number[]"
                                                                    value="{{ old('cin_number.'.$i, $tax->cin_number ?? '') }}">
                                                            </div>

                                                            <div class="col-md-12 mt-4">
                                                                <label>MSME Number</label>
                                                                <input
                                                                    type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="msme_number[]"
                                                                    value="{{ old('msme_number.'.$i, $tax->msme_number ?? '') }}">
                                                            </div>

                                                            <div class="col-md-12 mt-4">
                                                                <label>Trade License Number</label>
                                                                <input
                                                                    type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="trade_license_number[]"
                                                                    value="{{ old('trade_license_number.'.$i, $tax->trade_license_number ?? '') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- BUTTONS -->
                                <div class="row">
                                    <div class="col-12 d-flex gap-2 justify-content-md-start justify-content-center">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            {{ $data['strSubmit'] }}
                                        </button>
                                        @if($data['strReset'] == 'Cancel')
                                        <a href="{{ route('states.index') }}" class="btn btn-secondary btn-sm">
                                            {{ $data['strReset'] }}
                                        </a>
                                        @else
                                        <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                            {{ $data['strReset'] }}
                                        </button>
                                        @endif
                                    </div>
                                </div>
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
            $('#btnReset').click(function() {
                $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
                $('.form-select').val(0);
                $('.form-select').val('').trigger('change');
            });

            // $('#backoffice-form').on('submit', function(e) {

            //     e.preventDefault();
            //     commonAjax.confirmAlert('Are you sure to proceed !');
            //     $('#btnConfirmOk').on('click', function() {
            //         e.currentTarget.submit();
            //     });
            // });
            document.getElementById("menu-toggle").addEventListener("click", function() {
                document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
            });

            let isPageLoading = true;
            let taxIndex = $('.address-card').length;

            // Reset Form
            $('#btnReset').click(function() {
                $('#backoffice-form')[0].reset();
            });

            // Remove Tax Card
            $(document).on('click', '.btn-remove-runtime', function() {

                if ($('.address-card').length == 1) {
                    Swal.fire('Warning', 'At least one tax detail is required.', 'warning');
                    return;
                }

                $(this).closest('.address-card').remove();

                // Renumber cards
                $('.address-card').each(function(index) {

                    $(this).find('.tax-title').html(
                        '<i class="fa-solid fa-money-bills me-2"></i> Tax Details ' + (index + 1)
                    );

                });

                taxIndex = $('.address-card').length;
            });
        </script>
        @endpush