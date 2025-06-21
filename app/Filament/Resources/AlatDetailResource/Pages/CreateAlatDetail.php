<?php

namespace App\Filament\Resources\AlatDetailResource\Pages;

use App\Filament\Resources\AlatDetailResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAlatDetail extends CreateRecord
{
    protected static string $resource = AlatDetailResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
