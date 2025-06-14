<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_alat',
        'kode_alat',
        'jumlah_alat',
        'kondisi_alat',
        'merk_alat',
        'sumber_dana',
        'tahun_alat',
    ];

    public function peminjamans()
    {
        return $this->belongsToMany(Peminjaman::class, 'alat_peminjaman')->withPivot('jumlah_pinjam', 'kondisi_peminjaman')->withTimestamps();
    }
    public function details()
{
    return $this->hasMany(AlatDetail::class, 'alat_id');
}

public function stokTersedia()
{
    return $this->details()->whereDoesntHave('peminjamans', function ($q) {
        $q->where('status_pinjam', 'dipinjam');
    })->count();
}

}
