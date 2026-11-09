<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class BookingsChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue & Paid Bookings (Last 14 Days)';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $labels = [];
        $revenueData = [];
        $bookingsData = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M j');

            $dayRevenue = Booking::where('status', 'paid')
                ->whereDate('paid_at', $date->toDateString())
                ->sum('amount');
            $revenueData[] = (float) $dayRevenue;

            $dayCount = Booking::where('status', 'paid')
                ->whereDate('paid_at', $date->toDateString())
                ->count();
            $bookingsData[] = $dayCount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (₦)',
                    'data' => $revenueData,
                    'borderColor' => '#1d4ed8',
                    'backgroundColor' => 'rgba(29, 78, 216, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Paid Bookings Count',
                    'data' => $bookingsData,
                    'borderColor' => '#16a34a',
                    'backgroundColor' => 'rgba(22, 163, 74, 0.1)',
                    'fill' => false,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

