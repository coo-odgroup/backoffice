    @extends('admin.layouts.master')
    @section('page_title', 'Organization Bank Account')
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
        action="{{ route('organization-bank-account.edit', Crypt::encryptString($data['row']->id)) }}">
        @else
        <form id="backoffice-form"
            method="POST"
            action="{{ route('organization-bank-account.add') }}">
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
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">
                                                <i class="fa-solid fa-building-columns"></i>
                                                Organization Bank Account Details
                                            </h5>

                                            <button type="button"
                                                class="btn btn-primary btn-sm btn-add-runtime">
                                                <i class="fa fa-plus"></i> Add Bank Account
                                            </button>
                                        </div>
                                        <div id="addressContainer">

                                            @php
                                            $accounts = (!empty($data['accounts']) && count($data['accounts']) > 0)
                                            ? $data['accounts']
                                            : collect([
                                            (object)[
                                            'account_number' => '',
                                            'account_holder' => '',
                                            'bank_name' => '',
                                            'branch_name' => '',
                                            'ifsc' => '',
                                            'upi_id' => '',
                                            'is_primary' => 1
                                            ]
                                            ]);
                                            @endphp

                                            @foreach($accounts as $i => $account)
                                            <div class="address-card card shadow-sm border-1 mb-4">
                                                <div class="card-header bg-primary d-flex align-items-center">

                                                    <!-- Left -->
                                                    <strong class="text-white account-title">
                                                        <i class="fa-solid fa-money-bills me-2"></i>
                                                        Account {{ $i + 1 }}
                                                    </strong>

                                                    <!-- Right -->
                                                    <div class="ms-auto d-flex align-items-center">
                                                        <label class="primary-label me-2 mb-0 account-titletext-white fw-semibold">
                                                            Primary Account
                                                        </label>
                                                        <label class="primary-switch mb-0 me-3">
                                                            <input
                                                                type="radio"
                                                                class="primaryAccount"
                                                                name="primary_account"
                                                                value="{{ $i }}"
                                                                {{ $account->is_primary ? 'checked' : '' }}>
                                                            <span class="slider"></span>
                                                        </label>

                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3 mt-1">
                                                        <div class="col-md-4">
                                                            <label>Account Number <span class="text-danger">*</span></label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="account_number[]"
                                                                value="{{ old('account_number.'.$i, $account->account_number) }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Account Holder Name <span class="text-danger">*</span></label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="account_holder[]"
                                                                value="{{ old('account_holder.'.$i, $account->account_holder) }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Bank Name <span class="text-danger">*</span></label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="bank_name[]"
                                                                value="{{ old('bank_name.'.$i, $account->bank_name ?? '') }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Branch Name</label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="branch_name[]"
                                                                value="{{ old('branch_name.'.$i, $account->branch_name) }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>IFSC Code <span class="text-danger">*</span></label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="ifsc[]"
                                                                value="{{ old('ifsc.'.$i, $account->ifsc) }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>UPI ID</label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="upi_id[]"
                                                                value="{{ old('upi_id.'.$i, $account->upi_id ?? '') }}">
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

            let addressTypeList = [];
            let isPageLoading = true;

            let accountIndex = $('.address-card').length;

            $(document).on('click', '.btn-add-runtime', function() {

                let html = `
            <div class="address-card card shadow-sm border-1 mb-4">
                    <div class="card-header bg-primary d-flex align-items-center">

                        <!-- Left -->
                        <strong class="text-white account-title">
                            <i class="fa-solid fa-money-bills me-2"></i>
                            Account {{ $i + 1 }}
                        </strong>

                        <!-- Right -->
                        <div class="ms-auto d-flex align-items-center">

                            <label class="primary-label me-2 mb-0 text-white fw-semibold">
                                Primary Account
                            </label>
                            <label class="primary-switch mb-0 me-3">
                                <input
                        type="radio"
                        class="primaryAccount"
                        name="primary_account"
                        value="{{ $i }}"
                    {{ $account->is_primary ? 'checked' : '' }}
                        >
                                <span class="slider"></span>
                            </label>
                            <button type="button"
                                class="btn btn-sm btn-light text-danger btn-remove-runtime">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        </div>
                <div class="card-body">
                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label>Account Number <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control form-control-sm"
                                name="account_number[]">
                        </div>
                        <div class="col-md-4">
                            <label>Account Holder Name <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control form-control-sm"
                                name="account_holder[]">
                        </div>
                        <div class="col-md-4">
                            <label>Bank Name <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control form-control-sm"
                                name="bank_name[]">
                        </div>
                        <div class="col-md-4">
                            <label>Branch Name</label>
                            <input type="text"
                                class="form-control form-control-sm"
                                name="branch_name[]">
                        </div>
                        <div class="col-md-4">
                            <label>IFSC Code <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control form-control-sm text-uppercase"
                                name="ifsc[]">
                        </div>
                        <div class="col-md-4">
                            <label>UPI ID</label>
                            <input type="text"
                                class="form-control form-control-sm"
                                name="upi_id[]">
                        </div>
                    </div>
                </div>
            </div>`;

                $('#addressContainer').append(html);

                accountIndex++;
            });




            $(document).on('click', '.btn-remove-runtime', function() {
                $(this).closest('.address-card').remove();
            });

            $(document).on('click', '.btn-remove-runtime', function() {

                if ($('.address-card').length > 1) {
                    $(this).closest('.address-card').remove();
                }

            });

            $(document).on('change', '.primaryAccount', function() {

                // Reset all headers to BLUE
                $('.address-card .card-header')
                    .removeClass('primary-header')
                    .addClass('bg-primary')
                    .css({
                        'background-color': '',
                        'color': '#fff'
                    });

                $('.address-card .account-title')
                    .removeClass('text-dark')
                    .addClass('text-white');

                // Selected card
                let card = $(this).closest('.address-card');

                // Remove Bootstrap blue
                card.find('.card-header')
                    .removeClass('bg-primary')
                    .addClass('primary-header');

                card.find('.account-title')
                    .removeClass('text-white')
                    .addClass('text-dark');

            });
            $(function() {

                if ($('.primaryAccount:checked').length == 0) {
                    $('.primaryAccount:first').prop('checked', true);
                }

                $('.primaryAccount:checked').trigger('change');

            });
            $(function() {
                $('.primaryAccount:checked').trigger('change');
            });

            $(document).on('click', '.btn-remove-runtime', function() {

                if ($('.address-card').length == 1) {
                    Swal.fire('Warning', 'At least one bank account is required.', 'warning');
                    return;
                }

                $(this).closest('.address-card').remove();

                $('.address-card').each(function(index) {

                    $(this).find('.card-header strong').html(
                        '<i class="fa-solid fa-money-bills"></i> Account ' + (index + 1)
                    );

                    $(this).find('.primaryAccount').val(index);

                });

                accountIndex = $('.address-card').length;
            });
        </script>
        @endpush