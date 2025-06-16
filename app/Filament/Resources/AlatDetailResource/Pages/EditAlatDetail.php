<?php

namespace App\Filament\Resources\AlatDetailResource\Pages;

use App\Filament\Resources\AlatDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlatDetail extends EditRecord
{
    protected static string $resource = AlatDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
