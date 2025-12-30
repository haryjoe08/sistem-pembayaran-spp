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
 public function up()
{
    Schema::table('jenis_pembayaran', function (Blueprint $table) {
        if (!Schema::hasColumn('jenis_pembayaran', 'tipe')) {
            $table->enum('tipe', ['bulanan', 'tahunan', 'insidental'])
                  ->default('bulanan')
                  ->after('nama')
                  ->comment('Tipe pembayaran: bulanan, tahunan, atau insidental');
        }
    });
    
    // Set default untuk data existing
    DB::table('jenis_pembayaran')
        ->where('nama', 'like', '%SPP%')
        ->update(['tipe' => 'bulanan']);
    
    DB::table('jenis_pembayaran')
        ->where('nama', 'not like', '%SPP%')
        ->update(['tipe' => 'tahunan']);
}
};
