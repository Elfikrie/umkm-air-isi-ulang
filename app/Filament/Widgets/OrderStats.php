<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        $jam = now()->hour;
        $sapaan = match (true) {
            $jam < 11 => 'Selamat Pagi',
            $jam < 15 => 'Selamat Siang',
            $jam < 18 => 'Selamat Sore',
            default   => 'Selamat Malam',
        };
        $nama = auth()->user()->name ?? 'Admin';

        $pendapatanBulanIni = Order::whereMonth('order_date', now()->month)
            ->where('status', 'diterima')
            ->withSum('items', 'subtotal')
            ->get()
            ->sum('items_sum_subtotal');

        return [
            Stat::make("{$sapaan}, {$nama} 👋", 'Selamat datang di Dashboard Air Isi Ulang')
                ->description('Semoga usahamu lancar hari ini!')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary'),

            Stat::make('Pesanan Hari Ini', Order::whereDate('order_date', today())->count())
                ->description('Jumlah pesanan masuk hari ini')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),

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
