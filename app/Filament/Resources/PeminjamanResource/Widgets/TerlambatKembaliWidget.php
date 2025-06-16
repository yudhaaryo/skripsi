<?php

namespace App\Filament\Resources\PeminjamanResource\Widgets;

use App\Models\Peminjaman;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Filament\Widgets\TableWidget as BaseWidget;

class TerlambatKembaliWidget extends BaseWidget
{
    protected static ?string $heading = 'Daftar Peminjaman Terlambat Kembali';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Peminjaman::query()
                    ->where('status_pinjam', 'dipinjam')
                    ->whereDate('tanggal_kembali', '<', now())
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Peminjam'),
                   

                TextColumn::make('kelas_peminjam')
                    ->label('Kelas'),
                   

                
            ]);
    }
}
