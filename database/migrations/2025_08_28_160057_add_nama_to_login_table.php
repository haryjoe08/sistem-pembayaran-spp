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
        Schema::table('login', function (Blueprint $table) {
            $table->string('nama')->after('id'); // tambah kolom nama setelah kolom id
        });
    }

    public function down(): void
    {
        Schema::table('login', function (Blueprint $table) {
            $table->dropColumn('nama');
        });
    }
};
