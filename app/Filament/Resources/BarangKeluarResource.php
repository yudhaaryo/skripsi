<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangKeluarResource\Pages;
use App\Models\BarangKeluar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ExportAction;
use Carbon\Carbon;
use Filament\Tables\Actions\Modal\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;

class BarangKeluarResource extends Resource
{
    protected static ?string $model = BarangKeluar::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-circle';
    protected static ?string $navigationGroup = 'Inventaris Barang';
    protected static ?string $label = 'Barang Keluar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('barang_id')
                    ->label('Nama Barang')
                    ->relationship('barang', 'nama_barang_asli')
                    ->searchable()
                    ->required(),

                TextInput::make('jumlah_keluar')
                    ->numeric()
                    ->required(),

                Textarea::make('tujuan')
                    ->label('Tujuan Pengeluaran')
                    ->nullable(),

                DatePicker::make('tanggal_keluar')
                    ->required()
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barang.nama_barang_asli')
                ->label('Nama Barang')
                ->searchable(),
                TextColumn::make('jumlah_keluar')
                ->label('Jumlah Keluar')
                ->numeric()
                ->sortable(),
                TextColumn::make('tanggal_keluar')
                ->date('d M Y')
                ->label('Tanggal Keluar')
                ->sortable()
                ->searchable(),
                TextColumn::make('tujuan')->limit(30)
                ->searchable()
                ->label('Tujuan Pengeluaran'),
            ])
            ->filters([
                Filter::make('tanggal_keluar_range')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('tanggal_keluar', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('tanggal_keluar', '<=', $data['until']));
                    }),
            ])


            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->headerActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangKeluars::route('/'),
            'create' => Pages\CreateBarangKeluar::route('/create'),
            'edit' => Pages\EditBarangKeluar::route('/{record}/edit'),
        ];
    }
}
