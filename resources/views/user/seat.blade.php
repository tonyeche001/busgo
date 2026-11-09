@extends('layouts.public')

@section('title', 'Select Your Seat - BusGo')

@section('content')
<section class="busgo-section">
    <!-- Trip Header Info -->
    <div style="margin-bottom: 2rem;">
        <a href="{{ url()->previous() }}" class="busgo-nav-link" style="padding-left:0; color:var(--busgo-primary); margin-bottom:0.75rem; display:inline-flex;">
            <i class="ri-arrow-left-line"></i> Back to search results
        </a>
        <h2 style="font-size: clamp(1.5rem, 3vw, 2rem); margin-bottom: 0.25rem;">
            {{ $trip->busRoute->origin }} to {{ $trip->busRoute->destination }}
        </h2>
        <p style="color: var(--busgo-text-muted); font-size: 0.95rem;">
            <i class="ri-calendar-line"></i> {{ $trip->departure_at->format('l, F j, Y · g:i A') }} &nbsp;•&nbsp;
            <i class="ri-bus-line"></i> {{ $trip->bus->plate_number }} ({{ $trip->bus->bus_type }}) &nbsp;•&nbsp;
            <strong>₦{{ number_format($trip->fare) }}</strong> per seat
        </p>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="busgo-alert busgo-alert-danger">
            <i class="ri-error-warning-line"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <form action="{{ route('trips.book', $trip) }}" method="POST" id="bookingForm">
        @csrf

        <div class="busgo-seat-picker-container">
            <!-- Left: Bus-Shaped Cabin Representation -->
            <div>
                <div class="busgo-cabin-card">
                    <!-- Bus Front Area -->
                    <div class="busgo-cabin-front">
                        <div class="busgo-driver-area">
                            <i class="ri-steering-2-fill" style="font-size:1.2rem; color:var(--busgo-primary);"></i>
                            <span>Driver Cabin</span>
                        </div>
                        <div class="busgo-cabin-door">
                            <i class="ri-logout-box-r-line"></i> Entry Door
                        </div>
                    </div>

                    <!-- Bus Cabin Seat Grid with Aisle -->
                    <div class="busgo-seat-grid">
                        @for ($i = 1; $i <= $trip->bus->seat_capacity; $i++)
                            @php($taken = in_array($i, $takenSeats))
                            <label class="busgo-seat-label {{ $taken ? 'taken' : '' }}">
                                <input type="checkbox" name="seats[]" value="{{ $i }}" class="seat-checkbox" data-seat="{{ $i }}" {{ $taken ? 'disabled' : '' }}>
                                <div class="busgo-seat-box">
                                    <i class="ri-armchair-fill"></i>
                                    <span>{{ $i }}</span>
                                </div>
                            </label>
                            @if ($i % 2 == 0 && $i % 4 != 0 && $i < $trip->bus->seat_capacity)
                                <div class="busgo-aisle">AISLE</div>
                            @endif
                        @endfor
                    </div>

                    <!-- Legend -->
                    <div class="busgo-seat-legend">
                        <div class="busgo-legend-item">
                            <div class="busgo-legend-sample available"></div>
                            <span>Available</span>
                        </div>
                        <div class="busgo-legend-item">
                            <div class="busgo-legend-sample selected"></div>
                            <span>Selected</span>
                        </div>
                        <div class="busgo-legend-item">
                            <div class="busgo-legend-sample booked"></div>
                            <span>Booked</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Sticky Passenger Details & Fare Summary -->
            <div>
                <div class="busgo-booking-summary-panel">
                    <h3 class="busgo-summary-heading">
                        <i class="ri-shopping-cart-line" style="color:var(--busgo-primary);"></i> Booking Summary
                    </h3>

                    <div class="busgo-summary-row">
                        <span>Fare per seat</span>
                        <strong id="farePerSeat">₦{{ number_format($trip->fare) }}</strong>
                    </div>

                    <div class="busgo-summary-row">
                        <span>Selected Seats</span>
                        <span id="selectedCountText" style="font-weight:600; color:var(--busgo-primary);">0 seats</span>
                    </div>

                    <div class="busgo-selected-pills" id="selectedPillsContainer">
                        <span style="font-size:0.85rem; color:var(--busgo-text-muted); font-style:italic;">No seats selected yet</span>
                    </div>

                    <div class="busgo-summary-row total">
                        <span>Total Amount</span>
                        <span id="totalFareDisplay" style="color:var(--busgo-primary);">₦0</span>
                    </div>

                    <!-- Styled Passenger Details Form -->
                    <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--busgo-border);">
                        <h4 style="font-size: 0.95rem; margin-bottom: 0.85rem; color: var(--busgo-text-main);">
                            <i class="ri-user-line" style="color:var(--busgo-primary);"></i> Passenger Contact Details
                        </h4>

                        <div class="busgo-input-group" style="margin-bottom: 0.85rem;">
                            <label class="busgo-label">Full Name</label>
                            <input type="text" name="passenger_name" class="busgo-input" placeholder="e.g. Babatunde Adeleke" value="{{ old('passenger_name') }}" required />
                        </div>

                        <div class="busgo-input-group" style="margin-bottom: 1.25rem;">
                            <label class="busgo-label">Phone Number</label>
                            <input type="tel" name="passenger_phone" class="busgo-input" placeholder="e.g. 08012345678" value="{{ old('passenger_phone') }}" required />
                        </div>

                        <button type="submit" class="busgo-btn-block" id="submitBtn">
                            Proceed to Confirmation <i class="ri-arrow-right-line"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fare = {{ (float) $trip->fare }};
    const checkboxes = document.querySelectorAll('.seat-checkbox');
    const pillsContainer = document.getElementById('selectedPillsContainer');
    const selectedCountText = document.getElementById('selectedCountText');
    const totalFareDisplay = document.getElementById('totalFareDisplay');
    const submitBtn = document.getElementById('submitBtn');

    function updateSummary() {
        const selected = [];
        checkboxes.forEach(cb => {
            if (cb.checked) {
                selected.push(cb.value);
            }
        });

        const count = selected.length;
        selectedCountText.textContent = count + (count === 1 ? ' seat' : ' seats');

        const total = count * fare;
        totalFareDisplay.textContent = '₦' + total.toLocaleString();

        pillsContainer.innerHTML = '';
        if (count === 0) {
            pillsContainer.innerHTML = '<span style="font-size:0.85rem; color:var(--busgo-text-muted); font-style:italic;">No seats selected yet</span>';
        } else {
            selected.forEach(seat => {
                const pill = document.createElement('span');
                pill.className = 'busgo-seat-pill';
                pill.innerHTML = '<i class="ri-armchair-line"></i> Seat ' + seat;
                pillsContainer.appendChild(pill);
            });
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSummary);
    });

    // Run once on load (in case of browser restore)
    updateSummary();

    document.getElementById('bookingForm').addEventListener('submit', function (e) {
        let anyChecked = false;
        checkboxes.forEach(cb => {
            if (cb.checked) anyChecked = true;
        });
        if (!anyChecked) {
            e.preventDefault();
            alert('Please select at least one seat to proceed.');
        }
    });
});
</script>
@endpush
