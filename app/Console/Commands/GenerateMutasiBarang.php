<?php

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\MutasiBarang;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateMutasiBarang extends Command
{
    protected $signature = 'generate:mutasi-barang {bulan}';
    protected $description = 'Generate data mutasi barang habis pakai per bulan';

    public function handle()
    {
        $bulan = $this->argument('bulan');
        $carbon = Carbon::createFromFormat('Y-m', $bulan);
        $month = $carbon->month;
        $year = $carbon->year;

        $barangs = Barang::all();

        foreach ($barangs as $barang) {
            $digunakan = BarangKeluar::where('barang_id', $barang->id)
                ->whereMonth('tanggal_keluar', $month)
                ->whereYear('tanggal_keluar', $year)
                ->sum('jumlah_keluar');

            MutasiBarang::updateOrCreate(
                ['barang_id' => $barang->id, 'bulan' => $bulan],
                [
                    'saldo_awal' => $barang->jumlah_awal,
                    'tambah' => 0,
                    'digunakan' => $digunakan,
                    'saldo_akhir' => $barang->jumlah_awal - $digunakan,
                ]
            );
        }

        $this->info("Mutasi bulan $bulan berhasil digenerate.");
    }
}