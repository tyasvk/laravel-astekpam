<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Astekpam extends Model
{
    /**
     * Kolom yang dapat diisi secara massal.
     */
   protected $fillable = [
    'user_id', 'tanggal', 'pukul', 'dari_rupam', 'dari_shift', 'ke_rupam', 'ke_shift', 'pimpinan',
    'kapasitas', 'narapidana', 'blok_a', 'blok_b', 'dapur', 'klinik', 'dalam_lapas', 'luar_lapas',
    'rawat_inap_items', 'berobat_items', 'bon_luar_items', 'total_wbp',
    'rupam_pilihan', 'rupam_jumlah', 'rupam_hadir', 'rupam_keterangan',
    'p2u_jumlah', 'p2u_hadir', 'p2u_keterangan', 'tugas'
];

protected $casts = [
    'tugas' => 'array',
    'rawat_inap_items' => 'array',
    'berobat_items' => 'array',
    'bon_luar_items' => 'array',
];

    /**
     * Relasi ke User (Pembuat Laporan)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}