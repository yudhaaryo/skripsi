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
        $data['alats'] = $this->record->alats->map(function ($alat) {
            return [
                'alat_id' => $alat->id,
                'jumlah_pinjam' => $alat->pivot->jumlah_pinjam,
                'kondisi_peminjaman' => $alat->pivot->kondisi_peminjaman,
            ];
        })->toArray();

        return $data;
    }

    /**
     * Ambil data alat dari form sebelum update record utama
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->alatPivotData = collect($data['alats'] ?? [])
            ->mapWithKeys(fn ($item) => [
                $item['alat_id'] => [
                    'jumlah_pinjam' => $item['jumlah_pinjam'] ?? 1,
                    'kondisi_peminjaman' => $item['kondisi_peminjaman'] ?? null,
                ]
            ])
            ->toArray();

        unset($data['alats']);
        return $data;
    }

    /**
     * Update data pivot setelah update utama
     */
    protected function afterSave(): void
    {
        $this->record->alats()->sync($this->alatPivotData);
    }

    protected function getRedirectUrl(): string
    {
        return PeminjamanResource::getUrl(name: 'index');
    }
}