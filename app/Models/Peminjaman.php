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
        'nama_peminjam',
        'nis_peminjam',
        'kelas_peminjam',
        'status_pinjam',
        'file_surat',
        'surat_dicetak',
        'user_id',
    ];

    public function alats()
    {
        return $this->belongsToMany(Alat::class, 'alat_peminjaman')
            ->withPivot(['jumlah_pinjam', 'kondisi_peminjaman'])
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