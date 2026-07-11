@extends('admin.layouts.master')
@section('page_title', 'Boarding Dropping Management')
@section('content')

<?php
$page_name = 'All ' . trim($__env->yieldContent('page_title'));
$listButtons = ['indicate' => 'N', 'print' => 'N', 'xls' => 'N', 'download' => 'N', 'back' => 'N', 'delete' => 'y', 'active' => 'y', 'inactive' => 'y'];
?>

<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Manage Route SEO</li>
        <li class="breadcrumb-item active">@yield('page_title')</li>
    </ol>
</nav>

<!-- Booking Report Card -->
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2 d-none">
    <h5 id="page_title">@yield('page_title')</h5>
    <div>
        <button type="button" id="btnToggleFilter" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-magnifying-glass me-1"></i>
            <span class="btn-text">Filter</span>
        </button>
    </div>
</div>

<!-- TABLE -->
<form id="backoffice-form" name="backoffice-form" method="post" novalidate>
    <div class="card">
        <div class="card-body">

            <!-- FILTER -->
            <div class="mb-3" id="filterBox">
                <div class="card-body">
                    <div class="row align-items-end g-2">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Route</label>
                            <select class="form-select form-select-sm selRoute" id="route_id" name="route_id">
                                <option value="">Select Route</option>
                            </select>
                        </div>

                        <div class="col-lg-2 d-flex justify-content-end flex-wrap action-btns gap-1 mt-2">
                            <button class="btn btn-primary btn-sm" type="button" onclick="searchRouteDistance()">
                                <i class="fa-solid fa-search me-1"></i>Search
                            </button>
                            <button class="btn btn-secondary btn-sm" id="btnReset" type="button">
                                <i class="fa-solid fa-rotate-left me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="boardingDroppingSection" class="d-none mt-3">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card border-primary h-100">
                            <div class="card-header bg-primary text-white fw-bold" id="boardingCityTitle">
                                Boarding Points
                            </div>
                            <div class="card-body" id="boardingPointsContainer" style="max-height: 420px; overflow-y: auto; overflow-x: hidden;"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-primary h-100">
                            <div class="card-header bg-primary text-white fw-bold" id="droppingCityTitle">
                                Dropping Points
                            </div>
                            <div class="card-body" id="droppingPointsContainer" style="max-height: 420px; overflow-y: auto; overflow-x: hidden;"></div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-lg-6">
                        <div class="card border-secondary h-100">
                            <div class="card-header bg-secondary text-white fw-bold">
                                Breadcrumb Schema
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" id="breadcrumb_schema" name="breadcrumb_schema" rows="18"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-secondary  h-100">
                            <div class="card-header bg-secondary text-white fw-bold">
                                FAQ Schema
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" id="faq_schema" name="faq_schema" rows="18"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-sm d-none mt-4" id="btnUpdateBoardingDropping">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update All
                </button>
            </div>
        </div>


        <div class="footer-background border-success text-center" id="norecord" style="display:none">Select a route to View Boarding Dropping.</div>
        {{csrf_field()}}
        <input name="hdn_ids" id="hdn_ids" type="hidden">
        <input name="hdn_boarding_ids" id="hdn_boarding_ids" type="hidden">
        <input name="hdn_dropping_ids" id="hdn_dropping_ids" type="hidden">
        <input name="hdn_qs" id="hdn_qs" type="hidden">
        <input type="hidden" id="hdn_model" value="ManageBoardingDropping">

        <!-- <div class="d-flex justify-content-between align-items-center mt-2">
                <div id="customTableInfo"></div>
            </div> -->
    </div>
    </div>
    </div>
</form>

