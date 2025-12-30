<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tarif_tagihan', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'nonaktif'])
                  ->default('aktif')
                  ->after('nominal');
        });

        // Optional tapi aman: set data lama jadi aktif
        DB::table('tarif_tagihan')->update(['status' => 'aktif']);
    }

    public function down(): void
    {
        Schema::table('tarif_tagihan', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
