<?php

namespace App\Filament\Widgets;

use Filament\Widgets\BarChartWidget;
use App\Models\Peminjaman;
use Illuminate\Support\Carbon;

class GrafikPeminjamanBulanan extends BarChartWidget
{
    protected static ?string $heading = 'Grafik Peminjaman Bulanan';

    protected function getData(): array
    {
        $data = Peminjaman::selectRaw('MONTH(tanggal_pinjam) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->mapWithKeys(fn ($item) => [Carbon::create()->month($item->bulan)->format('F') => $item->total]);

        return [
            'labels' => $data->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Jumlah Peminjaman',
                    'data' => $data->values()->toArray(),
                ],
            ],
        ];
    }
}