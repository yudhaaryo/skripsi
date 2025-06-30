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
    ->label('Pilih Barang')
    ->relationship('barang', 'nama_barang_aplikasi')
    ->searchable()
    ->required(),

DatePicker::make('tanggal_masuk')
    ->label('Tanggal Masuk')
    ->required()
    ->default(now())
    ->dehydrateStateUsing(function ($state) {
        if (is_array($state)) {
            return $state[0] ?? now()->toDateString();
        }
        if (is_string($state) && str_contains($state, ',')) {
            return trim(explode(',', $state)[0]);
        }
        return $state;
    }),


TextInput::make('jumlah_masuk')
    ->label('Jumlah Masuk')
    ->numeric()
    ->required()
     ->rules(['min:1'])
    ]);
}


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barang.nama_barang_aplikasi')->label('Nama Barang')->searchable(),
                TextColumn::make('jumlah_masuk')->label('Jumlah Masuk'),
                TextColumn::make('tanggal_masuk')->label('Tanggal Masuk')->date()->searchable(),
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
            'create-barang-baru' => Pages\CreateBarangBaruMasuk::route('/create-barang-baru'),
        ];
    }
}