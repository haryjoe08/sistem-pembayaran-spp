<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tagihan_id');
            $table->integer('siswa_nis'); // INT tanpa auto_increment
            $table->dateTime('tanggal');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->enum('metode', ['cash', 'transfer', 'va', 'qris'])->default('cash');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('tagihan_id')->references('id')->on('tagihan')->onDelete('cascade');
            $table->foreign('siswa_nis')->references('nis')->on('siswa')->onDelete('cascade');
            
            // Indexes
            $table->index('tanggal');
            $table->index('metode');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
};