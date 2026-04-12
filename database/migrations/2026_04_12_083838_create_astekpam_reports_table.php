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
    Schema::create('astekpam_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->date('tanggal');
        $table->string('waktu'); // 13.00-19.30 WIB
        $table->string('dari_rupam');
        $table->string('ke_rupam');
        $table->string('pimpinan');
        
        // Data Penghuni
        $table->integer('napi_total');
        $table->integer('blok_a');
        $table->integer('blok_b');
        $table->integer('dapur');
        $table->integer('klinik');
        
        // Data Personil & Tugas (JSON untuk fleksibilitas)
        $table->json('personil'); 
        $table->json('tugas'); 
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astekpam_reports');
    }
};
