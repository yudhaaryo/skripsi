<?php

namespace App\Filament\Resources\BarangMasukResource\Pages;

use App\Filament\Resources\BarangMasukResource;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Models\Barang;
use App\Models\BarangMasuk;

class CreateBarangBaruMasuk extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = BarangMasukResource::class;

    // Nama view blade kamu, pastikan file blade-nya ada di lokasi ini
    protected static string $view = 'filament.resources.barang-masuk-resource.pages.create-barang-baru-masuk';

    public $kode_barang;
    public $nama_barang_asli;
    public $nama_barang_aplikasi;
    public $harga_beli;
    public $jumlah_awal;
    public $satuan;
    public $tanggal_masuk;

    public function getTitle(): string
    {
        return 'Tambah Barang Baru';
    }

    public function mount(): void
    {
        // Set default tanggal masuk saat form tampil
        $this->form->fill([
            'tanggal_masuk' => now(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('kode_barang')->label('Kode Barang')->required(),
            TextInput::make('nama_barang_asli')->label('Nama Asli')->required(),
            TextInput::make('nama_barang_aplikasi')->label('Nama di Aplikasi')->required(),
            TextInput::make('harga_beli')->label('Harga Beli')->numeric()->required(),
            TextInput::make('jumlah_awal')->label('Jumlah Awal')->numeric()->required(),
            TextInput::make('satuan')->label('Satuan')->required(),
            DatePicker::make('tanggal_masuk')->label('Tanggal Masuk')->required()->default(now()),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();

        // Filter tanggal_masuk agar format selalu aman
        $tanggalMasuk = $data['tanggal_masuk'];
        if (is_array($tanggalMasuk)) {
            $tanggalMasuk = $tanggalMasuk[0] ?? now()->toDateString();
        }
        if (is_string($tanggalMasuk) && str_contains($tanggalMasuk, ',')) {
            $tanggalMasuk = trim(explode(',', $tanggalMasuk)[0]);
        }

        // Simpan data barang baru tanpa stok awal di tabel barang_masuk
        $barang = Barang::create([
            'kode_barang' => $data['kode_barang'],
            'nama_barang_asli' => $data['nama_barang_asli'],
            'nama_barang_aplikasi' => $data['nama_barang_aplikasi'],
            'harga_beli' => $data['harga_beli'],
            'jumlah_awal' => $data['jumlah_awal'],
            'satuan' => $data['satuan'],
            'tanggal_masuk' => $tanggalMasuk,
        ]);
        // BarangMasuk::create([
        //     'barang_id' => $barang->id,
        //     'jumlah_masuk' => $data['jumlah_awal'],
        //     'tanggal_masuk' => $tanggalMasuk,
        // ]);

        // Berikan notifikasi sukses ke user
        session()->flash('success', 'Barang baru berhasil ditambahkan! Silakan tambah stok lewat menu Barang Masuk.');

        // Redirect ke halaman index resource barang masuk
        return redirect()->route('filament.admin.resources.barang-masuks.index');
    }
}
