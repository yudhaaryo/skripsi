<?php

namespace App\Filament\Resources;

use App\Filament\Exports\BarangExporter;
use App\Filament\Resources\BarangResource\Pages;
use App\Models\Barang;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Carbon\Carbon;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\ActionGroup;


class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Barang';
    protected static ?string $navigationGroup = 'Inventaris Barang';
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }


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
                TextColumn::make('total_barang')
                    ->label('Total Barang')
                    ->getStateUsing(fn($record) => $record->total_barang)
                    ->sortable(),

                TextColumn::make('satuan'),
                TextColumn::make('barangMasuks.tanggal_masuk')
                    ->label('Tanggal Masuk Terakhir')
                    ->formatStateUsing(
                        fn($state, $record) =>
                        optional($record->barangMasuks->sortByDesc('tanggal_masuk')->first())->tanggal_masuk
                    )
                    ->date('d M Y'),

            ])
            ->filters([
                 Filter::make('stok_habis')
                ->label('Stok Habis (0)')
                ->query(function ($query) {
                    $query->whereRaw('
                        (jumlah_awal + 
                            (SELECT IFNULL(SUM(jumlah_masuk),0) FROM barang_masuks WHERE barang_masuks.barang_id = barangs.id) - 
                            (SELECT IFNULL(SUM(jumlah_keluar),0) FROM barang_keluars WHERE barang_keluars.barang_id = barangs.id)
                        ) = 0
                    ');
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
            ])
            ->headerActions([
                ExportAction::make('export_excel')
            ->label('Export Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->form([
        DatePicker::make('bulan')
            ->label('Pilih Bulan')
            ->displayFormat('F Y')
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



        \Filament\Forms\Components\Select::make('filter_penggunaan')
            ->label('Tampilkan Barang')
            ->options([
                'semua' => 'Tampilkan Semua Barang',
                'digunakan' => 'Hanya Barang yang Digunakan Bulan Ini',
                'mutasi' =>'Hanya Barang yang Ada Mutasi Bulan Ini',
                'habis' => 'Hanya Barang yang stok habis',

            ])
            ->default('semua')
            ->required(),
    ])
    ->action(function (array $data) {
        $queryString = http_build_query([
            'bulan' => Carbon::parse($data['bulan'])->format('Y-m'),

            'filter_penggunaan' => $data['filter_penggunaan'],
        ]);

        return redirect()->away(route('export-barang') . '?' . $queryString);
    }),
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
