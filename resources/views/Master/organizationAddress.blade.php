    @extends('admin.layouts.master')
    @section('page_title', 'Organization Address')
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
    <form id="backoffice-form" name="backoffice-form" method="post" novalidate class="w-100 add-cities-form">
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
                                            <i class="fa fa-map-marker-alt text-primary"></i>
                                            Organization Addresses
                                        </h5>

                                        <button type="button"
                                            class="btn btn-primary btn-sm btn-add-runtime">
                                            <i class="fa fa-plus"></i> Add Address
                                        </button>
                                    </div>
                                    <div id="addressContainer">

                                        @php
                                        $addresses = (!empty($data['addresses']) && count($data['addresses']) > 0)
                                        ? $data['addresses']
                                        : collect([
                                        (object)[
                                        'address_type' => '',
                                        'address1' => '',
                                        'address2' => '',
                                        'country_id' => 1,
                                        'state_id' => '',
                                        'district_id' => '',
                                        'city_id' => '',
                                        'pincode' => '',
                                        'landmark' => '',
                                        'latitude' => '',
                                        'longitude' => '',
                                        'is_default' => 1
                                        ]
                                        ]);
                                        @endphp
                                        @foreach($addresses as $i => $address)

                                        <div class="address-card card shadow-sm border-1 mb-4">
                                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                <strong>
                                                    <i class="fa fa-map-marker-alt"></i>
                                                    Address {{ $i + 1 }}
                                                </strong>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-light text-danger btn-remove-runtime {{ $i == 0 ? 'd-none' : '' }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>

                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-lg-4">
                                                        <label>Address Type</label>
                                                        <select
                                                            class="form-select form-select-sm addressType"
                                                            id="addressType_{{ $i }}"
                                                            name="address_type[]">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row g-3 mt-1">
                                                    <div class="col-6">
                                                        <label>Address Line 1</label>
                                                        <input
                                                            type="text"
                                                            class="form-control form-control-sm"
                                                            name="address1[]"
                                                            value="{{ old('address1.'.$i, $address->address1) }}">
                                                    </div>
                                                    <div class="col-6">
                                                        <label>Address Line 2</label>
                                                        <input
                                                            type="text"
                                                            class="form-control form-control-sm"
                                                            name="address2[]"
                                                            value="{{ old('address2.'.$i, $address->address2) }}">
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label>Country</label>
                                                        <select
                                                            class="form-select form-select-sm country"
                                                            name="country_id[]">
                                                            <option value="1" {{ ($address->country_id ?? 1) == 1 ? 'selected' : '' }}>
                                                                India
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>State</label>
                                                        <select
                                                            class="form-select form-select-sm selState"
                                                            id="selState_{{ $i }}"
                                                            name="state_id[]">
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>District</label>
                                                        <select
                                                            class="form-select form-select-sm selDistrict"
                                                            id="selDistrict_{{ $i }}"
                                                            name="district_id[]">
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label>City</label>
                                                        <select
                                                            class="form-select form-select-sm selCity"
                                                            id="selCity_{{ $i }}"
                                                            name="city_id[]">
                                                        </select>
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <label>Pincode</label>
                                                        <input
                                                            type="text"
                                                            class="form-control form-control-sm"
                                                            name="pincode[]"
                                                            value="{{ old('pincode.'.$i, $address->pincode) }}">
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <label>Landmark</label>
                                                        <input
                                                            type="text"
                                                            class="form-control form-control-sm"
                                                            name="landmark[]"
                                                            value="{{ old('landmark.'.$i, $address->landmark ?? '') }}">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label>Latitude</label>
                                                        <input
                                                            type="text"
                                                            class="form-control form-control-sm"
                                                            name="latitude[]"
                                                            value="{{ old('latitude.'.$i, $address->latitude) }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Longitude</label>
                                                        <input
                                                            type="text"
                                                            class="form-control form-control-sm"
                                                            name="longitude[]"
                                                            value="{{ old('longitude.'.$i, $address->longitude) }}">
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

        $('#backoffice-form').on('submit', function(e) {

            e.preventDefault();
            commonAjax.confirmAlert('Are you sure to proceed !');
            $('#btnConfirmOk').on('click', function() {
                e.currentTarget.submit();
            });
        });
        document.getElementById("menu-toggle").addEventListener("click", function() {
            document.getElementById("sidebar-wrapper").classList.toggle("collapsed");
        });

        let addressTypeList = [];
        let isPageLoading = true;

        $(document).ready(function() {

            commonAjax.loadAnnextureList(['ADDRESS_TYPE'], function(data) {

                addressTypeList = data.ADDRESS_TYPE || [];

                @if(!empty($data['addresses']) && count($data['addresses']) > 0)

                @foreach($data['addresses'] as $i => $address)

                commonAjax.initSelect2('#addressType_{{ $i }}', 'Select Address Type');
                commonAjax.initSelect2('#selState_{{ $i }}', 'Select State');
                commonAjax.initSelect2('#selDistrict_{{ $i }}', 'Select District');
                commonAjax.initSelect2('#selCity_{{ $i }}', 'Select City');

                commonAjax.loadStateList('{{ $address->state_id }}', '#selState_{{ $i }}');

                commonAjax.getDistrictList(
                    '{{ $address->state_id }}',
                    '{{ $address->district_id }}',
                    '#selDistrict_{{ $i }}'
                );

                commonAjax.loadCityList(0, '#selCity_{{ $i }}');

                renderDropdown(
                    '#addressType_{{ $i }}',
                    addressTypeList,
                    '{{ $address->address_type }}'
                );

                setTimeout(function() {

                    $('#selState_{{ $i }}')
                        .val('{{ $address->state_id }}')
                        .trigger('change');

                    $('#selDistrict_{{ $i }}')
                        .val('{{ $address->district_id }}')
                        .trigger('change');

                    $('#selCity_{{ $i }}')
                        .val('{{ $address->city_id }}')
                         .trigger('change.select2');

                }, 1000);

                @endforeach

                @else

                commonAjax.initSelect2('#addressType_0', 'Select Address Type');
                commonAjax.initSelect2('#selState_0', 'Select State');
                commonAjax.initSelect2('#selDistrict_0', 'Select District');
                commonAjax.initSelect2('#selCity_0', 'Select City');

                commonAjax.loadStateList(0, '#selState_0');
                commonAjax.getDistrictList(0, 0, '#selDistrict_0');
                commonAjax.loadCityList(0, '#selCity_0');

                renderDropdown('#addressType_0', addressTypeList, '');

                @endif

            });

            commonAjax.initClearableInputs();

        });

        $(document).on('change', 'select[id^="selState"]', function() {

            if (isPageLoading) {
                return;
            }

            let stateId = $(this).val();
            let uid = $(this).attr('id').replace('selState_', '');

            commonAjax.getDistrictList(
                stateId,
                '',
                '#selDistrict_' + uid
            );

        });

        function renderDropdown(selector, items = [], selected = '') {
            let options = '<option value="">Select</option>';
            $.each(items, function(index, item) {
                options += `<option value="${item.annexture_value}"
                ${selected == item.annexture_value ? 'selected' : ''}>
                ${item.annexture_name}
            </option>`;
            });

            $(selector).html(options).trigger('change');
        }

        $(document).on('click', '.btn-add-runtime', function() {

            let uid = new Date().getTime();

            let html = `
                <div class="address-card card shadow-sm border-1 mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <strong><i class="fa fa-map-marker-alt"></i> Address</strong>

                        <button type="button"
                            class="btn btn-sm btn-light text-danger btn-remove-runtime">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-lg-4">
                                Address Type
                                <select class="form-select form-select-sm"
                                    id="addressType_${uid}"
                                    name="address_type[]">
                                </select>
                            </div>

                        </div>

                        <div class="row g-3">

                            <div class="col-6">
                                Address Line 1
                                <input class="form-control form-control-sm"
                                    name="address1[]">
                            </div>

                            <div class="col-6">
                                Address Line 2
                                <input class="form-control form-control-sm"
                                    name="address2[]">
                            </div>

                            <div class="col-lg-3">
                                Country
                                <select class="form-select form-select-sm"
                                    name="country_id[]">
                                    <option value="1">India</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                State
                                <select class="form-select form-select-sm"
                                    id="selState_${uid}"
                                    name="state_id[]">
                                </select>
                            </div>

                            <div class="col-md-3">
                                District
                                <select class="form-select form-select-sm"
                                    id="selDistrict_${uid}"
                                    name="district_id[]">
                                </select>
                            </div>

                            <div class="col-md-3">
                                City
                                <select class="form-select form-select-sm"
                                    id="selCity_${uid}"
                                    name="city_id[]">
                                </select>
                            </div>

                            <div class="col-lg-3">
                                Pincode
                                <input class="form-control form-control-sm"
                                    name="pincode[]">
                            </div>

                            <div class="col-lg-3">
                                Landmark
                                <input class="form-control form-control-sm"
                                    name="landmark[]">
                            </div>

                            <div class="col-md-3">
                                Latitude
                                <input class="form-control form-control-sm"
                                    name="latitude[]">
                            </div>

                            <div class="col-md-3">
                                Longitude
                                <input class="form-control form-control-sm"
                                    name="longitude[]">
                            </div>

                        </div>

                    </div>
                </div>
                `;

            $('#addressContainer').append(html);

            commonAjax.initSelect2('#addressType_' + uid, 'Select Address Type');
            commonAjax.initSelect2('#selState_' + uid, 'Select State');
            commonAjax.initSelect2('#selDistrict_' + uid, 'Select District');
            commonAjax.initSelect2('#selCity_' + uid, 'Select City');

            renderDropdown('#addressType_' + uid, addressTypeList, '');

            commonAjax.loadStateList(0, '#selState_' + uid);
            commonAjax.loadCityList(0, '#selCity_' + uid);
            setTimeout(function() {
                console.log($('#selCity_' + uid).html());
            }, 1000);

        });

        $(document).on('click', '.btn-remove-runtime', function() {
            $(this).closest('.address-card').remove();
        });

        $(document).on('click', '.btn-remove-runtime', function() {

            if ($('.address-card').length > 1) {
                $(this).closest('.address-card').remove();
            }

        });
    </script>
    @endpush