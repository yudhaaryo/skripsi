<?php

namespace App\Http\Controllers;

use App\Exports\LaporanBarangHabisExport;
use App\Models\Barang;
use App\Models\BarangKeluar;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LaporanBarangController extends Controller
{
    public function export(Request $request)
{
    $bulan = $request->input('bulan');
    $carbonBulan = Carbon::createFromFormat('Y-m', $bulan);
    $month = $carbonBulan->month;
    $year = $carbonBulan->year;

    $tanggalAwalBulan = Carbon::create($year, $month, 1);
    $tanggalAkhirBulan = $tanggalAwalBulan->copy()->endOfMonth();

    $barangs = Barang::all()->map(function ($barang) use ($tanggalAwalBulan, $tanggalAkhirBulan) {
        // Jumlah keluar sebelum bulan ini
        $keluarSebelumBulanIni = BarangKeluar::where('barang_id', $barang->id)
            ->where('tanggal_keluar', '<', $tanggalAwalBulan)
            ->sum('jumlah_keluar');

        // Jumlah keluar di bulan ini
        $keluarBulanIni = BarangKeluar::where('barang_id', $barang->id)
            ->whereBetween('tanggal_keluar', [$tanggalAwalBulan, $tanggalAkhirBulan])
            ->sum('jumlah_keluar');

        // Saldo awal adalah jumlah_awal - total keluar sebelum bulan ini
        $saldoAwal = $barang->jumlah_awal - $keluarSebelumBulanIni;

        // Saldo akhir adalah saldo awal - jumlah keluar bulan ini
        $saldoAkhir = $saldoAwal - $keluarBulanIni;

        return [
            'kode_barang'   => $barang->kode_barang,
            'nama_asli'     => $barang->nama_barang_asli,
            'nama_aplikasi' => $barang->nama_barang_aplikasi,
            'harga_beli'    => $barang->harga_beli,
            'jumlah_awal'   => $barang->jumlah_awal,
            'saldo_awal'    => $saldoAwal,
            'tambah'        => $barang->tambah ?? 0,
            'satuan'        => $barang->satuan,
            'digunakan'     => $keluarBulanIni,
            'saldo_akhir'   => $saldoAkhir,
            'jumlah_rupiah' => $saldoAkhir * $barang->harga_beli,
        ];
    });
    $filter = $request->input('filter_penggunaan', 'semua');

    if ($filter === 'digunakan') {
        $barangs = $barangs->filter(fn($b) => $b['digunakan'] > 0);
    }

    return Excel::download(
        new LaporanBarangHabisExport($barangs, 'TKJ', $carbonBulan->translatedFormat('F Y')),
        'laporan_barang_' . $carbonBulan->format('Y_m') . '.xlsx'
    );
}
}
