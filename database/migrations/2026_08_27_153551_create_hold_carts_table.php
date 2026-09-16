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
    Schema::create('hold_carts', function (Blueprint $table) {
        $table->id();
        $table->string('nama_antrean'); // Contoh: "Pak Budi - Semen"
        $table->json('isi_keranjang');  // Menyimpan seluruh array keranjang dalam format JSON
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hold_carts');
    }
};
