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
    Schema::table('peminjamans', function (Blueprint $table) {
        $table->string('file_surat')->nullable()->after('status_pinjam');
    });
}

public function down(): void
{
    Schema::table('peminjamans', function (Blueprint $table) {
        $table->dropColumn('file_surat');
    });
}

};
