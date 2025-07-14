<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class BarangImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows->shift(); // skip header

        foreach ($rows as $row) {
            $kodeBarang   = trim($row[0]);
            $namaBarang   = trim($row[1]);
            $namaAplikasi = trim($row[2]);
            $hargaBeli    = floatval($row[3]);
            $jumlahMasuk  = intval($row[4]);
            $satuan       = trim($row[5]);
            $tanggalMasuk = $row[6] ?? date('Y-m-d');
            $keterangan   = $row[7] ?? '-';

            // Konversi tanggal Excel serial number ke format Y-m-d jika perlu
            if (is_numeric($tanggalMasuk)) {
                $tanggalMasuk = $this->excelDateToDate($tanggalMasuk);
            }

            $barang = Barang::where('kode_barang', $kodeBarang)->first();

            if ($barang) {
                // Barang sudah ada: Tambah catatan masuk + update jumlah_awal
                BarangMasuk::create([
                    'barang_id'     => $barang->id,
                    'jumlah_masuk'  => $jumlahMasuk,
                    'tanggal_masuk' => $tanggalMasuk,
                    'keterangan'    => $keterangan,
                ]);

                $barang->jumlah_awal += $jumlahMasuk;
                $barang->save();
            } else {
                // Barang baru: Hanya tambah di master barang (stok awal)
                Barang::create([
                    'kode_barang'           => $kodeBarang,
                    'nama_barang_asli'      => $namaBarang,
                    'nama_barang_aplikasi'  => $namaAplikasi,
                    'harga_beli'            => $hargaBeli,
                    'jumlah_awal'           => $jumlahMasuk,
                    'satuan'                => $satuan,
                    'tanggal_masuk'         => $tanggalMasuk,
                ]);
            }
        }
    }

    // Tambahkan fungsi konversi excel serial date ke format Y-m-d
    public function excelDateToDate($excelDate)
    {
        // Fungsi ini mengkonversi serial date Excel ke tanggal standar
        return date('Y-m-d', ($excelDate - 25569) * 86400);
    }
}