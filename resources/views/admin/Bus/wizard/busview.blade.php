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
                    @foreach($busBoardingDropping as $city => $stops)
                    @foreach($stops as $stop)
                    <tr>
                        <td>{{ $city }}</td>
                        <td>{{ $stop['type'] == 1 ? 'Boarding' : 'Dropping' }}</td>
                        <td>{{ $stop['location'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($stop['time'])->format('h:i A') }}</td>
                    </tr>
                    @endforeach
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
            {!! $seatLayout !!}
        </div>

    </div>
</div>
