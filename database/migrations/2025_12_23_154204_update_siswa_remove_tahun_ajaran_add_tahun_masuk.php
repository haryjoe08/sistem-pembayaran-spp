<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            // hapus FK dulu kalau ada
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');

            // tambah tahun masuk
            $table->year('tahun_masuk')->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran');
            $table->dropColumn('tahun_masuk');
        });
    }
};
