<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menghapus tabel piutangs yang kosong dan tidak digunakan.
     * Data piutang sesungguhnya sudah tersimpan di kolom sisa_piutang
     * pada tabel penjualans.
     */
    public function up(): void
    {
        Schema::dropIfExists('piutangs');
    }

    /**
     * Tidak perlu recreate karena tabel ini memang tidak digunakan.
     */
    public function down(): void
    {
        // Tabel piutangs sengaja tidak dibuat ulang karena sudah tidak dipakai.
    }
};
