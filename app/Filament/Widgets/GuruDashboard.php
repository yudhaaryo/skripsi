<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use App\Models\Alat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GuruDashboard extends BaseWidget

{

    protected function getStats(): array
    {
        $totalPeminjaman = Peminjaman::count();

        $masihDipinjam = Peminjaman::where('status_pinjam', 'dipinjam')->count();

        $totalAlatTersedia = Alat::sum('jumlah_alat');

        return [
            Stat::make('Total Peminjaman', $totalPeminjaman),
            Stat::make('Sedang Dipinjam', $masihDipinjam),
            Stat::make('Total Alat Tersedia', $totalAlatTersedia),
        ];
    }
}
