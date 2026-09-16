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
        // Tambahkan kolom diskon setelah harga_jual
        $table->integer('diskon')->default(0)->after('harga_jual');
    });
}

public function down()
{
    Schema::table('barangs', function (Blueprint $table) {
        $table->dropColumn('diskon');
    });
}
};
