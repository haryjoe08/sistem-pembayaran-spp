<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->date('jatuh_tempo')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn('jatuh_tempo');
        });
    }
};