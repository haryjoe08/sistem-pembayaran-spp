<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // drop foreign key lama
            $table->dropForeign(['pembayaran_id']);

            // rename kolom
            $table->renameColumn('pembayaran_id', 'tagihan_id');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            // bikin foreign key baru ke tabel tagihan
            $table->foreign('tagihan_id')->references('id')->on('tagihan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['tagihan_id']);
            $table->renameColumn('tagihan_id', 'pembayaran_id');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreign('pembayaran_id')->references('id')->on('pembayaran')->onDelete('cascade');
        });
    }
};
