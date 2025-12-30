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
    // 1. DROP FOREIGN KEY DULU
    Schema::table('tagihan', function (Blueprint $table) {
        $table->dropForeign('pembayaran_jenis_pembayaran_id_foreign');
    });

    Schema::table('tarif_tagihan', function (Blueprint $table) {
        $table->dropForeign('tarif_pembayaran_jenis_pembayaran_id_foreign');
        $table->dropUnique('unique_tarif_per_tahun');
    });

    // 2. RENAME TABLE
    Schema::rename('jenis_pembayaran', 'jenis_tagihan');

    // 3. RENAME COLUMN
    Schema::table('tagihan', function (Blueprint $table) {
        $table->renameColumn('jenis_pembayaran_id', 'jenis_tagihan_id');
    });

    Schema::table('tarif_tagihan', function (Blueprint $table) {
        $table->renameColumn('jenis_pembayaran_id', 'jenis_tagihan_id');
    });

    // 4. ADD FK + INDEX BARU (NAMA RAPI)
    Schema::table('tagihan', function (Blueprint $table) {
        $table->foreign('jenis_tagihan_id', 'tagihan_jenis_tagihan_id_foreign')
              ->references('id')
              ->on('jenis_tagihan')
              ->onDelete('cascade');
    });

    Schema::table('tarif_tagihan', function (Blueprint $table) {
        $table->unique(
            ['jenis_tagihan_id', 'tahun_ajaran_id'],
            'tarif_tagihan_jenis_tagihan_tahun_ajaran_unique'
        );

        $table->foreign('jenis_tagihan_id', 'tarif_tagihan_jenis_tagihan_id_foreign')
              ->references('id')
              ->on('jenis_tagihan')
              ->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
public function down(): void
    {
        // rollback FK baru
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropForeign('tagihan_jenis_tagihan_id_foreign');
        });

        Schema::table('tarif_tagihan', function (Blueprint $table) {
            $table->dropForeign('tarif_tagihan_jenis_tagihan_id_foreign');
            $table->dropUnique('tarif_tagihan_jenis_tagihan_tahun_ajaran_unique');
        });

        // rename kolom balik
        Schema::table('tagihan', function (Blueprint $table) {
            $table->renameColumn('jenis_tagihan_id', 'jenis_pembayaran_id');
        });

        Schema::table('tarif_tagihan', function (Blueprint $table) {
            $table->renameColumn('jenis_tagihan_id', 'jenis_pembayaran_id');
        });

        // rename tabel balik
        Schema::rename('jenis_tagihan', 'jenis_pembayaran');

        // restore FK lama
        Schema::table('tagihan', function (Blueprint $table) {
            $table->foreign('jenis_pembayaran_id', 'pembayaran_jenis_pembayaran_id_foreign')
                  ->references('id')
                  ->on('jenis_pembayaran')
                  ->onDelete('cascade');
        });

        Schema::table('tarif_tagihan', function (Blueprint $table) {
            $table->unique(
                ['jenis_pembayaran_id', 'tahun_ajaran_id'],
                'unique_tarif_per_tahun'
            );

            $table->foreign('jenis_pembayaran_id', 'tarif_pembayaran_jenis_pembayaran_id_foreign')
                  ->references('id')
                  ->on('jenis_pembayaran')
                  ->onDelete('cascade');
        });
    }
};
