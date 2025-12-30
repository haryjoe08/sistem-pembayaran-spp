<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Migration: update_login_table_add_kepsek_role.php
    public function up()
    {
        // Ubah enum role untuk include 'kepsek'
        DB::statement("ALTER TABLE login MODIFY COLUMN role ENUM('admin', 'siswa', 'kepsek') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE login MODIFY COLUMN role ENUM('admin', 'siswa') NOT NULL");
    }
};
