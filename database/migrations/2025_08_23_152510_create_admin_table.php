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
    Schema::create('admin', function (Blueprint $table) {
        $table->id(); // Primary Key
        $table->unsignedBigInteger('login_id'); // FK ke login
        $table->string('nama', 50);
        $table->timestamps();

        $table->foreign('login_id')->references('id')->on('login')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
