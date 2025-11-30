<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->unsignedBigInteger('tagihan_id');
            $table->integer('siswa_nis'); // UBAH INI DARI unsignedBigInteger
            $table->decimal('amount', 15, 2);
            $table->enum('payment_type', ['gopay', 'qris', 'bank_transfer', 'credit_card', 'echannel', 'bca_klikpay', 'bri_epay', 'cimb_clicks', 'shopeepay', 'alfamart', 'indomaret'])->nullable();
            $table->enum('status', ['pending', 'settlement', 'capture', 'deny', 'cancel', 'expire', 'failure'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->string('payment_code')->nullable();
            $table->string('pdf_url')->nullable();
            $table->text('snap_token')->nullable();
            $table->text('payment_url')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            
            $table->foreign('tagihan_id')->references('id')->on('tagihan')->onDelete('cascade');
            $table->foreign('siswa_nis')->references('nis')->on('siswa')->onDelete('cascade');
            
            $table->index('order_id');
            $table->index('status');
            $table->index('transaction_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_orders');
    }
};