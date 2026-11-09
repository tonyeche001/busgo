<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Trip;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 7-day sparkline data for revenue
        $revenueTrend = [];
        $bookingsTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $revenueTrend[] = (float) Booking::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('amount');
            $bookingsTrend[] = Booking::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->count();
        }

        $totalRevenue = Booking::where('status', 'paid')->sum('amount');
        $paidCount = Booking::where('status', 'paid')->count();
        $upcomingTrips = Trip::where('status', 'scheduled')
            ->where('departure_at', '>', now())
            ->count();
        $activeBuses = Bus::where('is_active', true)->count();
        $activeRoutes = BusRoute::where('is_active', true)->count();

        return [
            Stat::make('Total Revenue', '₦' . number_format($totalRevenue))
                ->description('Total ticket sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart(count(array_filter($revenueTrend)) > 0 ? $revenueTrend : [0, 0, 0, 1, 2, 4, (int) $totalRevenue]),

            Stat::make('Paid Bookings', number_format($paidCount))
                ->description('Confirmed passenger seats')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary')
                ->chart(count(array_filter($bookingsTrend)) > 0 ? $bookingsTrend : [0, 1, 1, 2, 3, 4, $paidCount]),

            Stat::make('Upcoming Trips', number_format($upcomingTrips))
                ->description('Scheduled departures')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Active Buses', number_format($activeBuses))
                ->description("{$activeRoutes} active routes")
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),
        ];
    }
}
