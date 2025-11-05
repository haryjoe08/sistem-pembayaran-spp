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
          Schema::create('jenis_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // SPP, Seragam, Gedung, dll
            $table->text('deskripsi')->nullable();
            $table->decimal('nominal', 12, 2); // default nominal (bisa override di pembayaran)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('jenis_pembayaran');
    }
};
