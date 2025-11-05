<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign('pembayaran_tahun_ajaran_id_foreign');
            // Baru hapus kolom
            $table->dropColumn('tahun_ajaran_id');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
            $table->foreign('tahun_ajaran_id')
                  ->references('id')
                  ->on('tahun_ajaran')
                  ->onDelete('cascade');
        });
    }
};
