<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatPeminjamanWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 6;




    protected function getCards(): array
    {
        return [
            Card::make('Total Peminjaman', Peminjaman::count())
                ->description('Jumlah seluruh permintaan peminjaman')
                ->color('primary'),
        ];
    }
}
