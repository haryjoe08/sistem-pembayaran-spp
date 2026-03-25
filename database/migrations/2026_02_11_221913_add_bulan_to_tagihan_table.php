<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->tinyInteger('bulan')->unsigned()->nullable()
                ->after('tahun_ajaran_id')
                ->comment('1-12, hanya untuk tagihan bulanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            //
        });
    }
};
