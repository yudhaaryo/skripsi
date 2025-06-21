<?php

namespace App\Filament\Resources\PenghapusanInventarisResource\Pages;

use App\Filament\Resources\PenghapusanInventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePenghapusanInventaris extends CreateRecord
{
    protected static string $resource = PenghapusanInventarisResource::class;
     protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
