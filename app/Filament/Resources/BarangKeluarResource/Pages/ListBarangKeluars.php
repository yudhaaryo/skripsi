<?php

namespace App\Filament\Resources\BarangKeluarResource\Pages;

use App\Filament\Resources\BarangKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ListBarangKeluars extends ListRecords
{
    protected static string $resource = BarangKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
{
    return [
        'Semua' => Tab::make(),

        'Barang Keluar Minggu Ini' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->whereBetween('tanggal_keluar', [
                    now()->subDays(7)->toDateString(),
                    now()->toDateString(),
                ])
            ),

        'Barang Keluar Bulan Ini' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->whereMonth('tanggal_keluar', now()->month)
                      ->whereYear('tanggal_keluar', now()->year)
            ),
    ];
}

}
