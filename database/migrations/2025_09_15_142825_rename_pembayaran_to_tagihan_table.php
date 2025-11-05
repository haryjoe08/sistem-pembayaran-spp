<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::rename('pembayaran', 'tagihan');
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::rename('tagihan', 'pembayaran');
    }
};
