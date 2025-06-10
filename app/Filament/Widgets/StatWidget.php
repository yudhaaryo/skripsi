<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use App\Models\Alat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatWidget extends BaseWidget

{

    protected function getStats(): array
    {
        $totalPeminjaman = Peminjaman::count();

        $masihDipinjam = Peminjaman::where('status_pinjam', 'dipinjam')->count();

        $totalAlatTersedia = Alat::sum('jumlah_alat');

        return [
            Stat::make('Total Peminjaman', $totalPeminjaman)
                ->description('Jumlah seluruh permintaan peminjaman')
                ->color('primary'),
            Stat::make('Sedang Dipinjam', $masihDipinjam)
                ->description('Jumlah alat yang sedang dipinjam')
            ->color('warning'),
            Stat::make('Total Alat Tersedia', $totalAlatTersedia)
                ->description('Jumlah total alat yang tersedia')
                ->color('success'),
        ];
    }
}
