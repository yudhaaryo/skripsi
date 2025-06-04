<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePeminjaman extends CreateRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected array $alatPivotData = [];

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();
        return $user?->hasAnyRole(['admin', 'guru', 'siswa']);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
{
    // Ambil data pivot alat
    $this->alatPivotData = collect($data['alats'] ?? [])
        ->mapWithKeys(fn ($item) => [
            $item['alat_id'] => [
                'jumlah_pinjam' => $item['jumlah_pinjam'] ?? 1,
                'kondisi_peminjaman' => $item['kondisi_peminjaman'] ?? null,
            ]
        ])
        ->toArray();

    unset($data['alats']);
    $data['user_id'] = auth()->id();

    return $data;
}


    protected function afterCreate(): void
    {
        // Simpan relasi pivot setelah data utama dibuat
        $this->record->alats()->sync($this->alatPivotData);
    }

    protected function getRedirectUrl(): string
    {
        return PeminjamanResource::getUrl(name: 'index');
    }
}
