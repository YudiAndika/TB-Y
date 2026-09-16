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
    Schema::create('pembelians', function (Blueprint $table) {
        $table->id();
        $table->string('kode_po')->unique();
        $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
        $table->date('tanggal_pembelian');
        $table->decimal('total_biaya', 15, 2);
        $table->string('status_pembayaran')->default('Lunas'); // Lunas / Utang
        $table->text('catatan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
