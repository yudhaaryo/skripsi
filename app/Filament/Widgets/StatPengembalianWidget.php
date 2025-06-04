<?php

namespace App\Filament\Widgets;

use App\Models\Pengembalian;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatPengembalianWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 6;




    protected function getCards(): array
    {
        return [
            Card::make('Total Pengembalian', Pengembalian::count())
                ->description('Jumlah seluruh pengembalian alat')
                ->color('info'),
        ];
    }
}
