    @extends('admin.layouts.master')
    @section('page_title', 'Organization Contacts')
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
        action="{{ route('organization-contacts.edit', Crypt::encryptString($data['row']->id)) }}">
        @else
        <form id="backoffice-form"
            method="POST"
            action="{{ route('organization-contacts.add') }}">
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
                                                Organization Contact Details
                                            </h5>

                                            <button type="button"
                                                class="btn btn-primary btn-sm btn-add-runtime">
                                                <i class="fa fa-plus"></i> Add Contact
                                            </button>
                                        </div>
                                        <div id="addressContainer">

                                            @php
                                            $contacts = (!empty($data['contacts']) && count($data['contacts']) > 0)
                                            ? $data['contacts']
                                            : collect([
                                            (object)[
                                            'contact_type'=>'',
                                            'fullname'=>'',
                                            'designation'=>'',
                                            'mobile'=>'',
                                            'alternate_mobile'=>'',
                                            'email'=>'',
                                            'is_primary'=>1
                                            ]
                                            ]);
                                            @endphp

                                            @foreach($contacts as $i => $contact)

                                            <div class="address-card card shadow-sm border-1 mb-4">
                                                <div class="card-header bg-primary d-flex align-items-center">

                                                    <!-- Left -->
                                                    <strong class="text-white account-title">
                                                        <i class="fa-solid fa-address-card"></i>
                                                        Contact {{ $i + 1 }}
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
                                                                value="{{ $i }}">
                                                            <span class="slider"></span>
                                                        </label>

                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3 mt-1">
                                                        <div class="row g-3">
                                                            <div class="col-lg-6">
                                                                <label>Contact Type</label>
                                                                <select
                                                                    class="form-select form-select-sm contact_type"
                                                                    id="contact_type_{{ $i }}"
                                                                    name="contact_type[]">
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row g-3">
                                                             <div class="col-md-6">
                                                            <label>Full Name <span class="text-danger">*</span></label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="fullname[]"
                                                                value="{{ old('fullname.'.$i, $contact->fullname) }}">
                                                        </div></div>
                                                       
                                                        <div class="col-md-3">
                                                            <label>Designation<span class="text-danger">*</span></label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="designation[]"
                                                                value="{{ old('designation.'.$i, $contact->designation) }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>Mobile No. <span class="text-danger">*</span></label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="mobile[]"
                                                                value="{{ old('mobile.'.$i, $contact->mobile ?? '') }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>Alternate Mobile No.</label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="alternate_mobile[]"
                                                                value="{{ old('alternate_mobile.'.$i, $contact->alternate_mobile) }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>Email Id<span class="text-danger">*</span></label>
                                                            <input
                                                                type="text"
                                                                class="form-control form-control-sm"
                                                                name="email[]"
                                                                value="{{ old('email.'.$i, $contact->email) }}">
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
            let contactTypeList = [];
            let isPageLoading = true;

            $(document).ready(function() {

                commonAjax.loadAnnextureList(['CONTACT_TYPE'], function(data) {

                    contactTypeList = data.CONTACT_TYPE || [];
                    @if(!empty($data['contacts']) && count($data['contacts']) > 0)

                    @foreach($data['contacts'] as $i => $contact)
                    commonAjax.initSelect2('#contact_type_{{ $i }}', 'Select Contact Type');

                    renderContactDropdown(
                        '#contact_type_{{ $i }}',
                        contactTypeList,
                        '{{ $contact->contact_type }}'
                    );

                    @endforeach

                    @else

                    commonAjax.initSelect2('#contact_type_0', 'Select Contact Type');
                    renderContactDropdown(
                        '#contact_type_0',
                        contactTypeList,
                        ''
                    );

                    @endif

                });

                commonAjax.initClearableInputs();
});
            console.log($('#contact_type_0').length);
            console.log($('#contact_type_0'));

            function renderContactDropdown(selector, items = [], selected = '') {
                let options = '<option value="">Select Contact Type</option>';
                $.each(items, function(index, item) {

                    options += `
            <option value="${item.annexture_value}"
                ${selected == item.annexture_value ? 'selected' : ''}>
                ${item.annexture_name}
            </option>`;
                });

                $(selector).html(options).trigger('change');
            }


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

            let accountIndex = $('.address-card').length;
            $(document).on('click', '.btn-add-runtime', function() {

                let html = `
            <div class="address-card card shadow-sm border-1 mb-4">
                    <div class="card-header bg-primary d-flex align-items-center">

                        <!-- Left -->
                        <strong class="text-white account-title">
                           <i class="fa-solid fa-address-card"></i>
                             Contact {{ $i + 1 }}
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
                           {{ $contact->is_primary ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                            <button type="button"
                                class="btn btn-sm btn-light text-danger btn-remove-runtime">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        </div>
                <div class="card-body">
                    <div class="row g-3 mt-1"><div class="row g-3">
                                                    <div class="col-lg-4">
                                                        <label>Contact type</label>
                                                        <select
                                                            class="form-select form-select-sm contact_type"
                                                         id="contact_type_${accountIndex}"
                                                            name="contact_type[]">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Full Name<span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control form-control-sm"
                                                        name="fullname[]">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Designation<span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control form-control-sm"
                                                        name="designation[]">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Mobile No. <span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control form-control-sm"
                                                        name="mobile[]">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Alternate Mobile No.</label>
                                                    <input type="text"
                                                        class="form-control form-control-sm"
                                                        name="alternate_mobile[]">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Email Id<span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control form-control-sm text-uppercase"
                                                        name="email[]">
                                                </div>
                    </div>
                </div>
            </div>`;

                $('#addressContainer').append(html);

                commonAjax.initSelect2(
                    '#contact_type_' + accountIndex,
                    'Select Contact Type'
                );

                renderContactDropdown(
                    '#contact_type_' + accountIndex,
                    contactTypeList,
                    ''
                );

                accountIndex++;

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