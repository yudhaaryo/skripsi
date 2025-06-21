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
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\ActionGroup;




class BarangMasukResource extends Resource
{
    protected static ?string $model = BarangMasuk::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Inventaris Barang';
    protected static ?int $navigationSort = 0;


    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('barang_id')
                ->label('Pilih Barang / Tambah Barang Baru')
                ->relationship('barang', 'nama_barang_aplikasi')
                ->createOptionForm([
                    TextInput::make('kode_barang')->label('Kode')->required(),
                    TextInput::make('nama_barang_asli')->label('Nama Asli')->required(),
                    TextInput::make('nama_barang_aplikasi')->label('Nama di Aplikasi')->required(),
                    TextInput::make('harga_beli')->label('Harga Beli')->numeric(),
                    TextInput::make('jumlah_awal')->label('Jumlah Awal')->numeric()->required(),
                    TextInput::make('satuan')->required(),
                    DatePicker::make('tanggal_masuk')
                        ->label('Tanggal Masuk')
                        ->required()
                        ->default(now()),
                    
                ])

                ->searchable()
                ->required(),


            TextInput::make('jumlah_masuk')
                ->label('Jumlah Masuk (Jika barang baru kosongkan)')
                ->numeric()
                ->nullable()
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
                ActionGroup::make([
                    EditAction::make(),
                DeleteAction::make()

                ])
                
                ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->actionsPosition(ActionsPosition::BeforeCells)
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