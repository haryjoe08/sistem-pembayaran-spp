<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        // 1. Normalisasi data: ubah 'pindah' → 'keluar' (atau 'tidak_aktif')
        DB::table('siswa')
            ->where('status', 'pindah')
            ->update(['status' => 'keluar']);

        // 2. Redefinisi ENUM tanpa 'pindah'
        DB::statement("
            ALTER TABLE siswa
            MODIFY status
            ENUM('aktif', 'tidak_aktif', 'lulus', 'keluar')
            NOT NULL DEFAULT 'aktif'
        ");
    }

    public function down(): void
    {
        // Rollback: kembalikan enum 'pindah'
        DB::statement("
            ALTER TABLE siswa
            MODIFY status
            ENUM('aktif', 'tidak_aktif', 'lulus', 'pindah', 'keluar')
            NOT NULL DEFAULT 'aktif'
        ");
    }
};
