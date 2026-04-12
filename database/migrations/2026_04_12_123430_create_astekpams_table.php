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
    Schema::create('astekpams', function (Blueprint $table) {
        $table->id();
        // Waktu & Serah Terima
        $table->date('tanggal');
        $table->string('pukul'); // (13.00-19.30)
        $table->string('dari_rupam');
        $table->string('dari_shift');
        $table->string('ke_rupam');
        $table->string('ke_shift');
        $table->string('pimpinan')->nullable();

        // A. Jumlah Penghuni
        $table->integer('kapasitas')->default(0);
        $table->integer('narapidana')->default(0);
        $table->integer('blok_a')->default(0);
        $table->integer('blok_b')->default(0);
        $table->integer('dapur')->default(0);
        $table->integer('klinik')->default(0);
        $table->integer('dalam_lapas')->default(0);
        $table->integer('luar_lapas')->default(0);
        $table->integer('rawat_inap_rs')->default(0);
        $table->integer('berobat_rs')->default(0);
        $table->integer('bon_luar')->default(0);
        $table->integer('total_wbp')->default(0);

        // B. Personil Pengamanan
        $table->string('rupam_pilihan');
        $table->integer('rupam_jumlah')->default(0);
        $table->integer('rupam_hadir')->default(0);
        $table->integer('rupam_tidak_hadir')->default(0);
        $table->string('rupam_keterangan')->nullable();

        // P2U (Jika dipisah dari rupam utama)
        $table->string('p2u_rupam')->nullable();
        $table->integer('p2u_jumlah')->default(0);
        $table->integer('p2u_hadir')->default(0);
        $table->string('p2u_keterangan')->nullable();

        // 3. Pembagian Tugas (Disimpan sebagai JSON)
        $table->json('tugas'); 

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astekpams');
    }
};
