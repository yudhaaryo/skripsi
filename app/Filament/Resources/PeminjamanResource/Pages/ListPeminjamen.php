<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;

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

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('setujui')
                ->label('Setujui')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn ($record) => Auth::user()->hasRole('admin') && $record->status_pinjam === 'menunggu')
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['status_pinjam' => 'disetujui'])),

            Action::make('proses')
                ->label('Proses')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->visible(fn ($record) => Auth::user()->hasRole('admin') && $record->status_pinjam === 'disetujui')
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['status_pinjam' => 'diproses'])),

            Action::make('tolak')
                ->label('Tolak')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn ($record) => Auth::user()->hasRole('admin') && $record->status_pinjam === 'menunggu')
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['status_pinjam' => 'ditolak'])),
        ];
    }
}
