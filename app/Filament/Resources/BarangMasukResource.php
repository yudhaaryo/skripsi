<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangMasukResource\Pages;
use App\Models\Barang;
use App\Models\BarangMasuk;
use Filament\Forms\Form;
use Filament\Forms\Components\{Select, TextInput, DatePicker};
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Tables\Actions\{EditAction, DeleteAction};

class BarangMasukResource extends Resource
{
    protected static ?string $model = BarangMasuk::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square';
    protected static ?string $navigationGroup = 'Inventaris Barang';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('barang_id')
             ->label('Pilih Barang')
            ->relationship('barang', 'nama_barang_aplikasi')
            ->createOptionForm([
        TextInput::make('nama_barang_aplikasi')->label('Nama di Aplikasi')->required(),
        TextInput::make('nama_barang_asli')->label('Nama Asli')->required(),
        TextInput::make('kode_barang')->label('Kode')->required(),
        TextInput::make('satuan')->required(),
        TextInput::make('harga_beli')->label('Harga Beli')->numeric(),
    ])
    ->searchable()
    ->required(),


            TextInput::make('jumlah_masuk')
                ->label('Jumlah Masuk')
                ->numeric()
                ->required(),

            DatePicker::make('tanggal_masuk')
                ->label('Tanggal Masuk')
                ->required()
                ->default(now()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barang.nama_barang_aplikasi')->label('Nama Barang')->searchable(),
                TextColumn::make('jumlah_masuk')->label('Jumlah Masuk'),
                TextColumn::make('tanggal_masuk')->label('Tanggal Masuk')->date(),
                TextColumn::make('created_at')->label('Dibuat')->since(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ]);
    }



    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangMasuks::route('/'),
            'create' => Pages\CreateBarangMasuk::route('/create'),
            'edit' => Pages\EditBarangMasuk::route('/{record}/edit'),
        ];
    }
}