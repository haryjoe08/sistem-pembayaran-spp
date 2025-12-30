
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
        // Tambah kolom status ke tabel tahun_ajaran
        if (Schema::hasTable('tahun_ajaran')) {
            Schema::table('tahun_ajaran', function (Blueprint $table) {
                if (!Schema::hasColumn('tahun_ajaran', 'status')) {
                    $table->enum('status', ['aktif', 'non_aktif'])
                          ->default('aktif')
                          ->after('tahun')
                          ->comment('Status tahun_ajaran: aktif atau non_aktif');
                }
            });
        }

       

        // Set semua data yang sudah ada jadi 'aktif'
        DB::table('tahun_ajaran')->update(['status' => 'aktif']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus kolom status dari tabel tahun_ajaran
        if (Schema::hasTable('tahun_ajaran')) {
            Schema::table('tahun_ajaran', function (Blueprint $table) {
                if (Schema::hasColumn('tahun', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }

    }
};