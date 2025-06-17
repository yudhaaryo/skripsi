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
use Filament\Tables\Actions\{EditAction, DeleteAction, DeleteBulkAction};
use Filament\Tables\Filters\Filter;



class BarangMasukResource extends Resource
{
    protected static ?string $model = BarangMasuk::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Inventaris Barang';
    protected static ?int $navigationSort = 1;


  public static function form(Form $form): Form
{
    return $form->schema([
        Select::make('barang_id')
            ->label('Pilih Barang / Tambah Barang Baru')
            ->relationship('barang', 'nama_barang_aplikasi')
            ->createOptionForm([
                TextInput::make('nama_barang_asli')->label('Nama Asli')->required(),
                TextInput::make('nama_barang_aplikasi')->label('Nama di Aplikasi')->required(),
                TextInput::make('kode_barang')->label('Kode')->required(),
                TextInput::make('satuan')->required(),
                TextInput::make('harga_beli')->label('Harga Beli')->numeric(),
                DatePicker::make('tanggal_masuk')->label('Tanggal Masuk')->required()->default(now()),
                TextInput::make('jumlah_awal')->label('Jumlah Awal')->numeric()->required(),
            ])
            ->searchable()
            ->required(),

        // Ini hanya di form utama, tidak ikut modal create
        TextInput::make('jumlah_masuk')
            ->label('Jumlah Masuk')
            ->numeric()
            ->required()
            ->visible(fn ($livewire) => !method_exists($livewire, 'getCreateOptionForm') && !method_exists($livewire, 'mountCreateOptionForm')),

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
                TextColumn::make('tanggal_masuk')->label('Tanggal Masuk')->date() ->searchable() ,
                TextColumn::make('created_at')->label('Dibuat')->since(),
            ])
            ->filters([
                Filter::make('tanggal_masuk_range')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('tanggal_masuk', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('tanggal_masuk', '<=', $data['until']));
                    }),
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
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