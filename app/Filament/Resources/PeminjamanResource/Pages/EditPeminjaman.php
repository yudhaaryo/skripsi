<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPeminjaman extends EditRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected array $alatPivotData = [];

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();
        return $user?->hasAnyRole(['admin', 'guru']) || $user?->name === $parameters['record']?->nama_peminjam;
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['alat_details'] = $this->record->alatDetails
            ? $this->record->alatDetails->map(function ($detail) {
                return [
                    'alat_detail_id' => $detail->id,
                    'jumlah_pinjam' => $detail->pivot->jumlah_pinjam ?? 1,
                    'kondisi_saat_pinjam' => $detail->pivot->kondisi_saat_pinjam ?? '',
                    // tambah field lain kalau ada
                ];
            })->toArray()
            : [];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->alatPivotData = collect($data['alat_details'] ?? [])
            ->mapWithKeys(fn ($item) => [
                $item['alat_detail_id'] => [
                    'jumlah_pinjam' => $item['jumlah_pinjam'] ?? 1,
                    'kondisi_saat_pinjam' => $item['kondisi_saat_pinjam'] ?? '',
                    // tambah field lain kalau ada
                ]
            ])
            ->toArray();

        unset($data['alat_details']);
        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->alatDetails()->sync($this->alatPivotData);
    }


    protected function getRedirectUrl(): string
    {
        return PeminjamanResource::getUrl(name: 'index');
    }
}