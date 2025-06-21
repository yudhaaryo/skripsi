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
        Schema::table('alat_details', function (Blueprint $table) {
             $table->date('tanggal_penghapusan')->nullable()->after('status');
        $table->string('alasan_penghapusan')->nullable()->after('tanggal_penghapusan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alat_details', function (Blueprint $table) {
             $table->dropColumn(['tanggal_penghapusan', 'alasan_penghapusan']);
        });
    }
};
