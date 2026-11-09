@extends('layouts.public')

@section('title', 'Booking Confirmation - BusGo')

@section('content')
<section class="busgo-section">
    <div class="busgo-confirmation-container">
        @php
            $allPaid = $bookings->every(fn ($b) => $b->status === 'paid');
            $firstBooking = $bookings->first();
        @endphp

        @if(session('success'))
            <div class="busgo-alert busgo-alert-success busgo-no-print">
                <i class="ri-checkbox-circle-fill" style="font-size:1.3rem;"></i>
                <div>
                    <strong>Payment Confirmed!</strong> {{ session('success') }}
                </div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="busgo-alert busgo-alert-danger busgo-no-print">
                <i class="ri-error-warning-fill" style="font-size:1.3rem;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        @if(!$allPaid)
            <!-- PENDING PAYMENT STATE -->
            <div class="busgo-card">
                <div style="text-align:center; margin-bottom:1.75rem;">
                    <div style="width:60px; height:60px; background:var(--busgo-primary-light); color:var(--busgo-primary); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:1.75rem; margin-bottom:0.75rem;">
                        <i class="ri-time-line"></i>
                    </div>
                    <h2 style="font-size:1.75rem; margin-bottom:0.35rem;">Seats Reserved</h2>
                    <p style="color:var(--busgo-text-muted); font-size:0.95rem;">
                        Complete your payment via Paystack to secure your seats and receive your boarding passes.
                    </p>
                </div>

                <!-- Trip Details -->
                <div style="background:var(--busgo-border-light); border-radius:var(--busgo-radius-md); padding:1.25rem 1.5rem; margin-bottom:1.5rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem; margin-bottom:0.75rem;">
                        <span style="font-size:1.15rem; font-weight:700;">
                            {{ $firstBooking->trip->busRoute->origin }} &rarr; {{ $firstBooking->trip->busRoute->destination }}
                        </span>
                        <span class="busgo-badge busgo-badge-type">
                            <i class="ri-bus-line"></i> {{ $firstBooking->trip->bus->plate_number }}
                        </span>
                    </div>
                    <p style="color:var(--busgo-text-muted); font-size:0.9rem; margin:0;">
                        <i class="ri-calendar-line"></i> {{ $firstBooking->trip->departure_at->format('l, F j, Y · g:i A') }}
                    </p>
                </div>

                <!-- Booked Seats Breakdown -->
                <h4 style="font-size:1.05rem; margin-bottom:1rem;">Reserved Seats ({{ $bookings->count() }})</h4>
                <div style="margin-bottom:1.5rem;">
                    @foreach($bookings as $booking)
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:0.85rem 0; border-bottom:1px solid var(--busgo-border);">
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <span class="busgo-seat-pill" style="font-size:0.85rem;">
                                    Seat {{ $booking->seat_number }}
                                </span>
                                <div>
                                    <div style="font-weight:600; font-size:0.95rem;">{{ $booking->passenger_name }}</div>
                                    <div style="font-size:0.8rem; color:var(--busgo-text-muted);">Ref: {{ $booking->booking_code }}</div>
                                </div>
                            </div>
                            <div style="font-weight:700; color:var(--busgo-text-main);">
                                ₦{{ number_format($booking->amount) }}
                            </div>
                        </div>
                    @endforeach

                    <div style="display:flex; justify-content:space-between; align-items:center; padding:1.25rem 0 0 0; font-size:1.25rem; font-weight:800;">
                        <span>Total Due</span>
                        <span style="color:var(--busgo-primary);">₦{{ number_format($bookings->sum('amount')) }}</span>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="busgo-payment-box">
                    <form action="{{ route('bookings.pay') }}" method="POST">
                        @csrf
                        <div class="busgo-input-group" style="margin-bottom:1.25rem;">
                            <label class="busgo-label">
                                <i class="ri-mail-line"></i> Email Address for Payment Receipt
                            </label>
                            <input type="email" name="email" class="busgo-input" placeholder="e.g. user@example.com" value="{{ old('email') }}" required />
                        </div>

                        <button type="submit" class="busgo-pay-btn">
                            <i class="ri-secure-payment-line"></i> Pay ₦{{ number_format($bookings->sum('amount')) }} with Paystack
                        </button>
                    </form>
                    <p style="text-align:center; font-size:0.8rem; color:var(--busgo-text-muted); margin-top:0.85rem; margin-bottom:0;">
                        <i class="ri-shield-check-line" style="color:#059669;"></i> 256-bit encrypted checkout via Paystack. Supports Cards, Bank Transfer &amp; USSD.
                    </p>
                </div>
            </div>
        @else
            <!-- PAID SUCCESS STATE: YOUR TICKETS -->
            <div class="busgo-card busgo-no-print" style="text-align:center; padding:2rem 1.5rem; margin-bottom:2rem; background:linear-gradient(135deg, #f0fdf4, #ffffff); border-color:#bbf7d0;">
                <div style="width:68px; height:68px; background:#dcfce7; color:#15803d; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:2.2rem; margin-bottom:1rem;">
                    <i class="ri-checkbox-circle-fill"></i>
                </div>
                <h2 style="font-size:1.85rem; margin-bottom:0.4rem; color:#166534;">Booking Confirmed!</h2>
                <p style="color:#15803d; max-width:560px; margin:0 auto 1.25rem auto; font-size:1rem;">
                    Your payment was successful and your {{ $bookings->count() }} {{ \Illuminate\Support\Str::plural('seat', $bookings->count()) }} are confirmed. You can view each ticket individually or print all tickets at once below.
                </p>
            </div>

            <!-- "Your tickets" Section -->
            <div class="busgo-tickets-section">
                <div class="busgo-ticket-action-bar busgo-no-print">
                    <div>
                        <h3 style="font-size:1.4rem; margin:0; display:flex; align-items:center; gap:0.5rem;">
                            <i class="ri-ticket-2-fill" style="color:var(--busgo-primary);"></i>
                            Your Tickets ({{ $bookings->count() }})
                        </h3>
                        <p style="color:var(--busgo-text-muted); font-size:0.875rem; margin-top:0.25rem;">
                            Tap "View ticket" to open and save an individual boarding pass with its QR code.
                        </p>
                    </div>

                    @if($bookings->count() > 1)
                        <button type="button" onclick="window.print()" class="busgo-btn-select-seats" style="background:#0f172a; cursor:pointer; border:none;">
                            <i class="ri-printer-line"></i> Print All Tickets
                        </button>
                    @endif
                </div>

                <!-- Seat cards list (Interactive view on screen) -->
                <div class="busgo-no-print">
                    @foreach($bookings as $booking)
                        <div class="busgo-seat-ticket-card">
                            <div style="display:flex; align-items:center; gap:1.25rem;">
                                <div class="busgo-seat-badge-large" style="text-align:center; min-width:70px;">
                                    <div style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em; opacity:0.85;">SEAT</div>
                                    <div style="font-size:1.4rem; font-weight:800;">{{ $booking->seat_number }}</div>
                                </div>
                                <div>
                                    <h4 style="margin-bottom:0.25rem; font-size:1.1rem;">
                                        {{ $booking->trip->busRoute->origin }} &rarr; {{ $booking->trip->busRoute->destination }}
                                    </h4>
                                    <p style="color:var(--busgo-text-muted); font-size:0.85rem; margin-bottom:0.2rem;">
                                        <strong>Passenger:</strong> {{ $booking->passenger_name }} &bull;
                                        <strong>Ref:</strong> <span style="font-family:monospace; color:var(--busgo-primary); font-weight:700;">{{ $booking->booking_code }}</span>
                                    </p>
                                    <p style="color:var(--busgo-text-muted); font-size:0.85rem; margin:0;">
                                        <i class="ri-time-line"></i> {{ $booking->trip->departure_at->format('D, M j, Y · g:i A') }} &bull;
                                        <i class="ri-bus-line"></i> {{ $booking->trip->bus->plate_number }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <!-- Protected Ticket View: sends booking_code and passenger_phone via POST to tickets.show -->
                                <form action="{{ route('tickets.show') }}" method="POST" target="_blank" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="booking_code" value="{{ $booking->booking_code }}">
                                    <input type="hidden" name="passenger_phone" value="{{ $booking->passenger_phone }}">
                                    <button type="submit" class="busgo-btn-select-seats" style="cursor:pointer; border:none;">
                                        <i class="ri-qr-code-line"></i> View Ticket
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Printable Boarding Passes (Visible on Print or when printing all) -->
                <div class="busgo-printable-passes">
                    @foreach($bookings as $booking)
                        <div class="busgo-boarding-pass" style="margin-bottom: 2rem;">
                            <!-- Main Pass Section -->
                            <div class="busgo-pass-main">
                                <div class="busgo-pass-header">
                                    <div class="busgo-brand" style="font-size:1.3rem;">
                                        <span class="busgo-brand-icon"><i class="ri-bus-fill"></i></span>
                                        BusGo
                                    </div>
                                    <div class="busgo-badge" style="background:#dcfce7; color:#15803d; font-size:0.8rem; padding:0.35rem 0.85rem;">
                                        <i class="ri-check-line"></i> CONFIRMED BOARDING PASS
                                    </div>
                                </div>

                                <div class="busgo-pass-route">
                                    <div class="busgo-route-point">
                                        <h3>{{ $booking->trip->busRoute->origin }}</h3>
                                        <p>Departure Terminal</p>
                                    </div>
                                    <div class="busgo-route-arrow">
                                        &rarr;
                                    </div>
                                    <div class="busgo-route-point">
                                        <h3>{{ $booking->trip->busRoute->destination }}</h3>
                                        <p>Arrival Destination</p>
                                    </div>
                                </div>

                                <div class="busgo-pass-grid">
                                    <div class="busgo-pass-field">
                                        <p class="field-label">Passenger Name</p>
                                        <h5>{{ $booking->passenger_name }}</h5>
                                    </div>
                                    <div class="busgo-pass-field">
                                        <p class="field-label">Phone Number</p>
                                        <h5>{{ $booking->passenger_phone }}</h5>
                                    </div>
                                    <div class="busgo-pass-field">
                                        <p class="field-label">Departure Date &amp; Time</p>
                                        <h5>{{ $booking->trip->departure_at->format('M j, Y · g:i A') }}</h5>
                                    </div>
                                    <div class="busgo-pass-field">
                                        <p class="field-label">Bus Registration</p>
                                        <h5>{{ $booking->trip->bus->plate_number }} ({{ $booking->trip->bus->bus_type }})</h5>
                                    </div>
                                    <div class="busgo-pass-field">
                                        <p class="field-label">Assigned Seat</p>
                                        <div class="busgo-seat-badge-large">Seat {{ $booking->seat_number }}</div>
                                    </div>
                                    <div class="busgo-pass-field">
                                        <p class="field-label">Fare Paid</p>
                                        <h5 style="color:var(--busgo-primary);">₦{{ number_format($booking->amount) }}</h5>
                                    </div>
                                </div>
                            </div>

                            <!-- Tear-Off Stub -->
                            <div class="busgo-pass-stub">
                                <div style="font-size:0.75rem; font-weight:700; color:var(--busgo-text-muted); text-transform:uppercase; margin-bottom:0.5rem;">
                                    BOARDING PASS STUB
                                </div>
                                <div style="margin: 0.5rem 0;">
                                    {!! QrCode::size(130)->generate($booking->booking_code) !!}
                                </div>
                                <div style="font-family:monospace; font-weight:800; font-size:1.05rem; letter-spacing:0.05em; color:var(--busgo-primary); margin-top:0.35rem;">
                                    {{ $booking->booking_code }}
                                </div>
                                <div style="font-size:0.8rem; font-weight:700; margin-top:0.25rem;">
                                    Seat {{ $booking->seat_number }} &bull; {{ $booking->passenger_name }}
                                </div>
                                <div style="font-size:0.7rem; color:var(--busgo-text-muted); margin-top:0.35rem;">
                                    Show QR to operator at boarding
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
