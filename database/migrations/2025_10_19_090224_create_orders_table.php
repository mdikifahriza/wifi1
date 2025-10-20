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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // Order ID dari Midtrans
            $table->string('order_id')->unique();
            
            // Detail Pelanggan
            $table->string('name', 50); // Dari input LOG.blade.php
            $table->string('nim', 15);  // Dari input LOG.blade.php

            // Detail Transaksi
            $table->unsignedInteger('duration_seconds'); // Durasi yang dibeli user
            $table->unsignedBigInteger('gross_amount');  // Total harga (100 * duration_seconds)
            
            // Status Pembayaran
            $table->string('status')->default('pending'); // Status dari Midtrans (pending, settlement, expire, etc.)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};