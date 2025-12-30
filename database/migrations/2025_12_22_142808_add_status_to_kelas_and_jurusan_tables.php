<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah kolom status ke tabel kelas
        if (Schema::hasTable('kelas')) {
            Schema::table('kelas', function (Blueprint $table) {
                if (!Schema::hasColumn('kelas', 'status')) {
                    $table->enum('status', ['aktif', 'non_aktif'])
                          ->default('aktif')
                          ->after('kelas')
                          ->comment('Status kelas: aktif atau non_aktif');
                }
            });
        }

        // Tambah kolom status ke tabel jurusan
        if (Schema::hasTable('jurusan')) {
            Schema::table('jurusan', function (Blueprint $table) {
                if (!Schema::hasColumn('jurusan', 'status')) {
                    $table->enum('status', ['aktif', 'non_aktif'])
                          ->default('aktif')
                          ->after('nama')
                          ->comment('Status jurusan: aktif atau non_aktif');
                }
            });
        }

        // Set semua data yang sudah ada jadi 'aktif'
        DB::table('kelas')->update(['status' => 'aktif']);
        DB::table('jurusan')->update(['status' => 'aktif']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus kolom status dari tabel kelas
        if (Schema::hasTable('kelas')) {
            Schema::table('kelas', function (Blueprint $table) {
                if (Schema::hasColumn('kelas', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }

        // Hapus kolom status dari tabel jurusan
        if (Schema::hasTable('jurusan')) {
            Schema::table('jurusan', function (Blueprint $table) {
                if (Schema::hasColumn('jurusan', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};