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
            $masukSebelumBulanIni = BarangMasuk::where('barang_id', $barang->id)
                ->where('tanggal_masuk', '<', $tanggalAwalBulan)
                ->sum('jumlah_masuk');

            $keluarSebelumBulanIni = BarangKeluar::where('barang_id', $barang->id)
                ->where('tanggal_keluar', '<', $tanggalAwalBulan)
                ->sum('jumlah_keluar');

            $tambahBulanIni = BarangMasuk::where('barang_id', $barang->id)
                ->whereBetween('tanggal_masuk', [$tanggalAwalBulan, $tanggalAkhirBulan])
                ->sum('jumlah_masuk');

            $keluarBulanIni = BarangKeluar::where('barang_id', $barang->id)
                ->whereBetween('tanggal_keluar', [$tanggalAwalBulan, $tanggalAkhirBulan])
                ->sum('jumlah_keluar');

            $saldoAwal = $barang->jumlah_awal + $masukSebelumBulanIni - $keluarSebelumBulanIni;
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

        // Filter
        $filter = $request->input('filter_penggunaan', 'semua');
        if ($filter === 'digunakan') {
            $barangs = $barangs->filter(fn($b) => $b['digunakan'] > 0);
        }
        if ($filter === 'mutasi') {
            $barangs = $barangs->filter(fn($b) => $b['tambah'] != 0 || $b['digunakan'] != 0);
        }
        if ($filter === 'habis') {
            $barangs = $barangs->filter(fn($b) => $b['saldo_akhir'] == 0);
        }

        // Jika tidak ada data (misal ekspor tahun/bulan kosong), bisa handle di view excel
        if ($barangs->count() === 0) {
            // Bisa kasih pesan atau sheet kosong
        }

        return Excel::download(
            new LaporanBarangHabisExport(
                $barangs,
                'Nama Program',
                $carbonBulan->translatedFormat('F Y')
            ),
            'laporan_barang_' . $carbonBulan->format('Y_m') . '.xlsx'
        );
    }
}

