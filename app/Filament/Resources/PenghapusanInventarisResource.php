<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenghapusanInventarisResource\Pages;
use App\Filament\Resources\PenghapusanInventarisResource\RelationManagers;
use App\Models\PenghapusanInventaris;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextArea;
use Filament\Tables\Columns\TextColumn;

use Filament\Actions\Action;

class PenghapusanInventarisResource extends Resource
{
    protected static ?string $model = PenghapusanInventaris::class;
    protected static ?string $navigationGroup = 'Inventaris Barang';
    
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('jenis_inventaris')
                ->label('Jenis Inventaris')
                ->options([
                    'alat' => 'Alat',
                    'barang' => 'Barang',
                ])
                ->required()
                ->reactive(),

            // Pilihan ALAT
            Select::make('inventaris_id')
                ->label('Pilih Alat/Unit atau Barang')
                ->options(function (callable $get) {
                    if ($get('jenis_inventaris') === 'alat') {
                        // Pilihan alat detail
                        return \App\Models\AlatDetail::all()->mapWithKeys(function ($unit) {
                            return [
                                $unit->id => $unit->alat->nama_alat . ' - Unit ' . $unit->no_unit . ' (Kondisi: ' . $unit->kondisi_alat . ')'
                            ];
                        });
                    } elseif ($get('jenis_inventaris') === 'barang') {
                        // Pilihan barang
                        return \App\Models\Barang::all()->mapWithKeys(function ($barang) {
                            return [
                                $barang->id => $barang->nama_barang_aplikasi . ' (' . $barang->kode_barang . ')'
                            ];
                        });
                    }
                    return [];
                })
                ->searchable()
                ->required(),

            TextInput::make('alasan_penghapusan')
                ->label('Alasan Penghapusan')
                ->required(),

            DatePicker::make('tanggal_penghapusan')
                ->label('Tanggal Penghapusan')
                ->required(),

            Textarea::make('keterangan')
                ->label('Keterangan Tambahan')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jenis_inventaris')->label('Jenis'),
                TextColumn::make('inventaris_nama')
                    ->label('Nama Inventaris')
                    ->getStateUsing(function ($record) {
                        if ($record->jenis_inventaris === 'alat') {
                            $unit = \App\Models\AlatDetail::find($record->inventaris_id);
                            return $unit ? ($unit->alat->nama_alat . ' - Unit ' . $unit->no_unit) : '-';
                        } elseif ($record->jenis_inventaris === 'barang') {
                            $barang = \App\Models\Barang::find($record->inventaris_id);
                            return $barang ? $barang->nama_barang_aplikasi : '-';
                        }
                        return '-';
                    }),
                TextColumn::make('alasan_penghapusan')->label('Alasan'),
                TextColumn::make('tanggal_penghapusan')->label('Tanggal'),
                TextColumn::make('keterangan')->label('Keterangan'),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenghapusanInventaris::route('/'),
            'create' => Pages\CreatePenghapusanInventaris::route('/create'),
            'edit' => Pages\EditPenghapusanInventaris::route('/{record}/edit'),
        ];
    }
    public static function mutateFormDataBeforeCreate(array $data): array
{
    $data['user_id'] = auth()->id();

    return $data;
}

}
