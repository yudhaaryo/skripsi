<?php

namespace App\Filament\Resources\BarangMasukResource\Pages;

use App\Filament\Resources\BarangMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarangMasuks extends ListRecords
{
    protected static string $resource = BarangMasukResource::class;

    protected function getHeaderActions(): array
{
    return [
        Actions\CreateAction::make()
            ->label('Tambah Barang Masuk')
            ->url(route('filament.admin.resources.barang-masuks.create'))
            ,

        Actions\Action::make('Tambah Barang Baru')
            ->label('Tambah Barang Baru')
            ->url(route('filament.admin.resources.barang-masuks.create-barang-baru'))
            ->color('success')

    ];
}

}
