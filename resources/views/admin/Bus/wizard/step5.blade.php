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
                                                        <th>Seize Time ( In Min )</th>
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
                                            <input type="hidden" name="param" value="{{$data['param']}}">
                                            <input type="hidden" name="param2" value="{{$data['param2']}}">
                                            <input type="hidden" name="existRes" value="{{ $data['existRes'] ?? 0 }}">
                                            @php
                                            $isSave = ($data['param'] ?? null) === 'save';
                                            $isBack = ($data['param2'] ?? null) === 'back';
                                            @endphp

                                            @if ($isSave)
                                            <a href="{{ url($createBusUrl.'step4/'.$data['enc_bus_id'].'/save/back') }}"
                                                class="btn btn-secondary px-5 rounded-pill me-3">
                                                ← Back
                                            </a>
                                            @endif

                                            @if ($isSave && $isBack)
                                            <a href="{{ url($createBusUrl.'step6/'.$data['enc_bus_id'].'/save') }}"
                                                class="btn btn-warning px-5 rounded-pill me-3">
                                                Continue →
                                            </a>
                                            @elseif (@$data['existRes'] == 1)
                                            <a href="{{ url($createBusUrl.'step6/'.$data['enc_bus_id'].'/save') }}"
                                                class="btn btn-warning px-5 rounded-pill me-3">
                                                Continue →
                                            </a>
                                            @endif

                                            <button type="submit" class="btn btn-success px-5 rounded-pill">
                                                Save & Continue →
                                            </button>
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
    $('#btnReset').click(function() {
        $(':input', '#backoffice-form').not(':button, :submit, :reset, :hidden').val('');
        $('.form-select').val(0);
        $('.form-select').val('').trigger('change');
    });

    $('#backoffice-form').on('submit', function(e) {

        e.preventDefault();

        let isValid = true;

        $('#routeTableBody .text-center').each(function() {
            let $row = $(this);

            let seatFare = $row.find('.seatFare').val();
            let upperFare = $row.find('.upperSleeperFare').val();
            let lowerFare = $row.find('.lowerSleeperFare').val();
            let close_time = $row.find('.close_time').val();
            let seize_time = $row.find('.seize_time').val();

            if (seatFare === "" && upperFare === "" && lowerFare === "") {
                isValid = false;

                commonAjax.viewAlert('Please enter at least one fare in each row');

                return false;
            }

            if (close_time === "") {
                isValid = false;

                commonAjax.viewAlert('Close Time cannot be left blank');

                return false;
            }

            if (seize_time === "") {
                isValid = false;

                commonAjax.viewAlert('Seize Time cannot be left blank');

                return false;
            }
        });

        if (!isValid) return false;

        commonAjax.confirmAlert('Are you sure to proceed !');

        $('#btnConfirmOk').on('click', function() {
            e.currentTarget.submit();
        });

    });

    let tbody = $("#routeTableBody");
    tbody.empty();

    let routes = <?= json_encode($data['schedule_data']) ?>;
    let step5Res = <?= json_encode($data['step5Res']) ?>;

    let step5 = step5Res || [];

    if (routes && routes.length > 0) {

        routes.forEach((item, index) => {

            let dayOptionsFrom = [1, 2, 3, 4, 5].map(day => `
                <option value="${day}" ${step5[index]?.from_journey_day == day ? 'selected' : ''}>${day}</option>
            `).join('');

            let dayOptionsTo = [1, 2, 3, 4, 5].map(day => `
                <option value="${day}" ${step5[index]?.to_journey_day == day ? 'selected' : ''}>${day}</option>
            `).join('');

            let row = `
                <tr class="text-center">
                    <td>${index + 1}</td>

                    <td>
                        <input type="hidden" value="${item.source_id}" name="from_stop_id[]" />
                        <input type="text" class="form-control form-control-sm" value="${item.source}" disabled />
                    </td>

                    <td>
                        <select class="form-select form-select-sm" name="from_journey_day[]">
                            ${dayOptionsFrom}
                        </select>
                    </td>

                    <td>
                        <input type="hidden" value="${item.destination_id}" name="to_stop_id[]" />
                        <input type="text" class="form-control form-control-sm" value="${item.destination}" disabled />
                    </td>

                    <td>
                        <select class="form-select form-select-sm" name="to_journey_day[]">
                            ${dayOptionsTo}
                        </select>
                    </td>

                    <td>
                        <input type="number" class="form-control form-control-sm seatFare"
                            name="seat_fare[]" placeholder="Enter Seat Fare"
                            value="${step5[index]?.seat_fare ?? ''}">
                    </td>

                    <td>
                        <input type="number" class="form-control form-control-sm upperSleeperFare"
                            name="upper_sleeper_fare[]" placeholder="Enter U-Sleeper Fare"
                            value="${step5[index]?.upper_sleeper_fare ?? ''}">
                    </td>

                    <td>
                        <input type="number" class="form-control form-control-sm lowerSleeperFare"
                            name="lower_sleeper_fare[]" placeholder="Enter L-Sleeper Fare"
                            value="${step5[index]?.lower_sleeper_fare ?? ''}">
                    </td>

                    <td class="closeTimeRow">
                        <input type="hidden" value="${item.city_id}" class="city_id" />
                        <input type="time" class="form-control form-control-sm close_time"
                            name="close_time[]" value="${step5[index]?.close_time ?? ''}">
                    </td>

                    <td>
                        <input type="number" class="form-control form-control-sm seize_time"
                            name="seize_time[]" placeholder="Enter Seize Time" readonly
                            value="${step5[index]?.seize_time ?? ''}">
                    </td>

                    <td>
                        <div class="d-flex justify-content-center gap-2">

                            <input type="hidden" name="active_status[${index}]" value="0">

                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                    type="checkbox"
                                    name="active_status[${index}]"
                                    value="1"
                                    ${step5[index]?.active_status == 1 ? 'checked' : ''}>
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

    } else {

        let row = `
            <tr>
                <td colspan="11" class="text-center text-muted">
                    Schedule Not Found
                </td>
            </tr>
        `;

        tbody.append(row);
    }

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

                    let timeParts = listing_time.split(':');

                    let hours = parseInt(timeParts[0]);
                    let minutes = timeParts[1];

                    let ampm = hours >= 12 ? 'PM' : 'AM';

                    hours = hours % 12;
                    hours = hours ? hours : 12;

                    let formattedListingTime = hours + '.' + minutes + ' ' + ampm;

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
