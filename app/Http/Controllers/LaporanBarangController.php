<?php

namespace App\Http\Controllers;

use App\Exports\LaporanBarangHabisExport;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LaporanBarangController extends Controller
{
    public function export(Request $request)
    {
        $bulan = $request->input('bulan'); // format 'Y-m'
        if (!$bulan) {
            abort(400, 'Parameter bulan wajib diisi.');
        }

        $carbonBulan = Carbon::createFromFormat('Y-m', $bulan);
        $month = $carbonBulan->month;
        $year = $carbonBulan->year;

        $tanggalAwalBulan = Carbon::create($year, $month, 1);
        $tanggalAkhirBulan = $tanggalAwalBulan->copy()->endOfMonth();

        $barangs = Barang::all()->map(function ($barang) use ($tanggalAwalBulan, $tanggalAkhirBulan) {
            // Barang MASUK sebelum bulan ini
            $masukSebelumBulanIni = BarangMasuk::where('barang_id', $barang->id)
                ->where('tanggal_masuk', '<', $tanggalAwalBulan)
                ->sum('jumlah_masuk');

            // Barang KELUAR sebelum bulan ini
            $keluarSebelumBulanIni = BarangKeluar::where('barang_id', $barang->id)
                ->where('tanggal_keluar', '<', $tanggalAwalBulan)
                ->sum('jumlah_keluar');

            // Barang MASUK bulan ini
            $tambahBulanIni = BarangMasuk::where('barang_id', $barang->id)
                ->whereBetween('tanggal_masuk', [$tanggalAwalBulan, $tanggalAkhirBulan])
                ->sum('jumlah_masuk');

            // Barang KELUAR bulan ini
            $keluarBulanIni = BarangKeluar::where('barang_id', $barang->id)
                ->whereBetween('tanggal_keluar', [$tanggalAwalBulan, $tanggalAkhirBulan])
                ->sum('jumlah_keluar');

            // Hitung SALDO AWAL: stok saat awal bulan laporan
            $saldoAwal = $barang->jumlah_awal + $masukSebelumBulanIni - $keluarSebelumBulanIni;

            // Hitung SALDO AKHIR: stok akhir bulan laporan
            $saldoAkhir = $saldoAwal + $tambahBulanIni - $keluarBulanIni;

            return [
                'kode_barang'   => $barang->kode_barang,
                'nama_asli'     => $barang->nama_barang_asli,
                'nama_aplikasi' => $barang->nama_barang_aplikasi,
                'harga_beli'    => $barang->harga_beli,
                'jumlah_awal'   => $barang->jumlah_awal,
                'saldo_awal'    => $saldoAwal,
                'tambah'        => $tambahBulanIni,
                'satuan'        => $barang->satuan,
                'digunakan'     => $keluarBulanIni,
                'saldo_akhir'   => $saldoAkhir,
                'jumlah_rupiah' => $saldoAkhir * $barang->harga_beli,
            ];
        });

        // Filter, jika ingin menampilkan hanya yang dipakai atau habis
        $filter = $request->input('filter_penggunaan', 'semua');
        if ($filter === 'digunakan') {
            $barangs = $barangs->filter(fn($b) => $b['digunakan'] > 0);
        }
        if ($filter === 'mutasi') {
    $barangs = $barangs->filter(fn($b) => $b['tambah'] != 0 || $b['digunakan'] != 0);
}
        if ($filter === 'habis') {
            // Tampilkan hanya barang yang saldo akhirnya 0
            $barangs = $barangs->filter(fn($b) => $b['saldo_akhir'] == 0);
        }

        // Jika tahun/bulan sebelum ada data, tetap akan kosong (tidak error)
        if ($barangs->count() === 0) {
            // Jika ingin, bisa kirim view kosong atau handle sesuai kebutuhan
        }

        // Export Excel dengan data laporan dan judul bulan
        return Excel::download(
            new LaporanBarangHabisExport(
                $barangs,
                'Nama Program', // Bisa diganti sesuai input
                $carbonBulan->translatedFormat('F Y')
            ),
            'laporan_barang_' . $carbonBulan->format('Y_m') . '.xlsx'
        );
    }
}
