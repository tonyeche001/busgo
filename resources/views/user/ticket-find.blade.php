@extends('layouts.public')

@section('title', 'Find My Ticket - BusGo')

@section('content')
<section class="busgo-section">
    <div class="busgo-find-ticket-wrapper">
        <div class="busgo-card" style="box-shadow:var(--busgo-shadow-lg);">
            <div style="text-align:center; margin-bottom:2rem;">
                <div style="width:64px; height:64px; background:var(--busgo-primary-light); color:var(--busgo-primary); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:1.85rem; margin-bottom:1rem;">
                    <i class="ri-ticket-2-line"></i>
                </div>
                <h2 style="font-size:1.75rem; margin-bottom:0.4rem;">Find Your Ticket</h2>
                <p style="color:var(--busgo-text-muted); font-size:0.925rem;">
                    Enter your booking reference code and the phone number used during reservation to view or reprint your boarding pass.
                </p>
            </div>

            @if(isset($errors) && $errors->any())
                <div class="busgo-alert busgo-alert-danger">
                    <i class="ri-error-warning-fill"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form action="{{ route('tickets.show') }}" method="POST">
                @csrf
                <div class="busgo-input-group" style="margin-bottom:1.25rem;">
                    <label class="busgo-label">
                        <i class="ri-barcode-box-line"></i> Booking Code
                    </label>
                    <input type="text" name="booking_code" class="busgo-input" placeholder="e.g. BK789XYZ12" value="{{ old('booking_code') }}" required autofocus style="text-transform:uppercase;" />
                </div>

                <div class="busgo-input-group" style="margin-bottom:1.75rem;">
                    <label class="busgo-label">
                        <i class="ri-phone-line"></i> Passenger Phone Number
                    </label>
                    <input type="tel" name="passenger_phone" class="busgo-input" placeholder="e.g. 08012345678" value="{{ old('passenger_phone') }}" required />
                </div>

                <button type="submit" class="busgo-btn-block">
                    <i class="ri-search-line"></i> Retrieve Boarding Pass
                </button>
            </form>

            <div style="margin-top:2rem; padding-top:1.25rem; border-top:1px solid var(--busgo-border); text-align:center; font-size:0.85rem; color:var(--busgo-text-muted);">
                <i class="ri-question-line" style="color:var(--busgo-primary);"></i> Can't find your booking code? Check the confirmation SMS or Paystack email receipt sent to you after payment.
            </div>
        </div>
    </div>
</section>
@endsection
