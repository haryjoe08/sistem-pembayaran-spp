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
        Schema::table('tagihan', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan', 'tahun_ajaran_id')) {
                $table->foreignId('tahun_ajaran_id')
                    ->nullable()
                    ->after('jenis_pembayaran_id')
                    ->constrained('tahun_ajaran')
                    ->onDelete('cascade');
            }
        });

        // Set tahun ajaran aktif untuk data existing
        $tahunAktif = DB::table('tahun_ajaran')->where('status', 'aktif')->first();
        if ($tahunAktif) {
            DB::table('tagihan')
                ->whereNull('tahun_ajaran_id')
                ->update(['tahun_ajaran_id' => $tahunAktif->id]);
        }
    }
};
