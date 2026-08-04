<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WelcomeStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getColumns(): int
        {
            return 1;
        }

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

        return [
            Stat::make("{$sapaan}, {$nama} 👋", 'Selamat datang di Dashboard Air Isi Ulang')
                ->description('Semoga usahamu lancar hari ini! Tetap semangat dan terus berkembang selalu dan jangan lupa untuk selalu menjaga kualitas produk dan pelayananmu agar pelanggan tetap puas dan loyal. Semoga hari ini menjadi hari yang produktif dan menyenangkan bagi bisnismu!')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary'),
        ];
    }
}
