<?php

namespace App\Filament\Resources\AlatResource\Pages;

use App\Filament\Resources\AlatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlat extends EditRecord
{
    protected static string $resource = AlatResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return false;
    // }
     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
