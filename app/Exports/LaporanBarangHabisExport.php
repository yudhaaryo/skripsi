<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanBarangHabisExport implements FromView
{
    public $barangs, $program, $bulan;

    public function __construct($barangs, $program, $bulan)
    {
        $this->barangs = $barangs;
        $this->program = $program;
        $this->bulan = $bulan;
    }

    public function view(): View
    {
        return view('exports.laporan-barang', [
            'barangs' => $this->barangs,
            'program' => $this->program,
            'bulan' => $this->bulan,
        ]);
    }
}
