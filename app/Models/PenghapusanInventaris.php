<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenghapusanInventaris extends Model
{
    use HasFactory;

    protected $table = 'penghapusan_inventaris';

    protected $fillable = [
        'jenis_inventaris',
        'inventaris_id',
        'alasan_penghapusan',
        'tanggal_penghapusan',
        'keterangan',
        'user_id',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
