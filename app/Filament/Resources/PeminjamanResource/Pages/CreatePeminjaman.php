<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePeminjaman extends CreateRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected array $alatPivotData = [];



    protected function mutateFormDataBeforeCreate(array $data): array
{
    $this->alatPivotData = collect($data['alats'] ?? [])
        ->mapWithKeys(fn ($item) => [
            $item['alat_detail_id'] => [
                'kondisi_saat_pinjam' => $item['kondisi_saat_pinjam'] ?? null,
                'keterangan' => $item['keterangan'] ?? null,
            ]
        ])
        ->toArray();

    unset($data['alats']);
    $data['user_id'] = auth()->id();

    return $data;
}

protected function afterCreate(): void
{
    $this->record->alatDetails()->sync($this->alatPivotData);
}


    protected function getRedirectUrl(): string
    {
        return PeminjamanResource::getUrl(name: 'index');
    }
}