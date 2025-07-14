<?php

namespace App\Imports;

use App\Models\Alat;
use App\Models\AlatDetail;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class AlatImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows->shift();

        // Untuk no_unit per alat induk
        $counters = [];

        foreach ($rows as $row) {
            $kodeKategori = trim($row[0]);
            $namaAlat = trim($row[1]);
            $merk = trim($row[2]);
            $sumberDana = trim($row[3]);
            $kodeUnit = trim($row[4]);
            $tahun = trim($row[5]);
            $kondisi = trim($row[6]);
            $keterangan = trim($row[7]);

            // GROUPING: kode kategori + nama alat
            $alat = Alat::firstOrCreate(
                [
                    'kode_alat' => $kodeKategori,
                    'nama_alat' => $namaAlat,
                ],
                [
                    'merk_alat' => $merk,
                    'sumber_dana' => $sumberDana,
                ]
            );

            // Penomoran unit otomatis per alat induk
            if (!isset($counters[$alat->id])) {
                $counters[$alat->id] = 1;
            } else {
                $counters[$alat->id]++;
            }
            $noUnit = $counters[$alat->id];

            // Tambah alat detail/unit
            AlatDetail::create([
                'alat_id' => $alat->id,
                'no_unit' => $noUnit,
                'kode_alat' => $kodeUnit,
                'tahun_alat' => $tahun,
                'kondisi_alat' => $kondisi,
                'keterangan' => $keterangan,
            ]);

            // Update jumlah unit di alat induk
            $alat->jumlah_alat = $alat->details()->count();
            $alat->save();
        }
    }
}
