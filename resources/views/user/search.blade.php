@extends('layouts.public')

@section('title', 'Trip Search Results - BusGo')

@section('content')
<section class="busgo-section">
    <!-- Search Summary Banner -->
    <div class="busgo-search-summary-banner">
        <div>
            <h2 class="busgo-search-summary-title">
                <span>{{ request('from') }}</span>
                <i class="ri-arrow-right-line" style="font-size:1.3rem;"></i>
                <span>{{ request('to') }}</span>
            </h2>
            <div class="busgo-search-summary-meta">
                <span>
                    <i class="ri-calendar-line"></i> {{ \Carbon\Carbon::parse(request('date'))->format('l, F j, Y') }}
                </span>
                <span>
                    <i class="ri-bus-line"></i> {{ $trips->count() }} {{ \Illuminate\Support\Str::plural('trip', $trips->count()) }} available
                </span>
            </div>
        </div>
        <div>
            <a href="{{ route('home') }}#book" class="busgo-nav-link" style="color:#ffffff; background:rgba(255,255,255,0.2); padding:0.5rem 1rem; border-radius:9999px;">
                <i class="ri-edit-line"></i> Modify Search
            </a>
        </div>
    </div>

    <!-- Trips List -->
    <div class="busgo-trips-list">
        @forelse($trips as $trip)
            @php
                $seatsLeft = max(0, $trip->bus->seat_capacity - ($trip->bookings_count ?? 0));
            @endphp
            <div class="busgo-trip-card">
                <div class="busgo-trip-timing">
                    <div class="busgo-time-badge">
                        {{ $trip->departure_at->format('g:i A') }}
                    </div>
                    <div class="busgo-trip-info">
                        <h4>
                            {{ $trip->busRoute->origin }} to {{ $trip->busRoute->destination }}
                        </h4>
                        <div class="busgo-trip-meta">
                            <span class="busgo-badge busgo-badge-type">
                                <i class="ri-bus-fill"></i> {{ $trip->bus->bus_type }}
                            </span>
                            <span>
                                <i class="ri-car-line"></i> {{ $trip->bus_name ?? $trip->bus->plate_number }}
                            </span>
                            <span class="busgo-badge busgo-badge-seats">
                                <i class="ri-user-star-line"></i> {{ $seatsLeft }} {{ \Illuminate\Support\Str::plural('seat', $seatsLeft) }} left
                            </span>
                        </div>
                    </div>
                </div>

                <div class="busgo-trip-pricing">
                    <div class="busgo-trip-fare">
                        ₦{{ number_format($trip->fare) }} <span>/ person</span>
                    </div>
                    @if($seatsLeft > 0)
                        <a href="{{ route('trips.seats', $trip) }}" class="busgo-btn-select-seats">
                            Select seats <i class="ri-arrow-right-line"></i>
                        </a>
                    @else
                        <button class="busgo-btn-select-seats" disabled style="background:#cbd5e1; cursor:not-allowed;">
                            Fully Booked
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="busgo-empty-state">
                <div class="busgo-empty-icon">
                    <i class="ri-bus-2-line"></i>
                </div>
                <h3>No Scheduled Trips Found</h3>
                <p>
                    There are no trips available from <strong>{{ request('from') }}</strong> to <strong>{{ request('to') }}</strong> on <strong>{{ \Carbon\Carbon::parse(request('date'))->format('M j, Y') }}</strong>.
                </p>
                <div style="display:flex; justify-content:center; gap:1rem; flex-wrap:wrap;">
                    <a href="{{ route('home') }}#book" class="busgo-btn-search" style="text-decoration:none;">
                        <i class="ri-calendar-line"></i> Try Another Date or Route
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</section>
@endsection
