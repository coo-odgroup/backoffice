@extends('admin.layouts.master')
@section('page_title', 'City Selection')
@section('content')

<style>

</style>

<?php
$page_name = 'All ' . trim($__env->yieldContent('page_title'));
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
        <a href="{{ route('amenities.index') }}" class="btn btn-success btn-sm">
            View @yield('page_title')
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

                                <!-- POST FIELDS -->
                                <div class="col-12">

                                    <!-- ================= STEP 2 ================= -->

                                    <div id="step2">
                                        <div class="row">
                                            <!-- LEFT SIDE -->
                                            <div class="col-md-6">
                                                <div class="d-flex mb-1">
                                                    <input type="text" id="citySearch" class="form-control form-control-sm clearable" placeholder="Search By City Name">
                                                </div>
                                                <div id="cityList" class="city-scroll"></div>
                                            </div>


                                            <!-- RIGHT SIDE -->
                                            <div class="col-md-6">
                                                <h6 class="mb-3">Preview</h6>
                                                <div id="previewList"></div>
                                            </div>
                                        </div>


                                        <!-- STEP 2 BUTTONS -->
                                        <div class="text-center mt-4">
                                            <input type="hidden" name="bus_id" value="{{$data['bus_id']}}">
                                            <a href="{{ url($createBusUrl.'step1/'.$data['enc_bus_id']) }}" class="btn btn-warning px-5 rounded-pill me-3">
                                                ← Back
                                            </a>
                                            <button type="submit" class="btn btn-warning px-5 rounded-pill">Next →</button>
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
    </div>
</form>
@endsection
@push('scripts')

