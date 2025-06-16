<?php

namespace App\Filament\Resources\AlatDetailResource\Pages;

use App\Filament\Resources\AlatDetailResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAlatDetail extends CreateRecord
{
    protected static string $resource = AlatDetailResource::class;

    protected $alatId;
    protected $details = [];

  protected function mutateFormDataBeforeCreate(array $data): array
{
    $alat = \App\Models\Alat::find($data['alat_id']);
    $data['kode_alat'] = $alat ? $alat->kode_alat : null;
    return $data;
}

    protected function afterCreate(): void
    {
        
        $alat = \App\Models\Alat::find($this->alatId);
        $kodeAlatMaster = $alat ? $alat->kode_alat : null;

        foreach ($this->details as $detail) {
            \App\Models\AlatDetail::create([
                'alat_id'      => $this->alatId,
                'no_unit'      => $detail['no_unit'],
                'tahun_alat'   => $detail['tahun_alat'],
                'kode_alat'    => $kodeAlatMaster, 
                'kondisi_alat' => $detail['kondisi_alat'],
                'keterangan'   => $detail['keterangan'] ?? null,
            ]);
        }
    }
     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
