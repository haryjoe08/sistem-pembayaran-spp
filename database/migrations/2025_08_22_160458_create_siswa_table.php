<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('siswa', function (Blueprint $table) {
        $table->integer('nis')->primary(); // Primary Key
        $table->unsignedBigInteger('login_id'); // FK ke login.id
        $table->string('nama', 30);
        $table->date('tgl_lahir');
        $table->enum('jenis_kelamin', ['L', 'P']);
        $table->string('kelas', 20);
        $table->string('jurusan', 20);
        $table->string('alamat', 50);
        $table->string('wali', 50);
        $table->string('kontak', 50);
        $table->timestamps();

        $table->foreign('login_id')->references('id')->on('login')->onDelete('cascade');
        
    });
}


    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
