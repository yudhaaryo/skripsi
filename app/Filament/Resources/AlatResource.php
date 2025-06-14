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
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class AlatResource extends Resource
{
    protected static ?string $model = Alat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Peminjaman Alat';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama_alat')
                ->label('Nama Alat')
                ->required(),

            TextInput::make('kode_alat')
                ->label('Kode Alat (Jenis)')
                ->required(),

            TextInput::make('jumlah_alat')
                ->label('Jumlah Alat')
                ->numeric()
                ->readOnly()
                ->helperText('Otomatis sesuai jumlah detail/unit')
                ->default(0)
                ->required(),

            TextInput::make('merk_alat')
                ->label('Merk Alat')
                ->nullable()
                ->required(),

            TextInput::make('sumber_dana')
                ->label('Sumber Dana')
                ->nullable()
                ->required(),



            Repeater::make('details')
                ->label('Detail/Unit Alat')
                ->relationship('details')
                ->schema([
                    TextInput::make('no_unit')->label('No. Unit')->required(),
                    TextInput::make('kode_alat')
                        ->label('Kode Alat (Unit)')
                        ->required(),
                    TextInput::make('tahun_alat')
                        ->label('Tahun Alat')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(now()->year + 1)
                        ->required(),
                    Select::make('kondisi_alat')->label('Kondisi')->options([
                        'Baik' => 'Baik',
                        'Rusak' => 'Rusak',
                        'Hilang' => 'Hilang',
                    ])->required(),
                    TextInput::make('keterangan')->label('Keterangan')->nullable(),
                ])
                ->minItems(1)
                ->columns(2)
                ->afterStateUpdated(function ($state, callable $set) {

                    $set('jumlah_alat', count($state ?? []));
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_alat')->label('Nama Alat')->sortable()->searchable(),
                TextColumn::make('kode_alat')->label('Kode Alat')->sortable()->searchable(),
                TextColumn::make('total_unit')
                    ->label('Total Unit')
                    ->getStateUsing(fn($record) => $record->details()->count()),
                // Hitung unit tersedia (tidak sedang dipinjam)
                TextColumn::make('unit_tersedia')
                    ->label('Unit Tersedia')
                    ->getStateUsing(
                        fn($record) =>
                        $record->details()->whereDoesntHave('peminjamans', function ($q) {
                            $q->where('status_pinjam', 'dipinjam');
                        })->count()
                    ),
                // Hitung unit sedang dipinjam
                TextColumn::make('unit_dipinjam')
                    ->label('Unit Dipinjam')
                    ->getStateUsing(
                        fn($record) =>
                        $record->details()->whereHas('peminjamans', function ($q) {
                            $q->where('status_pinjam', 'dipinjam');
                        })->count()
                    ),
                TextColumn::make('total_unit')
                    ->label('Total Unit')
                    ->getStateUsing(fn($record) => $record->details()->count()),

                TextColumn::make('merk_alat')->label('Merk Alat')->sortable()->searchable(),
                TextColumn::make('sumber_dana')->label('Sumber Dana')->sortable()->searchable(),
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


    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $existingAlat = Alat::where('nama_alat', $data['nama_alat'])
            ->where('kode_alat', $data['kode_alat'])
            ->first();

        if ($existingAlat) {

            if (isset($data['details'])) {
                foreach ($data['details'] as $detail) {
                    $existingAlat->details()->create($detail);
                }
            }


            $existingAlat->jumlah_alat = $existingAlat->details()->count();
            $existingAlat->save();

            Notification::make()
                ->title('Alat Sudah Ada')
                ->body('Jumlah alat dan detail berhasil ditambahkan ke alat yang sudah ada.')
                ->success()
                ->send();


            return [];
        }


        if (isset($data['details'])) {
            $data['jumlah_alat'] = count($data['details']);
        } else {
            $data['jumlah_alat'] = 0;
        }

        return $data;
    }

    /**
     * Update jumlah_alat otomatis setelah edit/simpan.
     */
    public static function afterSave($record, $data)
    {
        $record->jumlah_alat = $record->details()->count();
        $record->save();
    }
}