<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Astekpam extends Model
{
    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'tanggal',
        'pukul',
        'dari_rupam',
        'dari_shift',
        'ke_rupam',
        'ke_shift',
        'pimpinan',
        'kapasitas',
        'narapidana',
        'blok_a',
        'blok_b',
        'dapur',
        'klinik',
        'dalam_lapas',
        'luar_lapas',
        'rawat_inap_rs',
        'berobat_rs',
        'bon_luar',
        'total_wbp',
        'rupam_pilihan',
        'rupam_jumlah',
        'rupam_hadir',
        'rupam_tidak_hadir',
        'rupam_keterangan',
        'p2u_rupam',
        'p2u_jumlah',
        'p2u_hadir',
        'p2u_keterangan',
        'tugas'
    ];

    /**
     * Konversi tipe data otomatis.
     */
    protected $casts = [
        'tugas'   => 'array',
        'tanggal' => 'date',
    ];
}