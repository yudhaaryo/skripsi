<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';

    protected $fillable = [
        'kode_barang',
        'nama_barang_asli',
        'nama_barang_aplikasi',
        'harga_beli',
        'jumlah_awal',
        'satuan',
        'tanggal_masuk',
    ];





    public function barangKeluars()
{
    return $this->hasMany(BarangKeluar::class, 'barang_id');
}
public function mutasiBarangs()
{
    return $this->hasMany(MutasiBarang::class);
}

}