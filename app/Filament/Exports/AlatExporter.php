<?php

namespace App\Filament\Exports;

use App\Models\Alat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AlatExporter extends Exporter
{
    protected static ?string $model = Alat::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('nama_alat'),
            ExportColumn::make('kode_alat'),
            ExportColumn::make('jumlah_alat'),
            ExportColumn::make('kondisi_alat'),
            ExportColumn::make('merk_alat'),
            ExportColumn::make('sumber_dana'),
            ExportColumn::make('tahun_alat'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your alat export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}