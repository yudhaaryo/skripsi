<?php

namespace App\Filament\Resources\AlatResource\Pages;

use App\Filament\Resources\AlatResource; 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Repeater;
use Filament\Forms;
use App\Models\Alat;
use Filament\Notifications\Notification;

class TambahUnitAlat extends Page
{
    protected static string $resource = AlatResource::class;
    protected static string $view = 'filament.resources.alat-resource.pages.tambah-unit-alat';

    public $alat_id;
    public $units = [];

    public function mount()
    {
        $this->alat_id = null;
        $this->units = [];
    }

    protected function getFormSchema(): array
{
    return [
        Select::make('alat_id')
            ->label('Pilih Alat')
            ->options(Alat::all()->pluck('nama_alat', 'id'))
            ->searchable()
            ->required(),

        Repeater::make('units')
            ->label('Tambah Unit/Detail')
            ->schema([
                Select::make('no_unit')
                    ->label('No. Unit')
                    ->options(function (callable $get) {
                        $alatId = $get('../../alat_id'); // ambil alat_id dari root
                        if (!$alatId) return [];

                        $maxUnit = 10;
                        $usedUnits = \App\Models\AlatDetail::where('alat_id', $alatId)
                            ->pluck('no_unit')
                            ->map(fn($v) => (int) $v)
                            ->toArray();

                        // Tambahkan no_unit yang sedang diinput agar tidak duplikat
                        $currentUnits = collect($get('../*.no_unit'))
                            ->filter()
                            ->map(fn($v) => (int) $v)
                            ->toArray();

                        $usedUnits = array_merge($usedUnits, $currentUnits);

                        $available = [];
                        for ($i = 1; $i <= $maxUnit; $i++) {
                            if (!in_array($i, $usedUnits)) {
                                $available[$i] = "Unit $i";
                            }
                        }

                        return $available;
                    })
                    ->required()
                    ->reactive(),

                TextInput::make('tahun_alat')
                    ->label('Tahun Alat')
                    ->numeric()
                    ->required(),

                TextInput::make('kode_alat')
                    ->label('Kode Alat')
                    ->required(),

                Select::make('kondisi_alat')
                    ->label('Kondisi')
                    ->options([
                        'Baik' => 'Baik',
                        'Rusak Ringan' => 'Rusak Ringan',
                        'Rusak Berat' => 'Rusak Berat',
                        'Hilang' => 'Hilang',
                    ])
                    ->required(),

                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->nullable(),
            ])
            ->minItems(1)
            ->columns(2),
    ];
}


   public function submit()
{
    $alat = Alat::find($this->alat_id);
    if (!$alat) {
        Notification::make()->title('Gagal')->body('Alat tidak ditemukan!')->danger()->send();
        return;
    }

    // Ambil no_unit yang sudah ada
    $usedUnits = $alat->details()->pluck('no_unit')->toArray();
    $inputUnits = array_column($this->units, 'no_unit');

    // Cek duplikat antar input
    if (count($inputUnits) !== count(array_unique($inputUnits))) {
        Notification::make()
            ->title('Gagal')
            ->body('Nomor Unit yang diinput ada yang sama/duplikat.')
            ->danger()
            ->send();
        return;
    }

    // Cek apakah ada yang sudah dipakai di alat tsb
    foreach ($inputUnits as $unit) {
        if (in_array($unit, $usedUnits)) {
            Notification::make()
                ->title('Gagal')
                ->body("Nomor Unit {$unit} sudah digunakan pada alat ini!")
                ->danger()
                ->send();
            return;
        }
    }

    foreach ($this->units as $unit) {
        $alat->details()->create($unit);
    }

    $alat->jumlah_alat = $alat->details()->count();
    $alat->save();

    Notification::make()
        ->title('Sukses')
        ->body('Unit/detail baru berhasil ditambahkan ke alat!')
        ->success()
        ->send();

    return redirect(route('filament.admin.resources.alats.index'));
}

}
