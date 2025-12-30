<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Pindahkan data kepsek ke admin (atau role lain)
        DB::table('login')
            ->where('role', 'kepsek')
            ->update(['role' => 'admin']);

        // Redefinisi enum TANPA kepsek
        DB::statement("
            ALTER TABLE login
            MODIFY role ENUM('admin', 'siswa')
            NOT NULL
        ");
    }

    public function down(): void
    {
        // Kembalikan enum kepsek jika rollback
        DB::statement("
            ALTER TABLE login
            MODIFY role ENUM('admin', 'siswa', 'kepsek')
            NOT NULL
        ");
    }
};
