<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    public function getHeaderWidgets(): array
    {

        return [
            AccountWidget::class,
            FilamentInfoWidget::class,
        ];
    }


    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public function getFooterWidgets(): array
    {
        return [];
    }
}
