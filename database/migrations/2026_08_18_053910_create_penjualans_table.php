<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            // Menyambungkan transaksi dengan barang yang ada di tabel barangs
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            // Mencatat jumlah barang yang dibeli
            $table->integer('jumlah');
            // Mencatat total harga transaksi (jumlah x harga jual)
            $table->integer('total_harga');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('penjualans');
    }
};