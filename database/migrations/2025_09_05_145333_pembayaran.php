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
        //

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->integer('siswa_nis'); 
            $table->foreign('siswa_nis')->references('nis')->on('siswa')->onDelete('cascade');

            $table->foreignId('jenis_pembayaran_id')->constrained('jenis_pembayaran')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->decimal('total_tagihan', 12, 2);
            $table->decimal('sudah_dibayar', 12, 2)->default(0);
            $table->enum('status', ['belum lunas', 'lunas'])->default('belum lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('pembayaran');
    }
};
