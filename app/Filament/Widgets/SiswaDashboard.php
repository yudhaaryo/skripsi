<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class SiswaDashboard extends BaseWidget
{

    // protected function getStats(): array
    // {
    //     $nama = Auth::user()->name;

    //     return [
    //         Stat::make('Total Peminjaman', Peminjaman::where('nama_peminjam', $nama)->count()),
    //         Stat::make('Sedang Dipinjam', Peminjaman::where('nama_peminjam', $nama)->where('status_pinjam', 'dipinjam')->count()),
    //         Stat::make('Sudah Dikembalikan', Peminjaman::where('nama_peminjam', $nama)->where('status_pinjam', 'dikembalikan')->count()),
    //     ];
    // }
}
