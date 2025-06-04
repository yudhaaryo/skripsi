<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalians'; // sesuaikan kalau nama tabel lain

    protected $fillable = [
        'peminjaman_id',
        'tanggal_pengembalian',
        'kondisi_pengembalian',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }
}
