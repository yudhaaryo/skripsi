<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewPeminjaman extends ViewRecord
{
    protected static string $resource = PeminjamanResource::class;

    // JANGAN pakai static!
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Daftar Unit Alat yang Dipinjam')
                    ->schema([
                        TextEntry::make('alatDetails')
                            ->label('Unit & Kondisi')
                            ->formatStateUsing(function ($state, $record) {
                                return $record->alatDetails->map(function ($detail) {
                                    return $detail->alat->nama_alat . ' - Unit ' . $detail->no_unit . ' (Kondisi: ' . ($detail->pivot->kondisi_saat_pinjam ?? $detail->kondisi_alat) . ')';
                                })->join(', ');
                            }),
                    ]),
            ]);
    }
}



