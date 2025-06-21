<?php

namespace App\Filament\Resources\PenghapusanInventarisResource\Pages;

use App\Filament\Resources\PenghapusanInventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenghapusanInventaris extends ListRecords
{
    protected static string $resource = PenghapusanInventarisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
