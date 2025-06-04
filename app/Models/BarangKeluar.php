<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'jumlah_keluar',
        'tujuan',
        'tanggal_keluar',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
