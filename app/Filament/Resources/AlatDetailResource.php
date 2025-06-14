<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlatDetailResource\Pages;
use App\Filament\Resources\AlatDetailResource\RelationManagers;
use App\Models\AlatDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class AlatDetailResource extends Resource
{
    protected static ?string $model = AlatDetail::class;
    protected static ?string $navigationGroup = 'Peminjaman Alat';
    protected static ?int $navigationSort = 1;



    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('alat.nama_alat')->label('Nama Alat Induk'),
            TextColumn::make('no_unit')->label('No. Unit'),
            TextColumn::make('kode_alat')->label('Kode Unit'),
            TextColumn::make('tahun_alat')->label('Tahun'),
            TextColumn::make('kondisi_alat')->label('Kondisi'),
            TextColumn::make('status')
                ->label('Status')
                ->getStateUsing(function ($record) {
                    return $record->peminjamans()->where('status_pinjam', 'dipinjam')->exists() ? 'Dipinjam' : 'Tersedia';
                })
                ->badge()
                ->colors([
                    'success' => 'Tersedia',
                    'danger' => 'Dipinjam',
                ]),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlatDetails::route('/'),
            'create' => Pages\CreateAlatDetail::route('/create'),
            'edit' => Pages\EditAlatDetail::route('/{record}/edit'),
        ];
    }
}