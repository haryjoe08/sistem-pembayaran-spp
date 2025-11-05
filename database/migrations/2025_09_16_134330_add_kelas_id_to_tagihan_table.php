<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            // Tambah kolom kelas_id
            $table->unsignedBigInteger('kelas_id')->after('siswa_nis');

            // Set foreign key ke tabel kelas
            $table->foreign('kelas_id')
                  ->references('id')
                  ->on('kelas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            // Hapus foreign key dan kolom
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
    }
};
