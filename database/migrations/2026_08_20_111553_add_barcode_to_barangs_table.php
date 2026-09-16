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
    Schema::table('barangs', function (Blueprint $table) {
        // Menambahkan kolom barcode (bisa kosong, tapi tidak boleh ada yang sama)
        $table->string('barcode')->nullable()->unique()->after('nama_barang');
    });
}

public function down()
{
    Schema::table('barangs', function (Blueprint $table) {
        $table->dropColumn('barcode');
    });
}
};
