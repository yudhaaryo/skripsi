<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    protected $fillable = [
        'barang_id',
        'bulan',
        'saldo_awal',
        'tambah',
        'digunakan',
        'saldo_akhir',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}