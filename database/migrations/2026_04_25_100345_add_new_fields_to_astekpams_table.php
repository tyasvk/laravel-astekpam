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
    Schema::table('astekpams', function (Blueprint $table) {
        // Cek jika kolom belum ada, baru tambahkan
        if (!Schema::hasColumn('astekpams', 'p2u_jumlah')) {
            $table->integer('p2u_jumlah')->nullable();
        }
        if (!Schema::hasColumn('astekpams', 'p2u_hadir')) {
            $table->integer('p2u_hadir')->nullable();
        }
        if (!Schema::hasColumn('astekpams', 'p2u_keterangan')) {
            $table->string('p2u_keterangan')->nullable();
        }
        if (!Schema::hasColumn('astekpams', 'rawat_inap_items')) {
            $table->json('rawat_inap_items')->nullable();
        }
        if (!Schema::hasColumn('astekpams', 'berobat_items')) {
            $table->json('berobat_items')->nullable();
        }
        if (!Schema::hasColumn('astekpams', 'bon_luar_items')) {
            $table->json('bon_luar_items')->nullable();
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('astekpams', function (Blueprint $table) {
            //
        });
    }
};