@endsection
@push('scripts')
<script type="module">
    $(document).ready(function() {
        commonAjax.loadRouteList();
        initSelect2('#route_id', 'Select Route');
        toggleBoardingDropping(false);
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val('').trigger('change');

        $('#hdn_ids').val('');
        $('#hdn_boarding_ids').val('');
        $('#hdn_dropping_ids').val('');
        $('#hdn_qs').val('');
        $('#boardingPointsContainer').html('');
        $('#droppingPointsContainer').html('');
        $('#btnUpdateBoardingDropping').addClass('d-none');

        toggleBoardingDropping(false);
        $('#norecord').show().text('Select a route to View Boarding Dropping.');
    });

    $(document).on('change', '.point-checkbox', function() {
        bindSelectedPointIds();
    });

    function bindSelectedPointIds() {
        let boardingIds = [];
        let droppingIds = [];
        let allIds = [];

        $('.boarding-point-checkbox:checked').each(function() {
            boardingIds.push($(this).val());
            allIds.push($(this).val());
        });

        $('.dropping-point-checkbox:checked').each(function() {
            droppingIds.push($(this).val());
            allIds.push($(this).val());
        });

        $('#hdn_boarding_ids').val(boardingIds.join(','));
        $('#hdn_dropping_ids').val(droppingIds.join(','));
        $('#hdn_ids').val(allIds.join(','));
    }

    window.searchRouteDistance = function() {
        let routeId = ($('#route_id').val() || '').toString().trim();
        let hasRoute = routeId !== '' && routeId !== '0' && routeId !== 'null' && routeId !== 'undefined';

        if (!hasRoute) {
            toggleBoardingDropping(false);
            $('#norecord').show().text('Please select a route before searching.');
            return;
        }

        getBoardingDroppingData();
    };

    function getBoardingDroppingData() {
        $.ajax({
            url: "{{ route('manage-boarding-dropping.dataTableView') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                route_id: $('#route_id').val()
            },
            beforeSend: function() {
                if (typeof viewLoader === 'function') {
                    viewLoader(true);
                }
            },
            success: function(response) {
                if (response && response.status && response.data) {
                    renderBoardingDropping(response.data);
                    toggleBoardingDropping(true);
                } else {
                    toggleBoardingDropping(false);
                    $('#norecord').show().text(response.message || 'No boarding/dropping data found.');
                }
            },
            error: function() {
                toggleBoardingDropping(false);
                $('#norecord').show().text('Failed to load boarding/dropping data.');
            },
            complete: function() {
                if (typeof viewLoader === 'function') {
                    viewLoader(false);
                }
            }
        });
    }

    function renderBoardingDropping(data) {
        let sourceCity = data.source_city_name || 'Boarding Points';
        let destinationCity = data.destination_city_name || 'Dropping Points';

        let selectedBoardingIds = data.selected_boarding_ids || [];
        let selectedDroppingIds = data.selected_dropping_ids || [];

        $('#boardingCityTitle').text(sourceCity + ' - Boarding Points');
        $('#droppingCityTitle').text(destinationCity + ' - Dropping Points');

        let boardingHtml = '';
        let droppingHtml = '';

        if (data.boarding_points && data.boarding_points.length > 0) {
            data.boarding_points.forEach(function(item, index) {
                let isChecked = selectedBoardingIds.includes(parseInt(item.id)) ? 'checked' : '';

                boardingHtml += `
                <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input boarding-point-checkbox point-checkbox"
                            type="checkbox"
                            value="${item.enc_id}"
                            data-type="boarding"
                            id="boarding_${index}"
                            ${isChecked}
                            style="width:12px; height:12px; border:1px solid #000; cursor:pointer;">
                        <label class="form-check-label w-100" for="boarding_${index}">
                            <strong>${item.brd_drp_point ?? '--'}</strong>
                        </label>
                    </div>
                </div>
            `;
            });
        } else {
            boardingHtml = `<div class="text-muted">No boarding points found.</div>`;
        }

        if (data.dropping_points && data.dropping_points.length > 0) {
            data.dropping_points.forEach(function(item, index) {
                let isChecked = selectedDroppingIds.includes(parseInt(item.id)) ? 'checked' : '';

                droppingHtml += `
                <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input dropping-point-checkbox point-checkbox"
                            type="checkbox"
                            value="${item.enc_id}"
                            data-type="dropping"
                            id="dropping_${index}"
                            ${isChecked}
                            style="width:12px; height:12px; border:1px solid #000; cursor:pointer;">
                        <label class="form-check-label w-100" for="dropping_${index}">
                            <strong>${item.brd_drp_point ?? '--'}</strong>
                        </label>
                    </div>
                </div>
            `;
            });
        } else {
            droppingHtml = `<div class="text-muted">No dropping points found.</div>`;
        }

        $('#boardingPointsContainer').html(boardingHtml);
        $('#droppingPointsContainer').html(droppingHtml);


        bindSelectedPointIds();
        generateRouteSchemas(data);
        $('#btnUpdateBoardingDropping').removeClass('d-none');
    }


    $('#btnUpdateBoardingDropping').on('click', function() {
        let routeId = ($('#route_id').val() || '').toString().trim();

        if (!routeId) {
            $('#norecord').show().text('Please select a route before updating.');
            return;
        }

        bindSelectedPointIds();

        $.ajax({
            url: "{{ route('manage-boarding-dropping.add') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                route_id: routeId,
                hdn_boarding_ids: $('#hdn_boarding_ids').val(),
                hdn_dropping_ids: $('#hdn_dropping_ids').val(),

                breadcrumb_schema: $('#breadcrumb_schema').val(),
                faq_schema: $('#faq_schema').val()
            },
            beforeSend: function() {
                if (typeof viewLoader === 'function') {
                    viewLoader(true);
                }
                $('#btnUpdateBoardingDropping').prop('disabled', true);
            },
            success: function(response) {
                if (response && response.status) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Updated successfully.'
                        });
                    } else {
                        commonAjax.viewAlert(response.message || 'Updated successfully.');
                    }

                    // Reload so saved selections come back from DB
                    getBoardingDroppingData();
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Something went wrong.'
                        });
                    } else {
                        commonAjax.viewAlert(response.message || 'Something went wrong.');
                    }
                }
            },
            error: function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update boarding / dropping mapping.'
                    });
                } else {
                    commonAjax.viewAlert('Failed to update boarding / dropping mapping.');
                }
            },
            complete: function() {
                if (typeof viewLoader === 'function') {
                    viewLoader(false);
                }
                $('#btnUpdateBoardingDropping').prop('disabled', false);
            }
        });
    });

    function toggleBoardingDropping(show = false) {
        $('#boardingDroppingSection').toggleClass('d-none', !show);

        if (!show) {
            $('#norecord').show();
        } else {
            $('#norecord').hide();
        }
    }

    function slugifyRoutePart(text) {
        return (text || '')
            .toString()
            .trim()
            .toLowerCase()
            .replace(/&/g, 'and')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    function generateRouteSchemas(data) {
        const frontUrl = "{{ rtrim(config('constants.CONSUMER_FRONT_URL'), '/') }}";

        const source = data.source_city_name || '';
        const destination = data.destination_city_name || '';

        const sourceSlug = slugifyRoutePart(source);
        const destinationSlug = slugifyRoutePart(destination);

        const distance = data.distance ?? '';
        const fromHrs = data.from_hrs ?? '';
        const toHrs = data.to_hrs ?? '';
        const busTypes = data.bus_types ?? '';
        const minFare = data.min_fare ?? '';
        const maxFare = data.max_fare ?? '';

        // =========================
        // BREADCRUMB SCHEMA
        // =========================
        if (data.breadcrumb_schema && data.breadcrumb_schema.trim() !== '') {
            $("#breadcrumb_schema").val(formatSchemaForTextarea(data.breadcrumb_schema));
        } else {
            commonAjax.getSchemaContent("Routes", "Breadcrumb", function(response) {
                if (!response.status || !response.data || !response.data.schema_content) {
                    console.log(response.message || 'Breadcrumb schema not found');
                    return;
                }

                let schema = response.data.schema_content;

                schema = schema
                    .replaceAll("__BASE_URL__", frontUrl)
                    .replaceAll("__SOURCE__", source)
                    .replaceAll("__DESTINATION__", destination)
                    .replaceAll("__SOURCE_SLUG__", sourceSlug)
                    .replaceAll("__DESTINATION_SLUG__", destinationSlug);

                $("#breadcrumb_schema").val(formatSchemaForTextarea(schema));
            });
        }

        // =========================
        // FAQ SCHEMA
        // =========================
        if (data.faq_schema && data.faq_schema.trim() !== '') {
            $("#faq_schema").val(formatSchemaForTextarea(data.faq_schema));
        } else {
            commonAjax.getSchemaContent("Routes", "FAQ", function(response) {
                if (!response.status || !response.data || !response.data.schema_content) {
                    console.log(response.message || 'FAQ schema not found');
                    return;
                }

                let schema = response.data.schema_content;

                schema = schema
                    .replaceAll("__SOURCE__", source)
                    .replaceAll("__DESTINATION__", destination)
                    .replaceAll("__DISTANCE__", distance)
                    .replaceAll("__FROM_HRS__", fromHrs)
                    .replaceAll("__TO_HRS__", toHrs)
                    .replaceAll("__BUS_TYPES__", busTypes)
                    .replaceAll("__MIN_FARE__", minFare)
                    .replaceAll("__MAX_FARE__", maxFare);

                $("#faq_schema").val(formatSchemaForTextarea(schema));
            });
        }
    }

    function formatSchemaForTextarea(schemaValue) {
        try {
            let parsed = schemaValue;

            // If DB/API returns escaped JSON string, parse once
            if (typeof parsed === 'string') {
                parsed = JSON.parse(parsed);
            }

            // If still stringified inside string, parse again
            if (typeof parsed === 'string') {
                parsed = JSON.parse(parsed);
            }

            return JSON.stringify(parsed, null, 4);
        } catch (e) {
            console.error('Schema formatting error:', e);
            return schemaValue || '';
        }
    }
</script>
@endpush