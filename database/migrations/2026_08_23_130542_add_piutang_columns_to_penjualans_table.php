<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('nama_pelanggan')->default('Umum')->after('kode_transaksi');
            $table->decimal('sisa_piutang', 12, 2)->default(0)->after('uang_kembali');
        });
    }

    public function down()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['nama_pelanggan', 'sisa_piutang']);
        });
    }
};