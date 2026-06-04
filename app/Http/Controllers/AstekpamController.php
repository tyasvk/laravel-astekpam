<?php

namespace App\Http\Controllers;

use App\Models\Astekpam;
use App\Models\Pejabat;
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
        // 1. Ambil semua petugas untuk dipetakan otomatis (fitur autofill)
        $users = \App\Models\User::all();
        
        // 2. Ambil data Pejabat (Pengawas Piket, Perwira Piket, Staff KPLP)
        $pejabats = Pejabat::all();
    
        // 3. Mengambil ID terakhir dari setiap rupam yang pernah dibuat
        $lastRupamData = Astekpam::whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                  ->from('astekpams')
                  ->groupBy('dari_rupam');
        })
        ->get()
        ->keyBy('dari_rupam'); 

        // 4. Ambil 1 Laporan Paling Terakhir (Shift sebelumnya secara absolut)
        $latestReport = Astekpam::with('user')->latest()->first();

        // 5. Return HANYA SATU KALI dengan mengirimkan semua data yang dibutuhkan
        return Inertia::render('Astekpam/Create', [
            'users'         => $users,          // Untuk autofill form Rupam
            'pejabats'      => $pejabats,       // Untuk dropdown pejabat
            'lastRupamData' => $lastRupamData,  // Data rupam terakhir
            'lastReport'    => $latestReport    // Untuk Riwayat Shift Sebelumnya
        ]);
    }

    /**
     * Menyimpan laporan baru ke database.
     */
/**
     * Menyimpan laporan baru ke database.
     */
    public function store(Request $request)
    {
        // Gunakan validasi yang mengakomodasi struktur array/nested object
        $validated = $request->validate([
            'tanggal' => 'required',
            'pukul' => 'required',
            'dari_rupam' => 'required',
            'tugas' => 'required|array', // Pastikan tugas terkirim
        ]);

        // 1. Simpan ke database (Tampung ke dalam variabel $astekpam)
        $astekpam = Astekpam::create($request->all());

        // 2. Format Teks Laporan untuk WhatsApp
        $pesanWA = "*ASTEKPAM LAPAS KELAS I PALEMBANG*\n\n";
        $pesanWA .= "Assalamu’alaikum Warahmatullahi Wabarakatuh\n\n";
        $pesanWA .= "Hari/Tgl : *" . $astekpam->tanggal . "*\n";
        $pesanWA .= "Pukul : *" . $astekpam->pukul . " WIB*\n\n";
        $pesanWA .= "Berikut ASTEKPAM dari *" . $astekpam->dari_rupam . "* (Shift " . $astekpam->dari_shift . ") ke *" . $astekpam->ke_rupam . "* (Shift " . $astekpam->ke_shift . ") Dipimpin oleh *" . ($astekpam->pimpinan ?? '-') . "* berjalan aman dan tertib.\n\n";
        $pesanWA .= "Rincian WBP:\n";
        $pesanWA .= "- Kapasitas : " . ($astekpam->kapasitas ?? '-') . "\n";
        $pesanWA .= "- Narapidana : " . ($astekpam->narapidana ?? '-') . "\n";
        $pesanWA .= "- Total WBP : *" . ($astekpam->total_wbp ?? '-') . "*\n\n";
        $pesanWA .= "Kehadiran Rupam: " . ($astekpam->rupam_hadir ?? '-') . "/" . ($astekpam->rupam_jumlah ?? '-') . " Hadir\n\n";
        $pesanWA .= "Untuk rincian pembagian tugas lengkap, silakan cek di aplikasi Astekpam.";

        // 3. Proses Pengiriman via HTTP Request (Gunakan Try-Catch agar jika WA error, web tidak ikut error)
        try {
            \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN')
            ])->post('https://api.fonnte.com/send', [
                'target' => env('WA_GROUP_TARGET'),
                'message' => $pesanWA,
                'countryCode' => '62', // Kode negara Indonesia
            ]);
        } catch (\Exception $e) {
            // Log error jika pengiriman pesan gagal, tapi biarkan proses berlanjut
            \Illuminate\Support\Facades\Log::error('Gagal kirim Notif WA Astekpam: ' . $e->getMessage());
        }

        // 4. Redirect kembali ke halaman index
        return redirect()->route('astekpam.index')->with('success', 'Laporan berhasil disimpan dan diteruskan ke WhatsApp');
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