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

class BarangKeluarResource extends Resource
{
    protected static ?string $model = BarangKeluar::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-circle';
    protected static ?string $navigationGroup = 'Inventaris';
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
                TextColumn::make('barang.nama_barang_asli')->label('Nama Barang'),
                TextColumn::make('jumlah_keluar')->label('Jumlah Keluar'),
                TextColumn::make('tanggal_keluar')->date('d M Y'),
                TextColumn::make('tujuan')->limit(30),
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                ExportAction::make('export_excel')
    ->label('Export Excel')
    ->icon('heroicon-o-arrow-down-tray')
    ->form([
        Forms\Components\DatePicker::make('bulan')
            ->label('Pilih Bulan')
            ->displayFormat('F Y') // Menampilkan seperti "Juni 2025"
            ->native(false)
            ->closeOnDateSelection(true)
            ->extraAttributes([
                'data-enable-time' => 'false',
                'data-no-calendar' => 'false',
                'data-date-format' => 'Y-m',
                'data-alt-input' => 'true',
                'data-alt-format' => 'F Y',
                'data-default-date' => now()->format('Y-m'),
                'data-plugins' => '[\"monthSelectPlugin\"]',
            ])
            ->reactive()
            ->required(),



        Forms\Components\Select::make('filter_penggunaan')
            ->label('Tampilkan Barang')
            ->options([
                'semua' => 'Tampilkan Semua Barang',
                'digunakan' => 'Hanya Barang yang Digunakan Bulan Ini',
            ])
            ->default('semua')
            ->required(),
    ])
    ->action(function (array $data) {
        $queryString = http_build_query([
            'bulan' => Carbon::parse($data['bulan'])->format('Y-m'),

            'filter_penggunaan' => $data['filter_penggunaan'], // tambahkan ini
        ]);

        return redirect()->away(route('export-barang') . '?' . $queryString);
    }),
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