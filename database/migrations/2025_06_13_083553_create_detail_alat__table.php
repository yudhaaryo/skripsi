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
        Schema::create('alat_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alat_id');
            $table->integer('no_unit');
            $table->string('kondisi_alat');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('alat_id')->references('id')->on('alats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_alat');
    }
};