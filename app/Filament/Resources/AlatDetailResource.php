<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlatDetailResource\Pages;
use App\Filament\Resources\AlatDetailResource\RelationManagers;
use App\Models\AlatDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use Filament\Forms\Components\{
    Select,
    TextInput,
};
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class AlatDetailResource extends Resource
{
    protected static ?string $model = AlatDetail::class;
    protected static ?string $navigationGroup = 'Peminjaman Alat';
    protected static ?int $navigationSort = 1;



    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';





   public static function form(Form $form): Form
{
    return $form
        ->schema([
        //     Select::make('alat_id')
        //         ->label('Tipe/Merk Alat')
        //         ->relationship('alat', 'nama_alat')
        //         ->searchable()
        //         ->required()
        //         ->reactive(),

        //     Select::make('no_unit')
        //         ->label('Nomor Unit')
        //         ->options(function (callable $get) {
        //             $alatId = $get('alat_id');
        //             if (!$alatId) return [];
        //             $maxUnit = 10;
        //             $usedUnits = \App\Models\AlatDetail::where('alat_id', $alatId)
        //                 ->pluck('no_unit')
        //                 ->map(fn($v) => (int) $v)
                        
        //                 ->toArray();

        //             $available = [];
        //             for ($i = 1; $i <= $maxUnit; $i++) {
        //                 if (!in_array($i, $usedUnits)) {
        //                     $available[$i] = "Unit $i";
        //                 }
        //             }
        //             return $available;
        //         })
        //         ->required()
                
        //         ->reactive(),

        //     TextInput::make('tahun_alat')
        //         ->label('Tahun Alat')
        //         ->numeric()
        //         ->minValue(1980)
        //         ->maxValue(date('Y'))
        //         ->required(),

        //     TextInput::make('kode_alat')
        //         ->label('Kode Inventaris')
        //         ->default(function (callable $get) {
        //             $alatId = $get('alat_id');
        //             if (!$alatId) return null;
        //             $alat = \App\Models\Alat::find($alatId);
        //             if (!$alat) return null;
        //             return $alat->kode_alat;
        //         })
        //         ->disabled()
        //         ->dehydrated(false) 
        //         ->hint('Kode otomatis dari master')
        //         ,

        //     Select::make('kondisi_alat')
        //         ->label('Kondisi Alat')
        //         ->options([
        //             'Baik' => 'Baik',
        //             'Rusak Ringan' => 'Rusak Ringan',
        //             'Rusak Berat' => 'Rusak Berat',
        //             'Hilang' => 'Hilang',
        //         ])
                
        //         ->required(),

                
        //     TextArea::make('keterangan')
        //         ->label('Keterangan')
        //         ->rows(2)
        //         ->nullable(),
                
        ]);
}





    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('alat.nama_alat')->label('Nama Alat Induk')->searchable(),
                TextColumn::make('no_unit')->label('No. Unit')->searchable(),
                TextColumn::make('kode_alat')->label('Kode Unit')->searchable(),
                TextColumn::make('tahun_alat')->label('Tahun')->searchable(),
                TextColumn::make('kondisi_alat')->label('Kondisi')->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        return $record->peminjamans()->where('status_pinjam', 'dipinjam')->exists() ? 'Dipinjam' : 'Tersedia';
                    })
                    ->badge()
                    ->colors([
                        'success' => 'Tersedia',
                        'danger' => 'Dipinjam',
                    ]),
                ]);
            // ->filters([
            //     //
            // ])
    //         ->actions([
    //             ActionGroup::make([
    //                 EditAction::make(),
    //                 DeleteAction::make(),
    //             ])
    //                 ->label('Aksi')
    //                 ->icon('heroicon-m-ellipsis-vertical')
    //                 ->size(ActionSize::Small)
    //                 ->color('primary')
    //                 ->button(),
    //         ])
    //         ->actionsPosition(ActionsPosition::BeforeCells)
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }

    // public static function getRelations(): array
    // {
    //     return [
    //         //
    //     ];
    // }
            }
            public static function getEloquentQuery(): Builder
{
    $alatTerhapusIds = \App\Models\PenghapusanInventaris::where('jenis_inventaris', 'alat')->pluck('inventaris_id');
    return parent::getEloquentQuery()->whereNotIn('id', $alatTerhapusIds);
}
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlatDetails::route('/'),
            'create' => Pages\CreateAlatDetail::route('/create'),
            'edit' => Pages\EditAlatDetail::route('/{record}/edit'),
        ];
    }
}