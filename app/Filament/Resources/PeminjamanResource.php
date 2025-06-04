<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\Pengembalian;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Actions\{Action, DeleteAction, EditAction, DeleteBulkAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\{
    DatePicker,
    Select,
    TextInput,
    FileUpload,
    Repeater
};
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Peminjaman Alat';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama_peminjam')->required(),
            TextInput::make('kelas_peminjam')->required(),
            TextInput::make('nis_peminjam')->required(),
            DatePicker::make('tanggal_pinjam')->required(),
            DatePicker::make('tanggal_kembali')->required(),
            Select::make('status_pinjam')
                ->options([
                    'menunggu' => 'Menunggu',
                    'dipinjam' => 'Dipinjam',
                    'dikembalikan' => 'Dikembalikan',
                    'ditolak' => 'Ditolak',
                ])
                ->default('menunggu')
                ->disabled(fn () => auth()->user()?->hasRole('siswa'))
                ->required(),

            Repeater::make('alats')
                ->label('Alat yang Dipinjam')
                ->schema([
                    Select::make('alat_id')
                        ->label('Nama Alat')
                        ->options(Alat::pluck('nama_alat', 'id'))
                        ->required()
                        ->reactive(),

                    TextInput::make('jumlah_pinjam')
                        ->numeric()
                        ->label('Jumlah')
                        ->required()
                        ->minValue(1)
                        ->helperText(function (callable $get) {
                            $alat = Alat::find($get('alat_id'));
                            if (!$alat) return null;

                            $dipinjam = Peminjaman::where('status_pinjam', 'dipinjam')
                                ->whereHas('alats', fn ($q) => $q->where('alat_id', $alat->id))
                                ->with(['alats' => fn ($q) => $q->where('alat_id', $alat->id)])
                                ->get();

                            $jumlahTerpakai = $dipinjam->sum(fn ($pinjam) =>
                                $pinjam->alats->first()?->pivot?->jumlah_pinjam ?? 0
                            );

                            $stokTersedia = $alat->jumlah_alat - $jumlahTerpakai;

                            return "Stok tersedia: {$stokTersedia}";
                        }),

                    TextInput::make('kondisi_peminjaman')
                        ->label('Kondisi Alat Saat Dipinjam')
                        ->placeholder('Misal: Rusak / Baik')
                        ->nullable(),
                ])
                ->columns(2)
                ->required()
                ->minItems(1),

            FileUpload::make('file_surat')
                ->label('Unggah Surat')
                ->directory('surat-peminjaman')
                ->acceptedFileTypes(['application/pdf', 'image/*'])
                ->visible(fn () => Auth::user()?->hasRole('siswa')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_peminjam')->searchable(),
                TextColumn::make('kelas_peminjam')->searchable(),
                TextColumn::make('nis_peminjam')->searchable(),
                TextColumn::make('tanggal_pinjam'),
                TextColumn::make('tanggal_kembali'),
                TextColumn::make('alats')
                    ->label('Kondisi Alat')
                    ->formatStateUsing(function ($record) {
                        return $record->alats->map(function ($alat) {
                            return $alat->nama_alat . ' (' . ($alat->pivot->kondisi_peminjaman ?? '-') . ')';
                        })->implode(', ');
                    })
                    ->wrap(),
                TextColumn::make('alats.nama_alat')->label('Nama Alat'),
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
                    ->formatStateUsing(fn ($state) => $state ? '📄 Lihat Surat' : '-')
                    ->url(fn ($record) => $record->file_surat ? Storage::url($record->file_surat) : null),
            ])
            ->actions([
                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['admin', 'guru']) &&
                        $record->status_pinjam === 'menunggu' &&
                        $record->file_surat !== null
                    )
                    ->requiresConfirmation()
                    ->action(function (Peminjaman $record) {
                        $record->update(['status_pinjam' => 'dipinjam']);

                        foreach ($record->alats as $alat) {
                            $alat->decrement('jumlah_alat', $alat->pivot->jumlah_pinjam);
                        }
                    }),

                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['admin', 'guru']) &&
                        $record->status_pinjam === 'menunggu'
                    )
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status_pinjam' => 'ditolak'])),

                Action::make('cetak_surat')
                    ->label('Cetak Surat')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->visible(fn ($record) =>
                        $record->status_pinjam === 'menunggu' &&
                        $record->user_id === auth()->user()->id
                    )
                    ->requiresConfirmation()
                    ->action(fn (Peminjaman $record) => redirect()->route('peminjaman.surat', ['id' => $record->id])),

                Action::make('upload_surat')
                    ->label('Upload Surat')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('gray')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['siswa', 'guru', 'admin']) &&
                        $record->status_pinjam === 'menunggu' &&
                        $record->user_id === auth()->user()->id
                    )
                    ->form([
                        FileUpload::make('file_surat')
                            ->label('Unggah Surat Bertandatangan')
                            ->directory('surat-peminjaman')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->required()
                    ])
                    ->action(function (array $data, Peminjaman $record) {
                        $record->update([
                            'file_surat' => $data['file_surat'],
                        ]);
                        Notification::make()
                            ->title('Surat berhasil diunggah')
                            ->success()
                            ->send();
                    }),

                Action::make('kembalikan')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['admin', 'guru']) &&
                        $record->status_pinjam === 'dipinjam'
                    )
                    ->form(fn (Peminjaman $record) => [
                        DatePicker::make('tanggal_pengembalian')
                            ->label('Tanggal Pengembalian')
                            ->required()
                            ->default(now()),

                        Repeater::make('kondisi_alat')
                            ->label('Kondisi Alat Saat Dikembalikan')
                            ->schema([
                                Select::make('alat_id')
                                    ->label('Nama Alat')
                                    ->options($record->alats->pluck('nama_alat', 'id'))
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('kondisi')
                                    ->label('Kondisi Pengembalian')
                                    ->required(),
                            ])
                            ->default(
                                $record->alats->map(fn ($alat) => [
                                    'alat_id' => $alat->id,
                                    'kondisi' => '',
                                ])->toArray()
                            )
                            ->columns(2)
                    ])
                    ->action(function (array $data, Peminjaman $record) {
                        $record->update(['status_pinjam' => 'dikembalikan']);

                        foreach ($record->alats as $alat) {
                            $alat->increment('jumlah_alat', $alat->pivot->jumlah_pinjam);
                        }

                        Pengembalian::create([
                            'peminjaman_id' => $record->id,
                            'tanggal_pengembalian' => $data['tanggal_pengembalian'],
                            'kondisi_pengembalian' => 'Tercatat per alat',
                        ]);

                        foreach ($data['kondisi_alat'] as $item) {
                            $record->alats()->updateExistingPivot($item['alat_id'], [
                                'kondisi_peminjaman' => $item['kondisi'],
                            ]);
                        }

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Pengembalian alat berhasil dicatat.')
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->visible(fn ($record) =>
                        Auth::user()->hasRole('admin') || $record->nama_peminjam === Auth::user()->name
                    ),

                DeleteAction::make()
                    ->visible(fn ($record) =>
                        Auth::user()->hasRole('admin') || $record->nama_peminjam === Auth::user()->name
                    ),
            ])
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
            'index' => Pages\ListPeminjamen::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }

    protected static function afterCreate(Form $form): void
    {
        $record = $form->getModelInstance();

        foreach ($form->getState()['alats'] as $alat) {
            $record->alats()->updateExistingPivot($alat['alat_id'], [
                'jumlah_pinjam' => $alat['jumlah_pinjam'],
                'kondisi_peminjaman' => $alat['kondisi_peminjaman'],
            ]);
        }
    }

    protected static function afterUpdate(Form $form): void
    {
        $record = $form->getModelInstance();

        foreach ($form->getState()['alats'] as $alat) {
            $record->alats()->updateExistingPivot($alat['alat_id'], [
                'jumlah_pinjam' => $alat['jumlah_pinjam'],
                'kondisi_peminjaman' => $alat['kondisi_peminjaman'],
            ]);
        }
    }
}
