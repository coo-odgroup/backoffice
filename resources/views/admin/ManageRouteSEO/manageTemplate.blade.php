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

            <div id="boardingDroppingSection" class="d-none mt-4 route-template-wrap">
                <div class="row g-4 align-items-start">
                    <div class="col-lg-8">
                        <div class="card route-main-card shadow-sm border-0">
                            <div class="card-header route-card-header d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="mb-0 fw-bold">Route Content</h5>
                                    <small class="text-muted">Auto-generated route SEO content</small>
                                </div>
                                <span class="route-badge">HTML Content</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="route-content-view" id="content" name="content"></div>
                                <input type="hidden" id="content_html" name="content">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="route-side-panel">

                            <div class="card route-side-card shadow-sm border-0 mb-3">
                                <div class="route-card-header">
                                    <h5>Meta Title</h5>
                                </div>
                                <div class="card-body route-side-body">
                                    <textarea class="form-control seo-textarea" id="meta_title" name="meta_title" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="card route-side-card shadow-sm border-0 mb-3">
                                <div class="route-card-header">
                                    <h5>Meta Description</h5>
                                </div>
                                <div class="card-body route-side-body">
                                    <textarea class="form-control seo-textarea" id="meta_description" name="meta_description" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="card route-side-card shadow-sm border-0 mb-3">
                                <div class="route-card-header">
                                    <h5>Breadcrumb Schema</h5>
                                </div>
                                <div class="card-body route-side-body">
                                    <textarea class="form-control seo-textarea" id="breadcrumb_schema" name="breadcrumb_schema" rows="18"></textarea>
                                </div>
                            </div>

                            <div class="card route-side-card shadow-sm border-0 mb-3">
                                <div class="route-card-header">
                                    <h5>FAQ Schema</h5>
                                </div>
                                <div class="card-body route-side-body">
                                    <textarea class="form-control seo-textarea" id="faq_schema" name="faq_schema" rows="18"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>>
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
<style>
    :root {
        --primary: #0f2b6d;
        --secondary: #2f80ed;
        --success: #18a558;
        --bg: #f4f7fb;
        --card: #ffffff;
        --border: #dfe7f3;
        --text: #253047;
        --muted: #7d8797;
    }

    body {
        background: var(--bg);
    }

    /*---------------------------------------
    Main Layout
    ---------------------------------------*/

    .route-template-wrap {
        animation: fadeIn .35s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    /*---------------------------------------
    Cards
    ---------------------------------------*/

    .route-main-card,
    .route-side-card {
        border: none;
        border-radius: 18px;
        background: white;
        box-shadow:
            0 12px 30px rgba(16, 24, 40, .08),
            0 2px 8px rgba(16, 24, 40, .04);
        transition: .25s;
        padding: 0;
    }

    .route-main-card:hover,
    .route-side-card:hover {
        transform: translateY(-3px);
        box-shadow:
            0 20px 40px rgba(16, 24, 40, .12);
    }

    /*---------------------------------------
    Headers
    ---------------------------------------*/

    .route-card-header {
        background:
            linear-gradient(90deg, #0f2b6d, #214fb7);
        color: white;
        padding: 16px 22px;
        border: none;
    }

    .route-card-header h5 {
        margin: 0;
        color: white;
        font-size: 18px;
        font-weight: 700;
    }

    .route-card-header small {
        color: rgba(255, 255, 255, .8);
    }

    /*---------------------------------------
    Badge
    ---------------------------------------*/

    .route-badge {
        background: white;
        color: var(--primary);
        padding: 6px 14px;
        border-radius: 40px;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 3px 10px rgba(0, 0, 0, .15);
    }

    /*---------------------------------------
    Content
    ---------------------------------------*/

    .route-content-view {
        background: white;
        min-height: 82vh;
        max-height: 82vh;
        overflow: auto;

        padding: 45px 55px;

        line-height: 1.9;
        font-size: 16px;

        color: #2f3c50;

        scrollbar-width: thin;
    }

    .route-content-view::-webkit-scrollbar {
        width: 8px;
    }

    .route-content-view::-webkit-scrollbar-thumb {
        background: #c8d5ec;
        border-radius: 20px;
    }

    .route-content-view h1 {
        font-size: 44px;
        line-height: 1.15;
        font-weight: 800;
        color: #10244b;
        margin-bottom: 30px;
    }

    .route-content-view h2 {
        font-size: 32px;
        font-weight: 700;
        color: #1c3e82;
        margin-top: 45px;
        margin-bottom: 18px;
    }

    .route-content-view h3 {
        font-size: 24px;
        color: #284f97;
        font-weight: 700;
        margin-top: 35px;
    }

    .route-content-view p {
        color: #394455;
        margin-bottom: 18px;
    }

    .route-content-view ul {
        margin-top: 15px;
        margin-bottom: 20px;
    }

    .route-content-view li {
        margin-bottom: 10px;
    }

    .route-content-view strong {
        color: #111827;
    }

    .route-content-view a {
        color: #2563eb;
        font-weight: 600;
    }

    .route-content-view img {
        max-width: 100%;
        border-radius: 12px;
        margin: 20px 0;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .1);
    }

    /*---------------------------------------
    Right Panel
    ---------------------------------------*/

    .route-side-panel {
        position: sticky;
        top: 20px;
    }

    .route-side-card {
        margin-bottom: 18px;
    }

    .route-side-card .card-body {
        padding: 18px;
    }

    /*---------------------------------------
    Inputs
    ---------------------------------------*/

    .seo-textarea {
        border: 1px solid #d7e2ef !important;
        border-radius: 12px !important;
        background: #fbfdff !important;
        font-size: 14px;
        padding: 14px 16px !important;
        transition: .25s;
    }

    .seo-textarea:focus {
        border-color: #2f80ed !important;
        background: white !important;
        box-shadow:
            0 0 0 4px rgba(47, 128, 237, .12) !important;
    }

    #meta_title {
        min-height: 75px;
    }

    #meta_description {
        min-height: 120px;
    }

    #breadcrumb_schema,
    #faq_schema {
        min-height: 320px;
        font-family: Consolas, monospace;
        font-size: 13px;
        background: #0f172a !important;
        color: #dbeafe !important;
        border: none !important;
    }

    /*---------------------------------------
    Filter
    ---------------------------------------*/

    #filterBox {
        background: white;
        border-radius: 18px;
        padding: 20px;
        border: none;
        box-shadow:
            0 8px 20px rgba(0, 0, 0, .06);
    }

    #filterBox label {
        font-weight: 700;
        color: #42526b;
    }

    #filterBox .form-select {
        border-radius: 12px;
        min-height: 44px;
    }

    /*---------------------------------------
    Update Button
    ---------------------------------------*/

    #btnUpdateBoardingDropping {

        position: fixed;
        bottom: 25px;
        right: 35px;

        z-index: 999;

        border: none;

        border-radius: 50px;

        background: linear-gradient(90deg, #18a558, #22c55e);

        color: white;

        padding: 14px 30px;

        font-size: 15px;

        font-weight: 700;

        box-shadow:
            0 15px 35px rgba(24, 165, 88, .35);

        transition: .25s;
    }

    #btnUpdateBoardingDropping:hover {
        transform: translateY(-3px);
        box-shadow:
            0 20px 45px rgba(24, 165, 88, .45);
    }

    /*---------------------------------------
    Responsive
    ---------------------------------------*/

    @media(max-width:991px) {

        .route-side-panel {
            position: static;
        }

        .route-content-view {
            padding: 28px;
            max-height: none;
            min-height: 500px;
        }

        #btnUpdateBoardingDropping {

            left: 15px;
            right: 15px;
            bottom: 15px;

            width: auto;

        }

    }
</style>

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
        $('#content').html('');
        $('#content_html').val('');
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
            $('#content').html(data.content);
            $('#content_html').val(data.content);
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