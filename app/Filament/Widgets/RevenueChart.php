<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pendapatan (30 Hari Terakhir)';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(
            fn ($i) => now()->subDays($i)->format('Y-m-d')
        );

        $revenues = Order::where('status', 'diterima')
            ->where('order_date', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(order_date) as tanggal, SUM(total_amount) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $data = $days->map(fn ($date) => (float) ($revenues[$date] ?? 0));

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days->map(fn ($d) => Carbon::parse($d)->format('d M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
