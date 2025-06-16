<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Filament\Resources\Components\Tab;





class ListPeminjamen extends ListRecords
{
    protected static string $resource = PeminjamanResource::class;

    public function getFilteredTableQuery(): Builder
    {

        return parent::getFilteredTableQuery();
    }

    public function canCreate(): bool
    {
        return Auth::user()?->hasAnyRole(['admin', 'guru', 'siswa']);
    }
    public function getTabs(): array
{
    return [
        'Semua' => Tab::make(),

        'Peminjaman Minggu Ini' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->whereBetween('tanggal_pinjam', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString(),
                ])
            ),

        'Peminjaman Bulan Ini' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->whereMonth('tanggal_pinjam', now()->month)
                      ->whereYear('tanggal_pinjam', now()->year)
            ),
    ];
}


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    
}
