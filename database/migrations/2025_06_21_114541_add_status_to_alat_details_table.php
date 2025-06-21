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
            $table->string('status')->default('aktif')->after('kondisi_alat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alat_details', function (Blueprint $table) {
           $table->dropColumn('status');
        });
    }
};
