<?php

namespace App\Http\Controllers;

use App\Models\Astekpam;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class AstekpamController extends Controller
{
    /**
     * Menampilkan daftar riwayat laporan Astekpam.
     */
    public function index()
    {
        $astekpams = Astekpam::with('user')->latest()->get();

        return Inertia::render('Astekpam/Index', [
            'astekpams' => $astekpams,
        ]);
    }

    /**
     * Menampilkan form buat laporan bersanding dengan data shift sebelumnya.
     */
    public function create()
    {
        // Mengambil ID terakhir dari setiap rupam yang pernah dibuat (Logika Asli Anda)
        $lastRupamData = Astekpam::whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                  ->from('astekpams')
                  ->groupBy('dari_rupam');
        })
        ->get()
        ->keyBy('dari_rupam'); 

        // TAMBAHAN: Ambil 1 Laporan Paling Terakhir (Shift sebelumnya secara absolut)
        $latestReport = Astekpam::with('user')->latest()->first();

        return Inertia::render('Astekpam/Create', [
            'lastRupamData' => $lastRupamData,
            'latestReport' => $latestReport // Kirim ke Vue
        ]);
    }

    /**
     * Menyimpan laporan baru ke database.
     */
  public function store(Request $request)
{
    // Coba log data yang masuk ke server
    // \Log::info($request->all());

    // Gunakan validasi yang mengakomodasi struktur array/nested object
    $validated = $request->validate([
        'tanggal' => 'required',
        'pukul' => 'required',
        'dari_rupam' => 'required',
        'tugas' => 'required|array', // Pastikan tugas terkirim
        // Tambahkan rule lain sesuai kebutuhan, atau gunakan $request->all() jika ingin bypass validasi sementara untuk testing
    ]);

    // Simpan ke database
    // Kita gunakan $request->all() untuk memastikan semua kolom dari form terambil
    // selama sudah terdaftar di $fillable di Model
    Astekpam::create($request->all());

    return redirect()->route('astekpam.index')->with('success', 'Laporan berhasil disimpan');
}
    /**
     * Menampilkan detail laporan tertentu.
     */
    public function show(Astekpam $astekpam)
    {
        return Inertia::render('Astekpam/Show', [
            'astekpam' => $astekpam
        ]);
    }
}