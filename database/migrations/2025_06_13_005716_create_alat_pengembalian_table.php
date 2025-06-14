<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alat_pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_id')->constrained()->onDelete('cascade');
            $table->foreignId('pengembalian_id')->constrained()->onDelete('cascade');
            $table->string('kondisi_pengembalian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat_pengembalian');
    }
};