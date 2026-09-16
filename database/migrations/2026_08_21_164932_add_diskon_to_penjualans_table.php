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
    Schema::table('penjualans', function (Blueprint $table) {
        $table->decimal('diskon', 12, 2)->default(0)->after('jumlah');
    });
}

public function down()
{
    Schema::table('penjualans', function (Blueprint $table) {
        $table->dropColumn('diskon');
    });
}
};
