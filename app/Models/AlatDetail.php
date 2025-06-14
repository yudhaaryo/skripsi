<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlatDetail extends Model
{
    protected $fillable = [
        'alat_id',
        'no_unit',
        'kode_alat',
        'tahun_alat',
        'kondisi_alat',
        'keterangan',
    ];

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }


    public function peminjamans()
    {
        return $this->belongsToMany(Peminjaman::class, 'peminjaman_alat_detail')
            ->withPivot(['kondisi_saat_pinjam', 'kondisi_saat_kembali', 'keterangan'])
            ->withTimestamps();
    }
}
