<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jenis_pembayaran', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'nonaktif'])
                  ->default('aktif')
                  ->after('nominal'); // sesuaikan posisi kolom
        });

        // Pastikan data lama ikut aktif
        DB::table('jenis_pembayaran')
            ->whereNull('status')
            ->update(['status' => 'aktif']);
    }

    public function down(): void
    {
        Schema::table('jenis_pembayaran', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
