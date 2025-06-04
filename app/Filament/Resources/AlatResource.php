<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlatResource\Pages;
use App\Models\Alat;
use App\Filament\Exports\AlatExporter;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class AlatResource extends Resource
{
    protected static ?string $model = Alat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Peminjaman Alat';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama_alat')
                ->label('Nama Alat')
                ->required(),

            TextInput::make('kode_alat')
                ->label('Kode Alat')
                ->required(),

            TextInput::make('jumlah_alat')
                ->label('Jumlah Alat')
                ->numeric()
                ->required(),

            TextInput::make('kondisi_alat')
                ->label('Kondisi Alat')
                ->required(),

            TextInput::make('merk_alat')
                ->label('Merk Alat')
                ->nullable()
                ->required(),

            TextInput::make('sumber_dana')
                ->label('Sumber Dana')
                ->nullable()
                ->required(),

            TextInput::make('tahun_alat')
                ->label('Tahun Alat')
                ->numeric()
                ->minValue(1900)
                ->maxValue(now()->year + 1)
                ->nullable()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_alat')->label('Nama Alat')->sortable()->searchable(),
                TextColumn::make('kode_alat')->label('Kode Alat')->sortable()->searchable(),
                TextColumn::make('jumlah_alat')->label('Jumlah Alat')->sortable(),
                TextColumn::make('kondisi_alat')->label('Kondisi Alat'),
                TextColumn::make('merk_alat')->label('Merk Alat')->sortable()->searchable(),
                TextColumn::make('sumber_dana')->label('Sumber Dana')->sortable()->searchable(),
                TextColumn::make('tahun_alat')->label('Tahun Alat'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(AlatExporter::class),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlats::route('/'),
            'create' => Pages\CreateAlat::route('/create'),
            'edit' => Pages\EditAlat::route('/{record}/edit'),
        ];
    }
}
