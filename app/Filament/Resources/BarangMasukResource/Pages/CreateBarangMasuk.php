<?php

namespace App\Filament\Resources\BarangMasukResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Models\Barang;
use App\Filament\Resources\BarangMasukResource;

class CreateBarangMasuk extends CreateRecord
{
    protected static string $resource = BarangMasukResource::class;
     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    
}
