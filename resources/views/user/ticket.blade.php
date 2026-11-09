@extends('layouts.public')

@section('title', 'Boarding Pass - ' . $booking->booking_code . ' - BusGo')

@section('content')
<section class="busgo-section">
    <!-- Top Action Toolbar (Hidden during print) -->
    <div class="busgo-no-print" style="max-width:780px; margin:0 auto 1.5rem auto; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <a href="{{ route('tickets.find') }}" class="busgo-nav-link" style="padding-left:0; color:var(--busgo-primary);">
            <i class="ri-arrow-left-line"></i> Find Another Ticket
        </a>
        <div style="display:flex; gap:0.75rem;">
            <button onclick="window.print()" class="busgo-btn-select-seats" style="cursor:pointer; border:none; background:#0f172a;">
                <i class="ri-printer-line"></i> Print Ticket / Save PDF
            </button>
        </div>
    </div>

    <!-- Official Real Boarding Pass -->
    <div class="busgo-boarding-pass">
        <!-- Main Pass Body -->
        <div class="busgo-pass-main">
            <div class="busgo-pass-header">
                <div class="busgo-brand" style="font-size:1.4rem;">
                    <span class="busgo-brand-icon"><i class="ri-bus-fill"></i></span>
                    BusGo<span class="brand-dot">.</span>
                </div>
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <span class="busgo-badge" style="background:#dcfce7; color:#15803d; font-size:0.8rem; padding:0.35rem 0.85rem;">
                        <i class="ri-checkbox-circle-fill"></i> CONFIRMED &amp; PAID
                    </span>
                </div>
            </div>

            <!-- Route Header -->
            <div class="busgo-pass-route">
                <div class="busgo-route-point">
                    <p>DEPARTURE</p>
                    <h3>{{ $booking->trip->busRoute->origin }}</h3>
                </div>
                <div class="busgo-route-arrow">
                    <i class="ri-arrow-right-line"></i>
                </div>
                <div class="busgo-route-point">
                    <p>DESTINATION</p>
                    <h3>{{ $booking->trip->busRoute->destination }}</h3>
                </div>
            </div>

            <!-- Ticket Details Grid -->
            <div class="busgo-pass-grid">
                <div class="busgo-pass-field">
                    <p class="field-label">Passenger Name</p>
                    <h5>{{ $booking->passenger_name }}</h5>
                </div>

                <div class="busgo-pass-field">
                    <p class="field-label">Contact Phone</p>
                    <h5>{{ $booking->passenger_phone }}</h5>
                </div>

                <div class="busgo-pass-field">
                    <p class="field-label">Assigned Seat</p>
                    <div class="busgo-seat-badge-large">
                        Seat {{ $booking->seat_number }}
                    </div>
                </div>

                <div class="busgo-pass-field">
                    <p class="field-label">Departure Date</p>
                    <h5>{{ $booking->trip->departure_at->format('l, M j, Y') }}</h5>
                </div>

                <div class="busgo-pass-field">
                    <p class="field-label">Departure Time</p>
                    <h5 style="color:var(--busgo-primary);">{{ $booking->trip->departure_at->format('g:i A') }}</h5>
                </div>

                <div class="busgo-pass-field">
                    <p class="field-label">Bus Vehicle Plate</p>
                    <h5>{{ $booking->trip->bus->plate_number }} ({{ $booking->trip->bus->bus_type }})</h5>
                </div>

                <div class="busgo-pass-field">
                    <p class="field-label">Booking Reference</p>
                    <h5 style="font-family:monospace; font-weight:800; color:var(--busgo-primary);">{{ $booking->booking_code }}</h5>
                </div>

                <div class="busgo-pass-field">
                    <p class="field-label">Fare Paid</p>
                    <h5 style="color:#059669;">₦{{ number_format($booking->amount) }}</h5>
                </div>

                <div class="busgo-pass-field">
                    <p class="field-label">Terminal Gate / Bay</p>
                    <h5>Bay 02 - Main Depo</h5>
                </div>
            </div>

            <div style="margin-top:1.75rem; padding-top:1rem; border-top:1px dashed var(--busgo-border); font-size:0.78rem; color:var(--busgo-text-muted);">
                <i class="ri-information-line"></i> Please arrive at least 30 minutes before departure time. Baggage allowance: 1 main luggage + 1 handbag.
            </div>
        </div>

        <!-- Tear-Off Boarding Stub -->
        <div class="busgo-pass-stub">
            <div style="font-size:0.7rem; font-weight:700; color:var(--busgo-text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.75rem;">
                <i class="ri-scissors-cut-line"></i> TEAR-OFF STUB
            </div>

            <!-- QR Code -->
            <div style="background:#ffffff; padding:0.65rem; border-radius:12px; border:1px solid #e2e8f0; margin-bottom:0.75rem; display:inline-block;">
                {!! QrCode::size(150)->generate($booking->booking_code) !!}
            </div>

            <div style="font-family:monospace; font-weight:800; font-size:1.15rem; letter-spacing:0.05em; color:var(--busgo-primary);">
                {{ $booking->booking_code }}
            </div>

            <div style="font-size:0.95rem; font-weight:800; color:var(--busgo-text-main); margin-top:0.35rem;">
                Seat {{ $booking->seat_number }}
            </div>

            <div style="font-size:0.8rem; color:var(--busgo-text-muted); margin-top:0.15rem;">
                {{ $booking->passenger_name }}
            </div>

            <div style="font-size:0.72rem; color:var(--busgo-text-muted); margin-top:0.75rem; line-height:1.4;">
                Present this digital QR code or printout to the terminal marshal upon boarding.
            </div>
        </div>
    </div>
</section>
@endsection
