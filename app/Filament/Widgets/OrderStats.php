<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        $pendapatanHariIni = Order::whereDate('order_date', today())
            ->where('status', 'diterima')
            ->sum('total_amount');

        $pendapatanBulanIni = Order::whereMonth('order_date', now()->month)
            ->where('status', 'diterima')
            ->sum('total_amount');

        return [
            Stat::make('Pesanan Hari Ini', Order::whereDate('order_date', today())->count())
                ->description('Jumlah pesanan masuk hari ini')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),

            Stat::make('Pendapatan Hari Ini', 'Rp' . number_format($pendapatanHariIni, 0, ',', '.'))
                ->description('Total Pendapatan Hari Ini (status diterima)')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description('Total pendapatan bulan ini (status diterima)')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pesanan Pending', Order::where('status', 'pending')->count())
                ->description('Perlu segera diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
