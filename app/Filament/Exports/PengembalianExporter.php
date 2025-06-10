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
    return $record->load(['peminjaman', 'alatPengembalians']);
}


    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),

            ExportColumn::make('peminjaman.nama_peminjam')
                ->label('Nama Peminjam'),

                ExportColumn::make('nama_alat')
                ->label('Nama Alat')
                ->formatStateUsing(function ($record) {
                    return $record->alatPengembalians->pluck('nama_alat')->implode(', ');
                }),

            ExportColumn::make('peminjaman.tanggal_pinjam')
                ->label('Tanggal Peminjaman'),

            ExportColumn::make('tanggal_pengembalian')
                ->label('Tanggal Pengembalian'),

            ExportColumn::make('kondisi_pengembalian_detail')
                ->label('Kondisi Pengembalian')
                ->formatStateUsing(function ($record) {
                    return $record->alatPengembalians->map(function ($alat) {
                        return $alat->nama_alat . ' (' . $alat->pivot->kondisi_pengembalian . ')';
                    })->implode(', ');
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
