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

    // Admin dan guru boleh akses semua
    if ($user?->hasAnyRole(['admin', 'guru'])) {
        return true;
    }

    // Jika record tersedia (siswa), cek apakah itu miliknya
    if (isset($parameters['record']) && $parameters['record'] instanceof \App\Models\Peminjaman) {
        return $parameters['record']->user_id === $user->id;
    }

    return false;
}



    protected function mutateFormDataBeforeFill(array $data): array
{
    $data['alats'] = $this->record->alatDetails
        ? $this->record->alatDetails->map(function ($detail) {
            return [
                'alat_detail_id' => $detail->id,
                'kondisi_saat_pinjam' => $detail->pivot->kondisi_saat_pinjam ?? '',
                'keterangan' => $detail->pivot->keterangan ?? '',
            ];
        })->toArray()
        : [];
    return $data;
}

protected function mutateFormDataBeforeSave(array $data): array
{
    $this->alatPivotData = collect($data['alats'] ?? [])
        ->mapWithKeys(fn ($item) => [
            $item['alat_detail_id'] => [
                'kondisi_saat_pinjam' => $item['kondisi_saat_pinjam'] ?? '',
                'keterangan' => $item['keterangan'] ?? '',
            ]
        ])
        ->toArray();

    unset($data['alats']);
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