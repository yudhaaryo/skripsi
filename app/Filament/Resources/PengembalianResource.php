<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengembalianResource\Pages;
use App\Models\Pengembalian;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Enums\ActionsPosition;

use App\Filament\Exports\PengembalianExporter;
use Filament\Forms\Components\Select;

class PengembalianResource extends Resource
{
    protected static ?string $model = Pengembalian::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Peminjaman Alat';

    protected static ?int $navigationSort = 3;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),

                TextColumn::make('peminjaman.user.name')->label('Nama Peminjam'),
                  TextColumn::make('peminjaman.alatDetails')
                    ->label('Nama Alat')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->peminjaman->alatDetails->map(function ($detail) {
                            return $detail->alat->nama_alat ?? '-' . ' - Unit ' . $detail->no_unit;
                        })->implode(', ');
                    })
                    ->wrap(),

                TextColumn::make('kondisi_pengembalian')
                    ->label('Kondisi Pengembalian')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->peminjaman->alatDetails->map(function ($detail) {
                            $namaAlat = $detail->alat->nama_alat ?? '-';
                            $unit = $detail->no_unit ?? '-';
                            $kondisi = $detail->pivot->kondisi_saat_kembali ?? '-';
                            return "{$namaAlat} (Unit {$unit}) [{$kondisi}]";
                        })->implode(', ');
                    })
                    ->wrap(),

                TextColumn::make('peminjaman.tanggal_pinjam')->label('Tanggal Peminjaman'),

                TextColumn::make('tanggal_pengembalian')->label('Tanggal Pengembalian'),

              

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
                BulkActionGroup::make([
                    // ExportBulkAction::make()
                    //     ->exporter(PengembalianExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // ExportAction::make()
                //     ->exporter(PengembalianExporter::class)
                //     ,
            ]);
    }



    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengembalians::route('/'),
            'create' => Pages\CreatePengembalian::route('/create'),
            'edit' => Pages\EditPengembalian::route('/{record}/edit'),
        ];
    }
}