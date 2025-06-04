<?php

namespace App\Filament\Resources;

use App\Filament\Exports\BarangExporter;
use App\Filament\Resources\BarangResource\Pages;
use App\Models\Barang;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Barang';
    protected static ?string $navigationGroup = 'Inventaris';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('kode_barang')->required()->maxLength(255),
            TextInput::make('nama_barang_asli')->required()->maxLength(255),
            TextInput::make('nama_barang_aplikasi')->required()->maxLength(255),
            TextInput::make('harga_beli')->required()->numeric(),
            TextInput::make('jumlah_awal')->required()->numeric(),
            TextInput::make('satuan')->required()->maxLength(100),
            DatePicker::make('tanggal_masuk')->label('Tanggal Masuk')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_barang')->searchable(),
                TextColumn::make('nama_barang_asli')->label('Nama Asli')->searchable(),
                TextColumn::make('nama_barang_aplikasi')->label('Nama di Aplikasi')->searchable(),
                TextColumn::make('harga_beli')->money('IDR', true),
                TextColumn::make('jumlah_awal'),
                TextColumn::make('satuan'),
                TextColumn::make('tanggal_masuk')->label('Tanggal Masuk')->date('d M Y'),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->headerActions([
                ExportAction::make()->exporter(BarangExporter::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}