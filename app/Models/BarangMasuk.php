<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
   protected $fillable = [
    'nama_barang_aplikasi',
        'barang_id',
        'jumlah_masuk',
        'tanggal_masuk',
    ];
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

}