<script type="module">
    document.addEventListener("DOMContentLoaded", function() {
        updatePreview();
        syncCheckboxes();
    });

    function getShimmerHTML(count = 5) {

        let shimmer = '';

        for (let i = 0; i < count; i++) {
            shimmer += `
                <div class="d-flex align-items-center mb-2">
                    <div class="shimmer-box me-2"></div>
                    <div class="shimmer-line flex-grow-1"></div>
                </div>
            `;
        }

        return shimmer;
    }

    $(document).on('change', '.cityCheck', function() {
        toggleCity(this);
    });

    let selectedCities = new Map(
        JSON.parse(localStorage.getItem("selectedCities") || "[]")
    );

    function saveToLocalStorage() {
        localStorage.setItem(
            "selectedCities",
            JSON.stringify([...selectedCities])
        );
    }


    function toggleCity(checkbox) {

        let cityId = checkbox.value;
        let cityName = $(checkbox).data('name');

        if (checkbox.checked) {
            selectedCities.set(cityId, cityName);
        } else {
            selectedCities.delete(cityId);
        }

        saveToLocalStorage();
        updatePreview();
    }

    function updateCityIndex() {

        $('#previewList .city-index').each(function(index) {
            $(this).text(index + 1 + '.');
        });

    }

    function updateOrderIndex() {

        $('#previewList .order-index').each(function(index) {
            $(this).val(index + 1);
        });

    }

    function checkPreviewEmpty() {

        let preview = document.getElementById("previewList");

        if (preview.children.length === 0) {
            preview.innerHTML = `<p class="text-muted mb-0">No city is added</p>`;
        }
    }

    function removeCity(city) {

        selectedCities.delete(city);

        saveToLocalStorage();
        updatePreview();
        syncCheckboxes();
    }

    function updatePreview() {

        let preview = document.getElementById("previewList");
        preview.innerHTML = '';

        if (selectedCities.size === 0) {
            preview.innerHTML = "<p>No city is added</p>";
            return;
        }

        let index = 0;

        selectedCities.forEach((cityName, cityId) => {

            index++;

            let div = document.createElement("div");
            div.className = "d-flex align-items-center mb-2";
            div.draggable = true;

            div.innerHTML = `
                <span class="me-2 city-index fw-bold"></span>
                <span class="form-control form-control-sm flex-grow-1 me-2 city_validation">${cityName}</span>
                <input type="hidden" name="cities[${index}][id]" value="${cityId}">
                <input type="hidden" name="cities[${index}][order]" value="${index}" class="order-index">
                <button class="btn btn-danger btn-sm" type="button">
                    <i class="fa fa-trash"></i>
                </button>
            `;

            div.querySelector("button").addEventListener("click", function() {
                removeCity(cityId);
            });

            addDragEvents(div);
            preview.appendChild(div);
        });

        updateCityIndex();
        updateOrderIndex();
    }

    function syncCheckboxes() {

        $('.cityCheck').each(function() {

            let city = $(this).val();

            $(this).prop('checked', selectedCities.has(city));
        });
    }


    // DRAG SORT FUNCTION
    let dragItem = null;

    function addDragEvents(element) {

        element.addEventListener("dragstart", function() {
            dragItem = element;
            element.classList.add("dragging");
        });

        element.addEventListener("dragover", function(e) {
            e.preventDefault();
        });

        element.addEventListener("drop", function(e) {
            e.preventDefault();

            if (dragItem !== element) {

                let parent = element.parentNode;

                let items = [...parent.children];
                let dragIndex = items.indexOf(dragItem);
                let dropIndex = items.indexOf(element);

                if (dragIndex < dropIndex) {
                    parent.insertBefore(dragItem, element.nextSibling);
                } else {
                    parent.insertBefore(dragItem, element);
                }

                updateCityIndex();
                updateOrderIndex();
                updateLocalStorageOrder(dragIndex, dropIndex);
            }
        });

        element.addEventListener("dragend", function() {
            dragItem = null;
            element.classList.remove("dragging");
        });
    }

    function updateLocalStorageOrder(fromIndex, toIndex) {

        let stored = JSON.parse(localStorage.getItem('selectedCities')) || [];

        if (stored.length === 0) return;

        // ✅ Move item
        let movedItem = stored.splice(fromIndex, 1)[0];
        stored.splice(toIndex, 0, movedItem);

        // ✅ Save updated order
        localStorage.setItem('selectedCities', JSON.stringify(stored));

        // 🔥 VERY IMPORTANT: Sync Map with new order
        selectedCities = new Map(stored);

        console.log("Updated localStorage:", stored);
    }

    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('.onSelect').on('change', function() {
        generateBusName();
    });

    $(document).ready(function() {

        searchCity();
        commonAjax.initClearableInputs();
        checkPreviewEmpty();

    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        let selectedCities = [];

        try {
            selectedCities = JSON.parse(localStorage.getItem('selectedCities')) || [];
        } catch (e) {
            selectedCities = [];
        }

        if (selectedCities.length < 3) {
            commonAjax.viewAlert("Please select at least 3 cities");
            return false;
        }

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    $('#citySearch').on('keyup', function() {

        let city = $(this).val();

        if (city.length >= 3) {
            searchCity(city);
        } else if (city.length === 0) {
            searchCity(); // reload all when cleared
        }
    });

    function searchCity(city = "") {

        $("#cityList").html(getShimmerHTML(6));
        $.ajax({
            type: "POST",
            url: "/admin/get-city-search",
            data: {
                city: city,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",

            success: function(response) {

                let html = "";

                if (response.status && response.data.length > 0) {

                    $.each(response.data, function(index, c) {

                        html += `
                        <div class="checkbox">
                            <input type="checkbox"
                                class="cityCheck"
                                value="${c.id}"
                                data-name="${c.city_name}">
                            ${c.city_name}
                        </div>`;
                    });

                } else {
                    html = `<p class="text-danger">No city found</p>`;
                }

                $("#cityList").html(html);

                syncCheckboxes(); // ✅ restore checked state
            },

            error: function() {
                $("#cityList").html(`<p class="text-danger">Error loading cities</p>`);
            }
        });
    }
</script>
@endpush
