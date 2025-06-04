<?php

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use App\Filament\Widgets\StatPeminjamanWidget;
use App\Filament\Widgets\StatPengembalianWidget;
use App\Filament\Widgets\GrafikPeminjamanBulanan;
use Filament\Tables\Columns\Layout\Grid as LayoutGrid;
use Filament\Widgets\Grid;

class AdminDashboard extends BaseDashboard
{
    public static ?string $title = 'Dashboard Admin';

    public function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
                    StatPeminjamanWidget::class,
                    StatPengembalianWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            GrafikPeminjamanBulanan::class,
        ];
    }
}
