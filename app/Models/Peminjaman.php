<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    protected $fillable = [
        'tanggal_pinjam',
        'tanggal_kembali',
        'nis_peminjam',
        'kelas_peminjam',
        'keperluan',
        'status_pinjam',
        'file_surat',
        'surat_dicetak',
        'user_id',
    ];


    public function alatDetails()
    {
        return $this->belongsToMany(AlatDetail::class, 'peminjaman_alat_detail')
            ->withPivot(['kondisi_saat_pinjam', 'kondisi_saat_kembali', 'keterangan'])
            ->withTimestamps();
    }

    public function pengembalians()
    {
        return $this->hasMany(Pengembalian::class, 'peminjaman_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
