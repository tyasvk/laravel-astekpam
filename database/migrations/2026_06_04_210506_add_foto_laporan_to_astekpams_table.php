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
    Schema::table('astekpams', function (Blueprint $table) {
        $table->string('foto_laporan')->nullable()->after('pimpinan'); 
    });
}

public function down()
{
    Schema::table('astekpams', function (Blueprint $table) {
        $table->dropColumn('foto_laporan');
    });
}
};
