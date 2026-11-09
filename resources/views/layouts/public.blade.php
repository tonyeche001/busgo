<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BusGo - Inter-State Bus Booking Across Nigeria')</title>

    <!-- Google Web Fonts with Naira (₦) symbol support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Remixicon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet" />

    <!-- Core Styles -->
    <link rel="stylesheet" href="{{ asset('style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/busgo.css') }}" />

    @stack('styles')
</head>
<body>
    <!-- Public Navbar -->
    <nav class="busgo-navbar">
        <div class="busgo-nav-container">
            <a href="{{ route('home') }}" class="busgo-brand">
                <span class="busgo-brand-icon"><i class="ri-bus-fill"></i></span>
                BusGo<span class="brand-dot">.</span>
            </a>

            <button class="busgo-mobile-toggle" id="busgo-mobile-btn" aria-label="Toggle navigation">
                <i class="ri-menu-line"></i>
            </button>

            <ul class="busgo-nav-menu" id="busgo-nav-menu">
                <li>
                    <a href="{{ route('home') }}" class="busgo-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="ri-home-4-line"></i> HOME
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}#popular" class="busgo-nav-link">
                        <i class="ri-map-pin-line"></i> POPULAR ROUTES
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}#features" class="busgo-nav-link">
                        <i class="ri-shield-check-line"></i> WHY BUSGO
                    </a>
                </li>
                <li>
                    <!-- MY TICKET link linking to route('tickets.find') -->
                    <a href="{{ route('tickets.find') }}" class="busgo-nav-link my-ticket-link {{ request()->routeIs('tickets.find') || request()->routeIs('tickets.show') ? 'active' : '' }}">
                        <i class="ri-ticket-2-line"></i> MY TICKET
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}#book" class="busgo-nav-cta">
                        <i class="ri-calendar-check-line"></i> Book Trip
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="busgo-page-wrapper">
        @yield('content')
    </div>

    <!-- Nigerian Public Footer -->
    <footer class="busgo-footer" id="contact">
        <div class="busgo-footer-container">
            <div class="busgo-footer-col">
                <a href="{{ route('home') }}" class="busgo-brand" style="color:#ffffff; margin-bottom:1rem; display:inline-flex;">
                    <span class="busgo-brand-icon"><i class="ri-bus-fill"></i></span>
                    BusGo<span class="brand-dot" style="color:#f97316;">.</span>
                </a>
                <p>
                    Nigeria's trusted online inter-state bus booking platform. Travel comfortably and safely between Lagos, Abuja, Port Harcourt, Ibadan, Benin City, Enugu, Kano, and across all 36 states.
                </p>
                <div style="display:flex; gap:0.75rem; margin-top:1.25rem;">
                    <span style="font-size:0.8rem; background:#1e293b; color:#38bdf8; padding:0.35rem 0.75rem; border-radius:9999px;">
                        <i class="ri-lock-line"></i> Verified Paystack
                    </span>
                    <span style="font-size:0.8rem; background:#1e293b; color:#4ade80; padding:0.35rem 0.75rem; border-radius:9999px;">
                        <i class="ri-qr-code-line"></i> Instant QR Ticket
                    </span>
                </div>
            </div>

            <div class="busgo-footer-col">
                <h4>Quick Links</h4>
                <ul class="busgo-footer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('home') }}#book">Search Trips</a></li>
                    <li><a href="{{ route('home') }}#popular">Popular Routes</a></li>
                    <li><a href="{{ route('tickets.find') }}">Find My Ticket</a></li>
                </ul>
            </div>

            <div class="busgo-footer-col">
                <h4>Support</h4>
                <ul class="busgo-footer-links">
                    <li><a href="{{ route('home') }}#features">Travel Safety</a></li>
                    <li><a href="{{ route('home') }}#features">Luggage Policy</a></li>
                    <li><a href="{{ url('/admin') }}" target="_blank">Staff Portal</a></li>
                </ul>
            </div>

            <div class="busgo-footer-col">
                <h4>Contact BusGo Nigeria</h4>
                <ul class="busgo-footer-links">
                    <li>
                        <span style="color:#38bdf8;"><i class="ri-phone-fill"></i></span> +234 (0) 800 287 4646
                    </li>
                    <li>
                        <span style="color:#38bdf8;"><i class="ri-mail-line"></i></span> support@busgo.ng
                    </li>
                    <li>
                        <span style="color:#38bdf8;"><i class="ri-map-pin-2-line"></i></span> Victoria Island, Lagos, Nigeria
                    </li>
                </ul>
            </div>
        </div>

        <div class="busgo-footer-bottom">
            <div>
                Copyright &copy; {{ date('Y') }} BusGo Nigeria. All rights reserved.
            </div>
            <div>
                Safe • Reliable • Air-Conditioned Comfort
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileBtn = document.getElementById('busgo-mobile-btn');
            const navMenu = document.getElementById('busgo-nav-menu');
            if (mobileBtn && navMenu) {
                mobileBtn.addEventListener('click', function () {
                    navMenu.classList.toggle('open');
                    const icon = mobileBtn.querySelector('i');
                    if (icon) {
                        icon.className = navMenu.classList.contains('open') ? 'ri-close-line' : 'ri-menu-line';
                    }
                });
            }
        });
    </script>
    <script src="{{ asset('main.js') }}"></script>
    @stack('scripts')
</body>
</html>

