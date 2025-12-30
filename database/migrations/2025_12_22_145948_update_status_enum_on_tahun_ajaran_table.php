<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE tahun_ajaran
            MODIFY status ENUM('aktif','nonaktif')
            NOT NULL DEFAULT 'aktif'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE tahun_ajaran
            MODIFY status ENUM('aktif')
            NOT NULL DEFAULT 'aktif'
        ");
    }
};
