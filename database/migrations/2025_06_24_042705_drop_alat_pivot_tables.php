<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::dropIfExists('alat_pengembalian');
    Schema::dropIfExists('alat_peminjaman');
}
public function down()
{
    // Kalau ingin bisa rollback, bisa recreate tabelnya di sini (opsional)
}

   
};
