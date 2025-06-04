<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengembalianResource\Pages;
use App\Models\Pengembalian;
use Filament\Tables\Actions\ExportAction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Exports\PengembalianExporter;

class PengembalianResource extends Resource
{
    protected static ?string $model = Pengembalian::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Peminjaman Alat';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('tanggal_pengembalian')
                    ->required(),
                TextInput::make('kondisi_pengembalian')
                    ->required(),
                TextInput::make('peminjaman_id')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('id')->label('ID'),

            TextColumn::make('peminjaman.nama_peminjam')->label('Nama Peminjam'),

            TextColumn::make('peminjaman.tanggal_pinjam')->label('Tanggal Peminjaman'),

            TextColumn::make('tanggal_pengembalian')->label('Tanggal Pengembalian'),

            TextColumn::make('peminjaman.alats')
                ->label('Nama Alat')
                ->formatStateUsing(function ($state, $record) {
                    return $record->peminjaman->alats->pluck('nama_alat')->implode(', ');
                })
                ->wrap(),

            TextColumn::make('kondisi_pengembalian')
                ->label('Kondisi Pengembalian')
                ->formatStateUsing(function ($state, $record) {
                    return $record->peminjaman->alats->map(function ($alat) {
                        return $alat->nama_alat . ' (' . ($alat->pivot->kondisi_peminjaman ?? '-') . ')';
                    })->implode(', ');
                })
                ->wrap(),
        ])
        ->actions([
            EditAction::make(),
            DeleteAction::make(),
            ViewAction::make(),
        ])
        ->bulkActions([
            BulkActionGroup::make([
                ExportBulkAction::make()
                    ->exporter(PengembalianExporter::class),
                DeleteBulkAction::make(),
            ]),
        ])
        ->headerActions([
            ExportAction::make()
                ->exporter(PengembalianExporter::class),
        ]);
}



    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengembalians::route('/'),
            'create' => Pages\CreatePengembalian::route('/create'),
            'edit' => Pages\EditPengembalian::route('/{record}/edit'),
        ];
    }
}
