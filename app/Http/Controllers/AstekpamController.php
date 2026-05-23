<?php

namespace App\Http\Controllers;

use App\Models\Astekpam;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class AstekpamController extends Controller
{
    /**
     * Menampilkan daftar riwayat laporan Astekpam.
     */
public function index()
{
    // Mengambil semua data astekpam, biasanya diurutkan dari yang terbaru
    $astekpams = Astekpam::with('user')->latest()->get();

    return Inertia::render('Astekpam/Index', [
        'astekpams' => $astekpams,
    ]);
}

    /**
     * Menampilkan form buat laporan.
     */
public function create()
    {
        // Mengambil ID terakhir dari setiap rupam yang pernah dibuat
        $lastRupamData = Astekpam::whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                  ->from('astekpams')
                  ->groupBy('dari_rupam');
        })
        ->get()
        ->keyBy('dari_rupam'); // Mengubah array menjadi object dengan key nama rupam

        return Inertia::render('Astekpam/Create', [
            'lastRupamData' => $lastRupamData
        ]);
    }

    /**
     * Menyimpan laporan baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'           => 'required|date',
            'pukul'             => 'required|string',
            'dari_rupam'        => 'required|string',
            'dari_shift'        => 'required|string',
            'ke_rupam'          => 'required|string',
            'ke_shift'          => 'required|string',
            'pimpinan'          => 'nullable|string',
            
            // A. Jumlah Penghuni
            'kapasitas'         => 'nullable|integer',
            'narapidana'        => 'nullable|integer',
            'blok_a'            => 'nullable|integer',
            'blok_b'            => 'nullable|integer',
            'dapur'             => 'nullable|integer',
            'klinik'            => 'nullable|integer',
            'dalam_lapas'       => 'nullable|integer',
            'luar_lapas'        => 'nullable|integer',
            'rawat_inap_rs'     => 'nullable|integer',
            'berobat_rs'        => 'nullable|integer',
            'bon_luar'          => 'nullable|integer',
            'total_wbp'         => 'nullable|integer',

            // B. Personil Pengamanan
            'rupam_pilihan'     => 'required|string',
            'rupam_jumlah'      => 'nullable|integer',
            'rupam_hadir'       => 'nullable|integer',
            'rupam_tidak_hadir' => 'nullable|integer',
            'rupam_keterangan'  => 'nullable|string',
            
            'p2u_rupam'         => 'nullable|string',
            'p2u_jumlah'        => 'nullable|integer',
            'p2u_hadir'         => 'nullable|integer',
            'p2u_keterangan'    => 'nullable|string',

            // 3. Pembagian Tugas
            'tugas'             => 'required|array',
        ]);

        Astekpam::create($validated);

        return Redirect::route('dashboard')->with('success', 'Laporan Astekpam berhasil disimpan dan dikirim.');
    }

    /**
     * Menampilkan detail laporan tertentu.
     * Metode ini yang sebelumnya hilang (undefined).
     */
    public function show(Astekpam $astekpam)
    {
        return Inertia::render('Astekpam/Show', [
            'astekpam' => $astekpam
        ]);
    }
}