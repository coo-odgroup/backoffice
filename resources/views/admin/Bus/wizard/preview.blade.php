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

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center">
    <h5 class="bpv-title">Bus Preview</h5>
    <button onclick="window.print()" class="btn btn-success btn-sm">Print</button>
</div>

<div class="bpv-main-box">
    <div class="bpv-wrapper">

    <!-- BUS INFO -->
    <div class="bpv-card">
        <h6 class="bpv-heading">
            <i class="bi bi-bus-front-fill bpv-icon"></i> Bus Info
        </h6>

        <div class="bpv-grid">
            <div><span>Operator</span>
                <p>ABC Travel</p>
            </div>
            <div><span>Bus Name/No</span>
                <p>Dildar Bus / OD02 AV 2545</p>
            </div>
            <div><span>Via</span>
                <p>Balasore, Soro, Bhadhrak</p>
            </div>
            <div><span>Max Seat</span>
                <p>6</p>
            </div>
            <div><span>Bus Type</span>
                <p>Scania Lift Axle AC Seater 1+2</p>
            </div>
        </div>
    </div>

    <!-- AMENITIES -->
    <div class="bpv-card">
        <h6 class="bpv-heading">
            <i class="bi bi-stars bpv-icon"></i> Amenities
        </h6>

        <div class="bpv-amenities">

            <!-- Seating & Comfort -->
            <div class="bpv-amenity-col">
                <h6>Seating & Comfort</h6>

                <div class="bpv-amenity-item">
                    <i class="bi bi-easel-fill"></i>
                    Pushback Seats
                </div>

                <div class="bpv-amenity-item">
                    <i class="bi bi-layout-sidebar-inset"></i>
                    Window Curtains
                </div>
            </div>

            <!-- Climate Control -->
            <div class="bpv-amenity-col">
                <h6>Climate Control</h6>

                <div class="bpv-amenity-item">
                    <i class="bi bi-snow2"></i>
                    Air Conditioning (AC)
                </div>

                <div class="bpv-amenity-item">
                    <i class="bi bi-wind"></i>
                    Individual Air Vents
                </div>
            </div>

        </div>
    </div>

    <!-- CANCELLATION -->
    <div class="bpv-card">
        <h6 class="bpv-heading">
            <i class="bi bi-x-circle-fill bpv-icon text-danger"></i> Cancellation Slab
        </h6>

        <table class="table bpv-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Hours Before</th>
                    <th>Charges</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>12-24</td>
                    <td>25%</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>12-35</td>
                    <td>30%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- CITY -->
    <div class="bpv-card">
        <h6 class="bpv-heading">
            <i class="bi bi-geo-alt-fill bpv-icon text-danger"></i> City Selection
        </h6>

        <table class="table bpv-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>City</th>
                    <th>Boarding</th>
                    <th>Dropping</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Bhubaneswar</td>
                    <td>Yes</td>
                    <td>No</td>
                    <td>20:30</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Cuttack</td>
                    <td>Yes</td>
                    <td>No</td>
                    <td>21:30</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Jajpur</td>
                    <td>Yes</td>
                    <td>No</td>
                    <td>21:30</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- SCHEDULE -->
    <div class="bpv-card">
        <h6 class="bpv-heading">
            <i class="bi bi-calendar-check bpv-icon text-primary"></i> Schedule
        </h6>

        <table class="table bpv-table">
            <thead>
                <tr>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Fare</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Bhubaneswar</td>
                    <td>Rourkela</td>
                    <td>₹480</td>
                    <td><span class="bpv-badge">Active</span></td>
                </tr>
                <tr>
                    <td>Bhubaneswar</td>
                    <td>Balasore</td>
                    <td>₹350</td>
                    <td><span class="bpv-badge">Active</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- CONTACT -->
    <div class="bpv-card">
        <h6 class="bpv-heading">
            <i class="bi bi-telephone-fill bpv-icon text-success"></i> Contact
        </h6>

        <div class="bpv-grid">
            <div><span>Conductor</span>
                <p>9887744412</p>
            </div>
            <div><span>SMS</span>
                <p>Enabled</p>
            </div>
            <div><span>WhatsApp</span>
                <p>Enabled</p>
            </div>
        </div>
        <div class="bpv-grid">
            <div><span>Manager</span>
                <p>9887744412</p>
            </div>
            <div><span>SMS</span>
                <p>Enabled</p>
            </div>
            <div><span>WhatsApp</span>
                <p>Enabled</p>
            </div>
        </div>
    </div>

    <div class="bpv-card">
        <h6 class="bpv-heading">
            <i class="bi bi-grid-3x3-gap-fill bpv-icon"></i> Seat Layout
        </h6>

        <div class="bpv-seat-box">
            <!-- LEFT : SEAT LAYOUT -->
            <div class="seat-left">

                <div class="bus-layout">

                    <!-- UPPER BERTH -->
                    <div class="berth-row">
                        <div class="berth-label">Upper Berth</div>

                        <div class="layout-box2">
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>


                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>


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


                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>
                            <div class="sleeper_prv" data-type="sleeper_prv"></div>

                        </div>
                    </div>

                    <!-- LOWER BERTH -->
                    <div class="berth-row">
                        <div class="berth-label">Lower Berth</div>

                        <div class="layout-box2">
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>


                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>


                            <div class="empty"></div>
                            <div class="empty"></div>
                            <div class="empty"></div>
                            <div class="empty"></div>
                            <div class="empty"></div>
                            <div class="empty"></div>
                            <div class="empty"></div>
                            <div class="empty"></div>
                            <div class="empty"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>

                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>
                            <div class="seat_prv" data-type="seat_prv"></div>


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