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
    Schema::create('alats', function (Blueprint $table) {
        $table->id();
        $table->string('nama_alat');
        $table->string('kode_alat');
        $table->integer('jumlah_alat');
        $table->string('kondisi_alat');
        $table->string('merk_alat')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alats');
    }
};