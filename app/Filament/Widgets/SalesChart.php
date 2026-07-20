<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Penjualan Air Isi Ulang (7 Hari Terakhir)';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $labels[] = $tanggal->translatedFormat('d M');

            $total = Order::whereDate('order_date', $tanggal->toDateString())
                ->where('status', 'diterima')
                ->withSum('items', 'subtotal')
                ->get()
                ->sum('items_sum_subtotal');

            $data[] = $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data,
                    'borderColor' => '#0ea5e9', // biru air
                    'backgroundColor' => 'rgba(14, 165, 233, 0.2)',
                    'fill' => true,
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
