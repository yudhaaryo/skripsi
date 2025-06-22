<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Models\AlatDetail;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Filament\Table\Actions\ActionGroup;
use Filament\Forms\Form;
use Filament\Support\Enums\ActionSize;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Forms\Components\{
    Select,
    DatePicker,
    TextInput,
    FileUpload,
    Repeater,
    Hidden
};
use Filament\Tables\Actions\{
    Action,
    DeleteAction,
    EditAction,
    DeleteBulkAction
};
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Peminjaman Alat';
    protected static ?string $navigationLabel = 'Peminjaman';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form->schema([


            TextInput::make('kelas_peminjam')->required(),
            TextInput::make('nis_peminjam')->required(),
            DatePicker::make('tanggal_pinjam')
                ->required()
                ->minDate(today()), 
            DatePicker::make('tanggal_kembali')
                ->required()
                ->minDate(today()), 


            TextInput::make('keperluan')
                ->label('Keperluan Peminjaman')
                ->required(),

            Select::make('status_pinjam')
                ->options([
                    'menunggu' => 'Menunggu',
                    'dipinjam' => 'Dipinjam',
                    'dikembalikan' => 'Dikembalikan',
                    'ditolak' => 'Ditolak',
                ])
                ->default('menunggu')
                ->disabled(fn() => auth()->user()?->hasRole('siswa'))
                ->hidden(fn() => auth()->user()?->hasRole('siswa'))
                ->required(),



            FileUpload::make('file_surat')
                ->label('Unggah Surat')
                ->directory('surat-peminjaman')
                ->acceptedFileTypes(['application/pdf', 'image/*'])
                ->visible(fn() => Auth::user()?->hasRole('siswa', 'guru')),
            Repeater::make('alats')
                ->label('Pilih Unit Alat')
                ->columnSpan(2)
                ->schema([
                    Select::make('alat_detail_id')
                        ->label('Unit Alat')
                        ->options(
                            AlatDetail::whereDoesntHave('peminjamans', function ($q) {
                                $q->where('status_pinjam', 'dipinjam');
                            })
                                ->with('alat')
                                ->get()
                                ->mapWithKeys(function ($detail) {
                                    return [
                                        $detail->id => $detail->alat->nama_alat . ' - Unit ' . $detail->no_unit . ' (Kondisi: ' . $detail->kondisi_alat . ')'
                                    ];
                                })
                        )
                        ->searchable()
                        ->required(),

                    TextInput::make('kondisi_saat_pinjam')
                        ->label('Kondisi Saat Dipinjam')
                        ->default(function (callable $get) {
                            $id = $get('alat_detail_id');
                            $unit = $id ? AlatDetail::find($id) : null;
                            return $unit?->kondisi_alat ?? '';
                        })
                        ->required(),

                    TextInput::make('keterangan')
                        ->label('Keterangan')
                        ->nullable(),
                ])
                ->columns(2)
                ->minItems(1)
                ->required(),
        ]);

    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Peminjam')
                    ->searchable(),
                TextColumn::make('kelas_peminjam')
                    ->searchable(),
                TextColumn::make('nis_peminjam')
                    ->searchable(),
                TextColumn::make('keperluan')
                    ->label('Keperluan')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('tanggal_pinjam'),
                TextColumn::make('tanggal_kembali'),

                TextColumn::make('alat_dipinjam')
                    ->label('Alat Dipinjam')
                    ->getStateUsing(function ($record) {
                        $details = $record->alatDetails;
                        $names = $details->take(2)->map(fn($d) => "{$d->alat->nama_alat} - {$d->no_unit}")->implode(', ');
                        $count = $details->count();
                        return $count > 2 ? "$names, +" . ($count - 2) . " lainnya" : $names;
                    }),


                TextColumn::make('status_pinjam')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'gray' => 'menunggu',
                        'success' => 'dipinjam',
                        'danger' => 'ditolak',
                        'warning' => 'dikembalikan',
                    ])
                    ->sortable(),

                TextColumn::make('file_surat')
                    ->label('Surat')
                    ->formatStateUsing(fn($state) => $state ? '📄 Lihat Surat' : '-')
                    ->url(fn($record) => $record->file_surat ? \Storage::url($record->file_surat) : null),
            ])
            ->filters([
                Filter::make('terlambat')
                    ->label('Terlambat Mengembalikan')
                    ->query(
                        fn($query) => $query
                            ->where('status_pinjam', 'dipinjam')
                            ->whereDate('tanggal_kembali', '<', now())
                    ),
                Filter::make('menunggu')
                    ->label('Menunggu Persetujuan')
                    ->query(
                        fn($query) => $query
                            ->where('status_pinjam', 'menunggu')
                    ),
                Filter::make('dipinjam')
                    ->label('Sedang Dipinjam')
                    ->query(
                        fn($query) => $query
                            ->where('status_pinjam', 'dipinjam')
                            ->whereDate('tanggal_kembali', '>=', now())
                    ),
                Filter::make('dikembalikan')
                    ->label('Sudah Dikembalikan')
                    ->query(
                        fn($query) => $query
                            ->where('status_pinjam', 'dikembalikan')
                    ),
                Filter::make('ditolak')
                    ->label('Ditolak')
                    ->query(
                        fn($query) => $query
                            ->where('status_pinjam', 'ditolak')
                    ),
            ])
            ->actions([
                \Filament\Tables\Actions\ActionGroup::make([
                    Action::make('setujui')
                        ->label('Setujui')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn($record) => auth()->user()?->can('setujui', $record))
                        ->requiresConfirmation()
                        ->action(fn(Peminjaman $record) => $record->update(['status_pinjam' => 'dipinjam'])),

                    Action::make('tolak')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn($record) => auth()->user()?->can('tolak', $record))
                        ->requiresConfirmation()
                        ->action(fn(Peminjaman $record) => $record->update(['status_pinjam' => 'ditolak'])),

                    Action::make('cetak_surat')
                        ->label('Cetak Surat')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->visible(
                            fn($record) =>
                            $record->status_pinjam === 'menunggu' &&
                            $record->user_id === auth()->user()->id
                        )
                        ->requiresConfirmation()
                        ->action(fn(Peminjaman $record) => redirect()->route('peminjaman.surat', ['id' => $record->id])),

                    Action::make('upload_surat')
                        ->label('Upload Surat')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('gray')
                        ->visible(fn($record) => $record->status_pinjam === 'menunggu')
                        ->form([
                            FileUpload::make('file_surat')
                                ->label('Unggah Surat Bertandatangan')
                                ->directory('surat-peminjaman')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->maxSize(10240)
                                ->required(),
                        ])
                        ->action(function (array $data, Peminjaman $record) {
                            $record->update(['file_surat' => $data['file_surat']]);
                            Notification::make()
                                ->title('Surat berhasil diunggah')
                                ->success()
                                ->send();
                        }),

                    Action::make('kembalikan')
                        ->label('Kembalikan')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->visible(fn($record) => auth()->user()?->can('kembalikan', $record))
                        ->form(fn(Peminjaman $record) => [
                            DatePicker::make('tanggal_pengembalian')
                                ->label('Tanggal Pengembalian')
                                ->required()
                                ->default(now()),

                            Repeater::make('kondisi_alat')
                                ->label('Kondisi Unit Saat Dikembalikan')
                                ->schema([
                                    Select::make('alat_detail_id')
                                        ->label('Unit Alat')
                                        ->options($record->alatDetails->mapWithKeys(
                                            fn($detail) => [
                                                $detail->id => $detail->alat->nama_alat
                                                    . ' - Unit ' . $detail->no_unit
                                                    . ' (' . $detail->kode_alat . ')'
                                            ]
                                        ))
                                        ->disabled()
                                        ->dehydrated(),

                                    TextInput::make('kondisi')
                                        ->label('Kondisi Pengembalian')
                                        ->required(),
                                ])
                                ->default(
                                    $record->alatDetails->map(fn($detail) => [
                                        'alat_detail_id' => $detail->id,
                                        'kondisi' => '',
                                    ])->toArray()
                                )
                                ->columns(2)
                                ->disableItemCreation()
                        ])
                        ->action(function (array $data, Peminjaman $record) {
                            $record->update(['status_pinjam' => 'dikembalikan']);

                            Pengembalian::create([
                                'peminjaman_id' => $record->id,
                                'tanggal_pengembalian' => $data['tanggal_pengembalian'],
                                'kondisi_pengembalian' => 'Tercatat per alat',
                            ]);

                            foreach ($data['kondisi_alat'] as $item) {
                                $record->alatDetails()->updateExistingPivot($item['alat_detail_id'], [
                                    'kondisi_saat_kembali' => $item['kondisi'],
                                ]);
                            }

                            Notification::make()
                                ->title('Berhasil')
                                ->body('Pengembalian unit alat berhasil dicatat.')
                                ->success()
                                ->send();
                        }),
                    Action::make('lihat_detail')
                        ->label('Detail')
                        ->icon('heroicon-o-eye')
                        ->url(fn($record) => route('filament.admin.resources.peminjamen.view', $record)),




                    EditAction::make()
                        ->visible(
                            fn($record) =>
                            $record->user_id === Auth::id() ||
                            auth()->user()?->hasRole('admin')
                        ),

                    DeleteAction::make()
                        ->visible(
                            fn($record) =>
                            $record->user_id === Auth::id() ||
                            auth()->user()?->hasRole('admin')
                        ),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeminjamen::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
            'view' => Pages\ViewPeminjaman::route('/{record}'),
        ];
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
{
    $query = parent::getEloquentQuery();

    if (auth()->user()?->hasRole('siswa')) {
        $query->where('user_id', auth()->id());
    }

    return $query;
}

}