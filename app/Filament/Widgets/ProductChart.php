<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;

class ProductChart extends ChartWidget
{
    protected static ?string $heading = 'Penjualan Produk Terbanyak';

    protected function getData(): array
    {
        $data = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'diterima')
            ->where('orders.order_date', '>=', now()->subDays(30)->startOfDay())
            ->selectRaw('products.name as product_name, SUM(order_items.quantity) as total')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total')
            ->pluck('total', 'product_name');

        $total = $data->sum();

        $labels = $data->map(function ($value, $name) use ($total) {
            $percentage = $total > 0 ? round(($value / $total) * 100, 1) : 0;
            return "{$name} ({$percentage}%)";
        })->values();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Produk Terjual',
                    'data' => $data->values(),
                    'backgroundColor' => [
                        '#22c55e', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6',
                        '#06b6d4', '#ec4899', '#84cc16', '#f97316', '#6366f1',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getMaxHeight(): ?string
    {
        return '250px'; // atur sesuai kebutuhan, misal '200px', '300px'
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
