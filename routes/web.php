<?php

use App\Exports\LaporanBarangHabisExport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratPeminjamanController;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\LaporanBarangController;

Route::get('/peminjaman/surat/{id}', [SuratPeminjamanController::class, 'show'])->name('peminjaman.surat');
Route::get('/export-barang', [LaporanBarangController::class, 'export'])->name('export-barang');


Route::get('/', function () {
    return view('landing');
});
