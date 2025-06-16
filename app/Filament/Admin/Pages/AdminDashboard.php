<?php

namespace App\Filament\Admin\Pages;
use App\Filament\Resources\PeminjamanResource\Widgets\TerlambatKembaliWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

use App\Filament\Widgets\GrafikPeminjamanBulanan;
use App\Filament\Widgets\StatWidget;


class AdminDashboard extends BaseDashboard
{
    public static ?string $title = 'Dashboard Admin';

    public function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,

        ];
    }

    public function getWidgets(): array
    {
        return [
            StatWidget::class,

            GrafikPeminjamanBulanan::class,
            TerlambatKembaliWidget::class,
        ];
    }
}
