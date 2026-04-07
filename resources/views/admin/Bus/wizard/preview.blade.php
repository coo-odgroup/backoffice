@extends('admin.layouts.master')
@section('page_title', 'Preview')
@section('content')

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item active">Preview</li>
    </ol>
</nav>

<!-- ================= HEADER (COMMENTED AS REQUESTED) ================= -->
<div class="d-flex justify-content-between align-items-center">
    <h5 class="bpv-title">Bus Preview</h5>
    <button onclick="window.print()" class="btn btn-success btn-sm">Print</button>
</div>

<div class="bpv-main-box">
    <div class="bpv-wrapper">

        <!-- ================= BUS INFO ================= -->
        <div class="bpv-card">
            <h6 class="bpv-heading">
                <i class="bi bi-bus-front-fill bpv-icon"></i> Bus Info
            </h6>

            <div class="bpv-grid">
                <div><span>Operator</span>
                    <p>{{$bus_record['operator']['name']}}</p>
                </div>
                <div><span>Bus Name/No</span>
                    <p>{{$bus_record['name']}} / {{$bus_record['bus_number']}}</p>
                </div>
                <div><span>Via</span>
                    <p>{{$bus_record['via']}}</p>
                </div>
                <div><span>Max Seat</span>
                    <p>{{$bus_record['max_seat_book']}}</p>
                </div>
                <div><span>Bus Type</span>
                    <p>{{$bus_record['gen_bus_type']}}</p>
                </div>
                <div><span>IRCTC Model</span>
                    <p>{{($bus_record['is_irctc_model']==1)?'YES':'NO'}}</p>
                </div>
            </div>
        </div>

        <!-- ================= AMENITIES ================= -->
        <div class="bpv-card">
            <h6 class="bpv-heading">
                <i class="bi bi-stars bpv-icon"></i> Amenities
            </h6>

            <div class="bpv-amenities">

                <!-- <div class="bpv-amenity-col">
                    <h6>Seating & Comfort</h6>

                    <div class="bpv-amenity-item">
                        <i class="bi bi-easel-fill"></i>
                        Pushback Seats
                    </div>

                    <div class="bpv-amenity-item">
                        <i class="bi bi-layout-sidebar-inset"></i>
                        Window Curtains
                    </div>
                </div> -->

                @foreach($amennity_records as $category => $items)
                <div class="bpv-amenity-col">

                    <h6>{!! html_entity_decode($category) !!}</h6>

                    @foreach($items as $item)
                    <div class="bpv-amenity-item">

                        <i class="{{ $item->icon }}"></i>

                        {!! html_entity_decode($item->amenity_name) !!}
                    </div>
                    @endforeach

                </div>
                @endforeach

            </div>
        </div>

        <!-- ================= CANCELLATION ================= -->
        <div class="bpv-card">
            <h6 class="bpv-heading">
                <i class="bi bi-x-circle-fill bpv-icon text-danger"></i> Cancellation Slab
            </h6>

            <table class="table bpv-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Hours Before Departure</th>
                        <th>Cancellation Charges (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bus_record->cancellationslab->SlabInfo as $k => $val)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$val['duration']}}</td>
                        <td>{{$val['deduction']}}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ================= CITY ================= -->
        <div class="bpv-card">
            <h6 class="bpv-heading">
                <i class="bi bi-geo-alt-fill bpv-icon text-danger"></i> City Selection
            </h6>

            <table class="table bpv-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>City Name</th>
                        <th>Boarding</th>
                        <th>Dropping</th>
                        <th>Timing</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($busRoutesStops as $k => $val)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$val['city']['city_name']}}</td>
                        <td>{{($val['is_boarding']==1)?'Yes':'No'}}</td>
                        <td>{{($val['is_dropping']==1)?'Yes':'No'}}</td>
                        <td>{{ date('g:i A', strtotime($val['listing_time'])) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ================= STATIONS ================= -->
        <div class="bpv-card">
            <h6 class="bpv-heading">
                <i class="bi bi-signpost-2-fill bpv-icon"></i> Stations
            </h6>

            <table class="table bpv-table">
                <thead>
                    <tr>
                        <th>City</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($busBoardingDropping as $k => $val)
                    <tr>
                        <td>{{$val['city']['city_name']}}</td>
                        <td>{{($val['type']==1)?'Boarding':'Dropping'}}</td>
                        <td>{{(@$val['stop']['city_name']!='')?$val['stop']['city_name']:'--'}}</td>
                        <td>{{ date('g:i A', strtotime($val['timing'])) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ================= SCHEDULE ================= -->
        <div class="bpv-card">
            <h6 class="bpv-heading">
                <i class="bi bi-calendar-check bpv-icon text-primary"></i> Schedule
            </h6>

            <table class="table bpv-table">
                <thead>
                    <tr>
                        <th>Source</th>
                        <th>Day</th>
                        <th>Destination</th>
                        <th>Day</th>
                        <th>Seat Fare</th>
                        <th>Upper Sleeper</th>
                        <th>Lower Sleeper</th>
                        <th>Seize Time</th>
                        <th>Close Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($busRouteFares as $k => $val)
                    <tr>
                        <td>{{$val['source']['city_name']}}</td>
                        <td>{{$val['from_journey_day']}}</td>
                        <td>{{$val['destination']['city_name']}}</td>
                        <td>{{$val['to_journey_day']}}</td>
                        <td>{{($val['seat_fare']!='')?$val['seat_fare']:'--'}}</td>
                        <td>{{($val['upper_sleeper_fare']!='')?$val['upper_sleeper_fare']:'--'}}</td>
                        <td>{{($val['lower_sleeper_fare']!='')?$val['lower_sleeper_fare']:'--'}}</td>
                        <td>{{$val['seize_time']}} M</td>
                        <td>{{ date('g:i A', strtotime($val['close_time'])) }}</td>
                        <td><span class="bpv-badge">{{($val['active_status']==1)?'Active':'Inactive'}}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ================= CONTACT ================= -->
        <div class="bpv-card">
            <h6 class="bpv-heading">
                <i class="bi bi-telephone-fill bpv-icon text-success"></i> Contact
            </h6>

            @foreach($busContacts as $contact)
            @if($contact->phone)
            <div class="bpv-grid">
                <div>
                    <span>{{ $contact->type == 0 ? 'Conductor' : ($contact->type == 1 ? 'Manager' : 'Owner') }}</span>
                    <p>{{ $contact->phone }}</p>
                </div>

                <div>
                    <span>SMS On Ticket</span>
                    <p>{{ $contact->booking_sms_send ? 'Yes' : 'No' }}</p>
                </div>

                <div>
                    <span>SMS On Cancel</span>
                    <p>{{ $contact->cancel_sms_send ? 'Yes' : 'No' }}</p>
                </div>

                <div>
                    <span>WhatsApp Ticket</span>
                    <p>{{ $contact->booking_wp_send ? 'Yes' : 'No' }}</p>
                </div>

                <div>
                    <span>WhatsApp Cancel</span>
                    <p>{{ $contact->cancel_wp_send ? 'Yes' : 'No' }}</p>
                </div>
            </div>
            @endif
            @endforeach
        </div>

        <!-- ================= SEAT LAYOUT ================= -->
        <div class="bpv-card">
            <h6 class="bpv-heading">
                <i class="bi bi-grid-3x3-gap-fill bpv-icon"></i> Seat Layout
            </h6>

            <div class="bpv-seat-box">
                <div class="seat-left">
                    <div class="bus-layout">

                        <!-- UPPER BERTH -->
                        <div class="berth-row berth-section active" id="upper-berth-box">
                            <div class="berth-label">Upper Berth</div>

                            <div class="layout-box">
                                <!-- Row 1 -->
                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U1">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U1</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U2">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U2</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U3">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U3</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U4">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U4</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U5">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U5</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U6">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U6</span>
                                </label>

                                <!-- Row 2 -->
                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U7">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U7</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U8">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U8</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U9">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U9</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U10">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U10</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U11">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U11</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U12">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U12</span>
                                </label>

                                <!-- Empty Row -->
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>

                                <!-- Row 3 -->
                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U13">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U13</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U14">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U14</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U15">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U15</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U16">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U16</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U17">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U17</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U18">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U18</span>
                                </label>

                                <!-- Row 4 -->
                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U19">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U19</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U20">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U20</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U21">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U21</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U22">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U22</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U23">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U23</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U24">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U24</span>
                                </label>
                            </div>
                        </div>

                        <!-- LOWER BERTH -->
                        <div class="berth-row berth-section" id="lower-berth-box">
                            <div class="berth-label">Lower Berth</div>

                            <div class="layout-box">
                                <!-- Row 1 -->
                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L1">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L1</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L2">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L2</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L3">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L3</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L4">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L4</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L5">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L5</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L6">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L6</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L7">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L7</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L8">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L8</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L9">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L9</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L10">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L10</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L11">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L11</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L12">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L12</span>
                                </label>

                                <!-- Row 2 -->
                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L13">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L13</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L14">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L14</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L15">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L15</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L16">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L16</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L17">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L17</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L18">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L18</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L19">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L19</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L20">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L20</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L21">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L21</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L22">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L22</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L23">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L23</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L24">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L24</span>
                                </label>

                                <!-- Empty Row -->
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>
                                <div class="empty"></div>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L25">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L25</span>
                                </label>

                                <!-- Row 3 -->
                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L26">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L26</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L27">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L27</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L28">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L28</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L29">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L29</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L30">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L30</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L31">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L31</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L32">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L32</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L33">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L33</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L34">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L34</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L35">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L35</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L36">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L36</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L37">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L37</span>
                                </label>

                                <!-- Row 4 -->
                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L38">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L38</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L39">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L39</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L40">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L40</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L41">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L41</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L42">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L42</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L43">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L43</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L44">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L44</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L45">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L45</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L46">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L46</span>
                                </label>

                                <label class="seat-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="L47">
                                    <span class="bus-seat" data-type="seat"></span>
                                    <span class="seat-number">L47</span>
                                </label>

                                <label class="seat-wrap sleeper-wrap">
                                    <input type="checkbox" class="seat-checkbox" name="seat_no[]" value="U12">
                                    <span class="bus-sleeper" data-type="sleeper"></span>
                                    <span class="seat-number">U18</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="bpv-seat-info">
                <div><span>Window Seats:</span>
                    <p>20</p>
                </div>
                <div><span>Female Seats:</span>
                    <p>10</p>
                </div>
                <div><span>VIP Seats:</span>
                    <p>5</p>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- BACK BUTTON -->
<div class="text-center mt-4">
    <a href="{{ url()->previous() }}" class="bpv-back-btn">
        ← Back
    </a>
</div>

@endsection