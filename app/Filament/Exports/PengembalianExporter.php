<?php

namespace App\Filament\Exports;

use App\Models\Pengembalian;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PengembalianExporter extends Exporter
{
    protected static ?string $model = Pengembalian::class;
    public static function resolveRecord($record): \Illuminate\Database\Eloquent\Model
    {
        return $record->load(['peminjaman.alatDetails.alat', 'peminjaman.user']);
    }
    public static function getDefaultDisk(): ?string
    {
        return 'public';
    }
    public static function getDefaultDirectory(): ?string
    {
        return 'exports';
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),

            ExportColumn::make('peminjaman.user.name')->label('Nama Peminjam'),

            ExportColumn::make('nama_alat')
                ->label('Nama Alat')
                ->formatStateUsing(function ($record) {
                    return $record->peminjaman?->alatDetails?->map(function ($detail) {
                        return $detail->alat->nama_alat . ' - Unit ' . $detail->no_unit;
                    })->implode(', ') ?? '';
                }),

            ExportColumn::make('peminjaman.tanggal_pinjam')->label('Tanggal Peminjaman'),

            ExportColumn::make('tanggal_pengembalian')->label('Tanggal Pengembalian'),

            ExportColumn::make('kondisi_pengembalian_detail')
                ->label('Kondisi Pengembalian')
                ->formatStateUsing(function ($record) {
                    return $record->peminjaman?->alatDetails?->map(function ($detail) {
                        $namaAlat = $detail->alat->nama_alat ?? '-';
                        $unit = $detail->no_unit ?? '-';
                        $kondisi = $detail->pivot->kondisi_saat_kembali ?? '-';
                        return "{$namaAlat} (Unit {$unit}) [{$kondisi}]";
                    })->implode(', ') ?? '';
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pengembalian export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
