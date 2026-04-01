@extends('admin.layouts.master')
@section('page_title', 'City Timings')
@section('content')

<style>
    #previewList .d-flex {
        cursor: move;
    }
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

                                    <div id="step5">

                                        <h3 class="fw-bold mb-4 border-bottom pb-2">Bus Schedule</h3>

                                        <div class="table-responsive">

                                            <table class="table table-hover table-condensed">

                                                <thead class="border-bottom">
                                                    <tr class="text-left">
                                                        <th>#</th>
                                                        <th>Source</th>
                                                        <th>Days</th>
                                                        <th>Destination</th>
                                                        <th>Days</th>
                                                        <th>Seat Fare</th>
                                                        <th>U-Sleeper</th>
                                                        <th>L-Sleeper</th>
                                                        <th>Close Time</th>
                                                        <th>Seize Time</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="routeTableBody">
                                                </tbody>
                                            </table>
                                        </div>


                                        <!-- Buttons -->
                                        <div class="text-center mt-1">
                                            <input type="hidden" name="bus_id" value="{{$data['bus_id']}}">
                                            <button type="button" class="btn btn-warning px-5 rounded-pill me-4" onclick="backStep()">
                                                Back
                                            </button>
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
    function backStep() {
        document.getElementById("step1").style.display = "block";
        document.getElementById("step2").style.display = "none";
    }

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

    let tbody = $("#routeTableBody"); // your tbody id
    tbody.empty();

    let routes = <?= json_encode($data['schedule_data']) ?>;

    routes.forEach((item, index) => {

        let row = `
            <tr class="text-center">
                <td>${index + 1}</td>

                <td>
                    <input type="hidden" value="${item.source_id}" name="from_stop_id[]" />
                    <input type="text" class="form-control form-control-sm" value="${item.source}" disabled />
                </td>

                <td>
                    <select class="form-select form-select-sm" name="from_journey_day[]">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </td>

                <td>
                    <input type="hidden" value="${item.destination_id}" name="to_stop_id[]" />
                    <input type="text" class="form-control form-control-sm" value="${item.destination}" disabled />
                </td>

                <td>
                    <select class="form-select form-select-sm" name="to_journey_day[]">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </td>

                <td>
                    <input type="number" class="form-control form-control-sm" name="seat_fare[]" placeholder="Enter Seat Fare">
                </td>

                <td>
                    <input type="number" class="form-control form-control-sm" name="upper_sleeper_fare[]" placeholder="Enter U-Sleeper Fare">
                </td>

                <td>
                    <input type="number" class="form-control form-control-sm" name="lower_sleeper_fare[]" placeholder="Enter L-Sleeper Fare">
                </td>

                <td class="closeTimeRow">
                    <input type="hidden" value="${item.city_id}" class="city_id" />
                    <input type="time" class="form-control form-control-sm close_time" name="close_time[]">
                </td>

                <td>
                    <input type="number" class="form-control form-control-sm seize_time" name="seize_time[]" placeholder="Enter Seize Time">
                </td>

                <td>
                    <div class="d-flex justify-content-center gap-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="active_status[]" value="1">
                        </div>
                        <button class="btn btn-outline-danger btn-sm removeRow">
                            ✕
                        </button>
                    </div>
                </td>

            </tr>
        `;

        tbody.append(row);
    });

    $(document).on("click", ".removeRow", function() {
        $(this).closest("tr").remove();
    });

    $(document).on("change", ".close_time", function() {

        let closeRow = $(this).closest(".closeTimeRow");
        let closestTr = $(this).closest("tr");

        let city_id = closeRow.find(".city_id").val();
        let close_time = $(this).val(); // HH:MM

        $.ajax({
            url: "/admin/get-listing-time",
            type: "POST",
            data: {
                city_id: city_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {

                let listing_time = res.listing_time; // HH:MM:SS

                if (!listing_time) return;

                // Convert to minutes
                let closeMinutes = timeToMinutes(close_time);
                let listingMinutes = timeToMinutes(listing_time);

                if (closeMinutes > listingMinutes) {

                    // format listing_time (HH:MM:SS → HH:MM)
                    let formattedListingTime = listing_time.substring(0, 5);

                    commonAjax.viewAlert(
                        "Close time cannot be greater than listing time (" + formattedListingTime + ")"
                    );

                    return;
                }

                // ✅ Calculate seizeTime
                let seizeMinutes = listingMinutes - closeMinutes;
                // let seizeTime = minutesToTime(seizeMinutes);
                closestTr.find(".seize_time").val(seizeMinutes);

                console.log("Seize Time:", seizeMinutes);
            }
        });

    });

    function timeToMinutes(time) {
        let parts = time.split(":");
        let hours = parseInt(parts[0]);
        let minutes = parseInt(parts[1]);
        return (hours * 60) + minutes;
    }

    function minutesToTime(minutes) {
        let h = Math.floor(minutes / 60);
        let m = minutes % 60;

        return String(h).padStart(2, '0') + ":" + String(m).padStart(2, '0');
    }
</script>
@endpush