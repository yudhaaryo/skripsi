<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratPeminjamanController extends Controller
{
    public function show($id)
    {
        $peminjaman = Peminjaman::with('alatDetails.alat')->findOrFail($id);

        $pdf = Pdf::loadView('surat.peminjaman', compact('peminjaman'));

        return $pdf->stream('Surat-Peminjaman-' . $peminjaman->id . '.pdf');
    }
}
