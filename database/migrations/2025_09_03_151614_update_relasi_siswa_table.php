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
        Schema::table('siswa', function (Blueprint $table) {
            // hapus field lama (string jurusan & kelas)
            if (Schema::hasColumn('siswa', 'jurusan')) {
                $table->dropColumn('jurusan');
            }
            if (Schema::hasColumn('siswa', 'kelas')) {
                $table->dropColumn('kelas');
            }

            // tambahkan field baru (relasi foreign key)
            $table->unsignedBigInteger('jurusan_id')->nullable()->after('nis');
            $table->unsignedBigInteger('kelas_id')->nullable()->after('jurusan_id');
            $table->unsignedBigInteger('tahun_ajaran_id')->nullable()->after('kelas_id');

            // definisi foreign key
            $table->foreign('jurusan_id')->references('id')->on('jurusan')->onDelete('set null');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('set null');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            // rollback: hapus foreign key & kolom baru
            $table->dropForeign(['jurusan_id']);
            $table->dropForeign(['kelas_id']);
            $table->dropForeign(['tahun_ajaran_id']);

            $table->dropColumn(['jurusan_id', 'kelas_id', 'tahun_ajaran_id']);

            // kembalikan field lama
            $table->string('jurusan')->nullable();
            $table->string('kelas')->nullable();
        });
    }
};
