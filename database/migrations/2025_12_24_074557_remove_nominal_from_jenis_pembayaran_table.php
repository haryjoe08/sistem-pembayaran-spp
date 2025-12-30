<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jenis_pembayaran', function (Blueprint $table) {
            $table->dropColumn('nominal');
        });
    }

    public function down(): void
    {
        Schema::table('jenis_pembayaran', function (Blueprint $table) {
            $table->integer('nominal')->after('nama');
        });
    }
};
