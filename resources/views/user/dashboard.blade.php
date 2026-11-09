@extends('layouts.public')

@section('title', 'BusGo - Inter-State Bus Booking Across Nigeria')

@section('content')
<!-- Hero Section -->
<section class="busgo-hero" id="home">
    <div class="busgo-hero-inner">
        <div class="busgo-hero-badge">
            <i class="ri-compass-3-line"></i> Nigeria's Leading Inter-State Bus Network
        </div>
        <h1 class="busgo-hero-title">
            Travel Across Nigeria in Comfort &amp; Safety
        </h1>
        <p class="busgo-hero-subtitle">
            Book guaranteed seats on premium air-conditioned buses across Lagos, Abuja, Port Harcourt, Ibadan, Benin City, Enugu, and all Nigerian states.
        </p>
    </div>
</section>

<!-- Overlapping Search Card -->
<div class="busgo-search-wrapper" id="book">
    <div class="busgo-search-card">
        <form action="{{ route('trips.search') }}" method="GET" class="busgo-search-form">
            <div class="busgo-input-group">
                <label class="busgo-label">
                    <i class="ri-map-pin-user-line" style="color:var(--busgo-primary);"></i> From (Departure)
                </label>
                <select name="from" class="busgo-select" required>
                    <option value="" disabled selected>Select departure city</option>
                    @foreach ($routes->pluck('origin')->unique() as $origin)
                        <option value="{{ $origin }}" {{ old('from') == $origin ? 'selected' : '' }}>{{ $origin }}</option>
                    @endforeach
                </select>
            </div>

            <div class="busgo-input-group">
                <label class="busgo-label">
                    <i class="ri-map-pin-range-line" style="color:#0ea5e9;"></i> To (Destination)
                </label>
                <select name="to" class="busgo-select" required>
                    <option value="" disabled selected>Select destination city</option>
                    @foreach ($routes->pluck('destination')->unique() as $destination)
                        <option value="{{ $destination }}" {{ old('to') == $destination ? 'selected' : '' }}>{{ $destination }}</option>
                    @endforeach
                </select>
            </div>

            <div class="busgo-input-group">
                <label class="busgo-label">
                    <i class="ri-calendar-event-line" style="color:#f97316;"></i> Travel Date
                </label>
                <input type="date" name="date" class="busgo-input" min="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" required />
            </div>

            <button type="submit" class="busgo-btn-search">
                <i class="ri-search-line"></i> Search Trips
            </button>
        </form>
    </div>
</div>

<!-- Popular Nigerian Routes Section -->
<section class="busgo-section" id="popular">
    <div class="busgo-section-header">
        <h2 class="busgo-section-title">Popular Routes Across Nigeria</h2>
        <p class="busgo-section-subtitle">
            Explore daily scheduled departures between Nigeria's major commercial hubs and regional capitals at competitive fares.
        </p>
    </div>

    <div class="busgo-routes-grid">
        @forelse($routes as $route)
            <div class="busgo-route-card">
                <div>
                    <div class="busgo-route-header">
                        <span class="busgo-badge busgo-badge-type">
                            <i class="ri-bus-line"></i> Daily Service
                        </span>
                        @if($route->distance_km)
                            <span class="busgo-route-distance">
                                <i class="ri-route-line"></i> {{ $route->distance_km }} km
                            </span>
                        @endif
                    </div>
                    <div class="busgo-route-cities">
                        <span>{{ $route->origin }}</span>
                        <i class="ri-arrow-right-line"></i>
                        <span>{{ $route->destination }}</span>
                    </div>
                    <p style="font-size:0.875rem; color:var(--busgo-text-muted); margin-top:0.4rem;">
                        Air-conditioned express coach with reclining seats &amp; charging ports.
                    </p>
                </div>
                <div class="busgo-route-footer">
                    <div class="busgo-route-fare">
                        ₦{{ number_format($route->base_fare) }} <span>/ seat</span>
                    </div>
                    <a href="{{ route('trips.search', ['from' => $route->origin, 'to' => $route->destination, 'date' => now()->toDateString()]) }}" class="busgo-nav-link" style="color:var(--busgo-primary); font-weight:700; padding:0;">
                        View Trips <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="busgo-empty-state" style="grid-column: 1 / -1;">
                <p>No active bus routes available right now. Please check back shortly.</p>
            </div>
        @endforelse
    </div>
</section>

<!-- Why Travel with BusGo Section -->
<section class="busgo-section" id="features" style="background:#ffffff; border-radius:32px; margin-top:2rem; margin-bottom:3rem; box-shadow:var(--busgo-shadow-sm); border:1px solid var(--busgo-border);">
    <div class="busgo-section-header">
        <h2 class="busgo-section-title">Why Commuters Choose BusGo</h2>
        <p class="busgo-section-subtitle">
            Reliable, secure, and stress-free road travel engineered for modern Nigerian commuters.
        </p>
    </div>

    <div class="busgo-features-grid">
        <div class="busgo-feature-card">
            <div class="busgo-feature-icon">
                <i class="ri-smartphone-line"></i>
            </div>
            <h4 class="busgo-feature-title">Instant Seat Booking</h4>
            <p class="busgo-feature-desc">
                Choose your exact window or aisle seat in real time. Avoid chaotic terminal queues with 100% digital reservations.
            </p>
        </div>

        <div class="busgo-feature-card">
            <div class="busgo-feature-icon">
                <i class="ri-qr-code-line"></i>
            </div>
            <h4 class="busgo-feature-title">Digital QR Boarding Pass</h4>
            <p class="busgo-feature-desc">
                Receive an encrypted QR ticket directly to your phone. Show it at the terminal gate for immediate contactless boarding.
            </p>
        </div>

        <div class="busgo-feature-card">
            <div class="busgo-feature-icon">
                <i class="ri-shield-check-line"></i>
            </div>
            <h4 class="busgo-feature-title">Safety &amp; Verified Fleets</h4>
            <p class="busgo-feature-desc">
                GPS-tracked buses, speed-monitored drivers, and regular maintenance ensure your journey is safe and on-schedule.
            </p>
        </div>

        <div class="busgo-feature-card">
            <div class="busgo-feature-icon">
                <i class="ri-customer-service-2-line"></i>
            </div>
            <h4 class="busgo-feature-title">24/7 Roadside Assistance</h4>
            <p class="busgo-feature-desc">
                Our operations team supports every route with round-the-clock monitoring and responsive passenger assistance.
            </p>
        </div>
    </div>
</section>
@endsection
