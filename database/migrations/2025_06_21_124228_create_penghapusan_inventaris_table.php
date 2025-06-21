<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penghapusan_inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_inventaris'); 
            $table->unsignedBigInteger('inventaris_id'); 
            $table->string('alasan_penghapusan')->nullable();
            $table->date('tanggal_penghapusan')->nullable();
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('user_id'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penghapusan_inventaris');
    }
};
