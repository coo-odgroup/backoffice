@extends('admin.layouts.master')
@section('page_title', 'Template Management')
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
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <h5 class="card-header fw-bold" style="color:#1b2155;">
                                Content
                            </h5>
                            <div class="card-body">
                                <div class="form-control" id="content" name="content"
                                    style="min-height: 500px; overflow:auto; background:#fff;"></div>
                                <input type="hidden" id="content_html" name="content">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card mb-2 ">
                            <h5 class="card-header fw-bold" style="color:#1b2155;">
                                Meta Title
                            </h5>
                            <div class="card-body ">
                                <textarea class="form-control border_none_route " id="meta_title" name="meta_title" rows="1"></textarea>
                            </div>
                        </div>
                        <div class="card mb-2">
                            <h5 class="card-header fw-bold" style="color:#1b2155;">
                                Meta Description
                            </h5>
                            <div class="card-body">
                                <textarea class="form-control border_none_route" id="meta_description" name="meta_description" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="card mb-2">
                            <h5 class="card-header fw-bold" style="color:#1b2155;">
                                Breadcrumb Schema
                            </h5>
                            <div class="card-body">
                                <textarea class="form-control border_none_route " id="breadcrumb_schema" name="breadcrumb_schema" rows="18"></textarea>
                            </div>
                        </div>
                        <div class="card">
                            <h5 class="card-header fw-bold" style="color:#1b2155;">
                                FAQ Schema
                            </h5>
                            <div class="card-body">
                                <textarea class="form-control border_none_route" id="faq_schema" name="faq_schema" rows="18"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-success btn-sm d-none mt-4" id="btnUpdateBoardingDropping">
                <i class="fa-solid fa-floppy-disk me-1"></i> Update All
            </button>
        </div>
    </div>


    <div class="footer-background border-success text-center" id="norecord" style="display:none">
        Select a route to view template details.
    </div>
    {{csrf_field()}}
    <input type="hidden" id="hdn_model" value="ManageTemplate">

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
        toggleTemplateSection(false);
    });

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val('').trigger('change');

        clearTemplateFields();
        toggleTemplateSection(false);
        $('#norecord').show().text('Select a route to view template details.');
    });

    window.searchRouteDistance = function() {
        let routeId = ($('#route_id').val() || '').toString().trim();

        if (!routeId || routeId === '0' || routeId === 'null' || routeId === 'undefined') {
            toggleTemplateSection(false);
            $('#norecord').show().text('Please select a route before searching.');
            return;
        }

        getTemplateData();
    };

    function getTemplateData() {
        $.ajax({
            url: "{{ route('manage-template.dataTableView') }}",
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
                    renderTemplateData(response.data);
                    toggleTemplateSection(true);
                } else {
                    clearTemplateFields();
                    toggleTemplateSection(false);
                    $('#norecord').show().text(response.message || 'No route data found.');
                }
            },
            error: function() {
                clearTemplateFields();
                toggleTemplateSection(false);
                $('#norecord').show().text('Failed to load route data.');
            },
            complete: function() {
                if (typeof viewLoader === 'function') {
                    viewLoader(false);
                }
            }
        });
    }

    function renderTemplateData(data) {
        generateRouteSchemas(data);
        $('#btnUpdateBoardingDropping').removeClass('d-none');
    }

    $('#btnUpdateBoardingDropping').on('click', function() {
        let routeId = ($('#route_id').val() || '').toString().trim();

        if (!routeId) {
            $('#norecord').show().text('Please select a route before updating.');
            return;
        }

        $.ajax({
            url: "{{ route('manage-template.add') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                route_id: routeId,
                content: $('#content').html(),
                meta_title: $('#meta_title').val(),
                meta_description: $('#meta_description').val(),
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
                    if (typeof viewAlert === 'function') {
                        viewAlert('success', response.message || 'Updated successfully.');
                    } else if (typeof commonAjax !== 'undefined' && typeof commonAjax.viewAlert === 'function') {
                        commonAjax.viewAlert('Successfully updated', response.message || 'Updated successfully.');
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Updated successfully.'
                        });
                    }

                    getTemplateData();
                } else {
                    if (typeof viewAlert === 'function') {
                        viewAlert('error', response.message || 'Something went wrong.');
                    } else if (typeof commonAjax !== 'undefined' && typeof commonAjax.viewAlert === 'function') {
                        commonAjax.viewAlert('Error', response.message || 'Something went wrong.');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Something went wrong.'
                        });
                    }
                }
            },
            error: function() {
                if (typeof viewAlert === 'function') {
                    viewAlert('error', 'Failed to update template data.');
                } else if (typeof commonAjax !== 'undefined' && typeof commonAjax.viewAlert === 'function') {
                    commonAjax.viewAlert('Error', 'Failed to update template data.');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update template data.'
                    });
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

    function toggleTemplateSection(show = false) {
        $('#boardingDroppingSection').toggleClass('d-none', !show);

        if (!show) {
            $('#norecord').show();
            $('#btnUpdateBoardingDropping').addClass('d-none');
        } else {
            $('#norecord').hide();
            $('#btnUpdateBoardingDropping').removeClass('d-none');
        }
    }

    function clearTemplateFields() {
        $('#content').val('');
        $('#meta_title').val('');
        $('#meta_description').val('');
        $('#breadcrumb_schema').val('');
        $('#faq_schema').val('');
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

        const source = data.source_city_name || data.source || '';
        const destination = data.destination_city_name || data.destination || '';

        const sourceSlug = slugifyRoutePart(source);
        const destinationSlug = slugifyRoutePart(destination);

        const duration = data.duration_in_hours ?? data.from_hrs ?? '';
        const firstBusTiming = data.first_bus_timing ?? '';
        const lastBusTiming = data.last_bus_timing ?? '';
        const minFare = data.min_fare ?? '';
        const maxFare = data.max_fare ?? '';
        const busCount = data.bus_count ?? '';
        const distance = data.distance ?? '';
        const operatorsCount = data.operators_count ?? '';
        const operatorsList = data.operators_list_html ?? '';
        const busTypes = data.bus_types_html ?? '';
        const boardingPoints = data.boarding_points_html ?? '';
        const droppingPoints = data.dropping_points_html ?? '';
        const sourceContent = data.source_content ?? '';
        const destinationContent = data.destination_content ?? '';
        const bookingUrl = `${frontUrl}/routes/${sourceSlug}-${destinationSlug}-bus-services`;
        const returnJourneyUrl = `${frontUrl}/routes/${destinationSlug}-${sourceSlug}-bus-services`;
        const priceRange = (minFare && maxFare) ? `${minFare} to ${maxFare}` : '';

        // =========================
        // CONTENT
        // =========================
        if (data.content && data.content.trim() !== '') {
            $('#content').val(data.content);
        } else {
            commonAjax.getSchemaContent("Routes", "Content", function(response) {
                if (!response.status || !response.data || !response.data.schema_content) {
                    console.log(response.message || 'Content template not found');
                    return;
                }

                let content = response.data.schema_content;

                content = content
                    .replaceAll("__SOURCE__", source)
                    .replaceAll("__DESTINATION__", destination)
                    .replaceAll("__DURATION__", duration)
                    .replaceAll("__FIRST_BUS_TIMING__", firstBusTiming)
                    .replaceAll("__LAST_BUS_TIMING__", lastBusTiming)
                    .replaceAll("__MIN_FARE__", minFare)
                    .replaceAll("__MAX_FARE__", maxFare)
                    .replaceAll("__BUS_COUNT__", busCount)
                    .replaceAll("__DISTANCE__", distance)
                    .replaceAll("__OPERATORS_COUNT__", operatorsCount)
                    .replaceAll("__OPERATORS_LIST__", operatorsList)
                    .replaceAll("__BUS_TYPES__", busTypes)
                    .replaceAll("__BOARDING_POINTS_LIST__", boardingPoints)
                    .replaceAll("__DROPPING_POINTS_LIST__", droppingPoints)
                    .replaceAll("__RETURN_JOURNEY__", returnJourneyUrl)
                    .replaceAll("__SOURCE_CONTENT__", sourceContent)
                    .replaceAll("__DESTINATION_CONTENT__", destinationContent)
                    .replaceAll("__BOOKING_URL__", bookingUrl)
                    .replaceAll("__PRICE_RANGE__", priceRange);

                $('#content').html(content);
                $('#content_html').val(content);
            });
        }

        // =========================
        // META TITLE
        // =========================
        if (data.meta_title && data.meta_title.trim() !== '') {
            $('#meta_title').val(data.meta_title);
        } else {
            commonAjax.getSchemaContent("Routes", "Meta Title", function(response) {
                if (!response.status || !response.data || !response.data.schema_content) {
                    console.log(response.message || 'Meta Title template not found');
                    return;
                }

                let metaTitle = response.data.schema_content;
                metaTitle = metaTitle
                    .replaceAll("__SOURCE__", source)
                    .replaceAll("__DESTINATION__", destination)
                    .replaceAll("__MIN_FARE__", minFare);

                $('#meta_title').val(metaTitle);
            });
        }

        // =========================
        // META DESCRIPTION
        // =========================
        if (data.meta_description && data.meta_description.trim() !== '') {
            $('#meta_description').val(data.meta_description);
        } else {
            commonAjax.getSchemaContent("Routes", "Meta Description", function(response) {
                if (!response.status || !response.data || !response.data.schema_content) {
                    console.log(response.message || 'Meta Description template not found');
                    return;
                }

                let metaDescription = response.data.schema_content;
                metaDescription = metaDescription
                    .replaceAll("__SOURCE__", source)
                    .replaceAll("__DESTINATION__", destination)
                    .replaceAll("__MIN_FARE__", minFare);

                $('#meta_description').val(metaDescription);
            });
        }

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
                    .replaceAll("__FROM_HRS__", duration)
                    .replaceAll("__TO_HRS__", duration)
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

            if (typeof parsed === 'string') {
                parsed = JSON.parse(parsed);
            }

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