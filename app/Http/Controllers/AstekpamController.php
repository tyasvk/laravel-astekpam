<?php

namespace App\Http\Controllers;

use App\Models\Astekpam;
use App\Models\Pejabat;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        $users = \App\Models\User::all();
        $pejabats = Pejabat::all();
    
        $lastRupamData = Astekpam::whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                  ->from('astekpams')
                  ->groupBy('dari_rupam');
        })
        ->get()
        ->keyBy('dari_rupam'); 

        $latestReport = Astekpam::with('user')->latest()->first();

        return Inertia::render('Astekpam/Create', [
            'users'         => $users,
            'pejabats'      => $pejabats,
            'lastRupamData' => $lastRupamData,
            'lastReport'    => $latestReport 
        ]);
    }

    /**
     * Menyimpan laporan baru ke database dan mengirim WA via Fonnte.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required',
            'pukul' => 'required',
            'dari_rupam' => 'required',
            'tugas' => 'required|array',
        ]);

        // 1. Ambil semua data input
        $data = $request->all();

        // 2. TANGANI KOLOM ANGKA: Ubah null/kosong menjadi 0
        $kolomAngka = [
            'kapasitas', 'narapidana', 'blok_a', 'blok_b', 'dapur', 'klinik', 
            'dalam_lapas', 'luar_lapas', 'total_wbp', 
            'rupam_jumlah', 'rupam_hadir', 'p2u_jumlah', 'p2u_hadir'
        ];
        foreach ($kolomAngka as $kolom) {
            $data[$kolom] = $data[$kolom] ?? 0;
        }

        // 3. TANGANI KOLOM TEKS: Ubah null/kosong menjadi tanda strip '-'
        $kolomTeks = [
            'dari_shift', 'ke_rupam', 'ke_shift', 'pimpinan', 
            'rupam_pilihan', 'rupam_keterangan', 'p2u_keterangan'
        ];
        foreach ($kolomTeks as $kolom) {
            $data[$kolom] = $data[$kolom] ?? '-';
        }

        // 4. TANGANI KOLOM JSON: Beri array kosong jika tidak ada isian
        $data['rawat_inap_items'] = $data['rawat_inap_items'] ?? [];
        $data['berobat_items'] = $data['berobat_items'] ?? [];
        $data['bon_luar_items'] = $data['bon_luar_items'] ?? [];

        // 5. Simpan ke database menggunakan $data yang sudah dibersihkan
        $astekpam = Astekpam::create($data);

        // 6. Format Teks Laporan Lengkap
        $pesanWA = $this->generatePesanLaporan($astekpam);

        // 7. Proses Pengiriman via HTTP Request Fonnte
        try {
            Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN')
            ])->post('https://api.fonnte.com/send', [
                'target' => env('WA_GROUP_TARGET'), // Pastikan target benar di .env
                'message' => $pesanWA,
                'delay' => '2', // Jeda natural
            ]);
        } catch (\Exception $e) {
            // Log error jika WA gagal, tapi biarkan proses web berlanjut
            Log::error('Gagal kirim Notif WA Astekpam: ' . $e->getMessage());
        }

        // 8. Redirect kembali ke halaman index
        return redirect()->route('astekpam.index')->with('success', 'Laporan berhasil disimpan dan diteruskan ke WhatsApp Grup!');
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

    // =========================================================================
    // FUNGSI BANTUAN UNTUK MEMBENTUK TEKS WHATSAPP
    // =========================================================================
    private function generatePesanLaporan($data)
    {
        Carbon::setLocale('id');
        $tanggalIndo = Carbon::parse($data->tanggal)->translatedFormat('l, d F Y');
        
        $pesan = "*ASTEKPAM LAPAS KELAS I PALEMBANG*\n\n";
        $pesan .= "Assalamu’alaikum Warahmatullahi Wabarakatuh\n";
        $pesan .= "Selamat Bertugas....\n\n";
        
        $pesan .= "Hari/Tgl : *" . $tanggalIndo . "*\n";
        $pesan .= "Pukul : *" . $data->pukul . " WIB*\n\n";
        
        $pesan .= "Berikut, ASTEKPAM dari *" . ($data->dari_rupam ?? '-') . "* (Shift " . ($data->dari_shift ?? '-') . ") ke *" . ($data->ke_rupam ?? '-') . "* (Shift " . ($data->ke_shift ?? '-') . ") Dipimpin oleh *" . ($data->pimpinan ?? '-') . "* berjalan aman dan tertib.\n\n";
        
        $pesan .= "Dengan rincian sebagai berikut :\n\n";
        $pesan .= "*A. JUMLAH PENGHUNI*\n";
        $pesan .= "1. Kapasitas : " . ($data->kapasitas ?? 0) . " Org\n";
        $pesan .= "2. Narapidana : " . ($data->narapidana ?? 0) . " Org\n";
        $pesan .= "3. Isi Blok Hunian :\n";
        $pesan .= "  - Blok A : " . ($data->blok_a ?? 0) . " Org\n";
        $pesan .= "  - Blok B : " . ($data->blok_b ?? 0) . " Org\n";
        $pesan .= "  - Dapur : " . ($data->dapur ?? 0) . " Org\n";
        $pesan .= "  - Klinik : " . ($data->klinik ?? 0) . " Org\n";
        $pesan .= "  - Dalam Lapas : " . ($data->dalam_lapas ?? 0) . " Org\n";
        $pesan .= "  - Luar Lapas : " . ($data->luar_lapas ?? 0) . " Org\n\n";

        $pesan .= "4. Keterangan di luar Lapas :\n";
        $pesan .= "  - Rawat Inap RS : " . $this->formatJsonArray($data->rawat_inap_items) . "\n";
        $pesan .= "  - Berobat RS : " . $this->formatJsonArray($data->berobat_items) . "\n";
        $pesan .= "  - Bon luar : " . $this->formatJsonArray($data->bon_luar_items) . "\n\n";
        
        $pesan .= "*5. Total Jumlah WBP : " . ($data->total_wbp ?? 0) . " Org*\n\n";

        $pesan .= "*B. PERSONIL PENGAMANAN*\n";
        $pesan .= "1. *" . ($data->rupam_pilihan ?? 'Rupam -') . "*\n";
        $pesan .= "  - Jumlah : " . ($data->rupam_jumlah ?? 0) . " Org\n";
        $pesan .= "  - Hadir : " . ($data->rupam_hadir ?? 0) . " Org\n";
        $kurangRupam = (int)($data->rupam_jumlah ?? 0) - (int)($data->rupam_hadir ?? 0);
        $pesan .= "  - Kurang : " . ($kurangRupam > 0 ? $kurangRupam . " Org" : "-") . "\n";
        $pesan .= "  - Ket : " . ($data->rupam_keterangan ?: '-') . "\n\n";

        $pesan .= "2. *SATGAS P2U*\n";
        $pesan .= "  - Jumlah : " . ($data->p2u_jumlah ?? 0) . " Org\n";
        $pesan .= "  - Hadir : " . ($data->p2u_hadir ?? 0) . " Org\n";
        $pesan .= "  - Ket : " . ($data->p2u_keterangan ?: '-') . "\n\n";

        // Parsing data Pembagian Tugas
        $tugas = is_string($data->tugas) ? json_decode($data->tugas, true) : $data->tugas;
        
        if ($tugas && is_array($tugas)) {
            $pesan .= "*3. Pembagian Tugas :*\n";
            $pesan .= "a. Ka. Rupam : " . ($tugas['ka_rupam'] ?? '-') . "\n";
            $pesan .= "   Wakarupam : " . ($tugas['wakarupam'] ?? '-') . "\n";
            $pesan .= "b. Petugas P2U :\n";
            $pesan .= "   - Kasatgas : " . ($tugas['kasatgas_p2u'] ?? '-') . "\n";
            $pesan .= "   - Wakasatgas : " . ($tugas['wakasatgas_p2u'] ?? '-') . "\n";
            
            $pesan .= "c. Petugas Blok :\n";
            $pesan .= "   - Blok A : " . $this->formatJamTugas($tugas['blok_a'] ?? []) . "\n";
            $pesan .= "   - Blok B : " . $this->formatJamTugas($tugas['blok_b'] ?? []) . "\n";
            
            $pesan .= "d. Petugas Pos Atas :\n";
            $pesan .= "   - Menara 1 : " . $this->formatJamTugas($tugas['menara_1'] ?? []) . "\n";
            $pesan .= "   - Menara 2 : " . $this->formatJamTugas($tugas['menara_2'] ?? []) . "\n";
            $pesan .= "   - Menara 3 : " . $this->formatJamTugas($tugas['menara_3'] ?? []) . "\n";
            $pesan .= "   - Menara 4 : " . $this->formatJamTugas($tugas['menara_4'] ?? []) . "\n";

            $pesan .= "e. Jaga RS : " . ($tugas['jaga_rs'] ?? '-') . "\n";
            $pesan .= "f. Piket Dapur : " . ($tugas['piket_dapur'] ?? '-') . "\n";
            $pesan .= "g. Pengawas Piket : " . ($tugas['perwira_piket'] ?? '-') . "\n";
            $pesan .= "h. Perwira Piket : " . ($tugas['perwira_kontrol'] ?? '-') . "\n";
            $pesan .= "i. Banjaga : " . ($tugas['banjaga'] ?? '-') . "\n";
            $pesan .= "j. Staff KPLP : " . ($tugas['staff_kplp'] ?? '-') . "\n";
            $pesan .= "k. Amanah : " . ($tugas['amanah'] ?? '-') . "\n";
            $pesan .= "*l. Petugas Laporan : " . strtoupper($tugas['petugas_laporan'] ?? '-') . "*\n";
        }

        $pesan .= "\nDemikian Laporan ini, kami sampaikan dan diucapkan terima kasih.\n\n";
        $pesan .= "Wassalamu'alaikum Warahmatullaahi wabarakaatuh\n";
        $pesan .= "Salam Sejahtera\n";
        $pesan .= "Salam Sehat Selalu…🙏\n\n";

        // =======================================================
        // TAMBAHAN: MENGAMBIL NAMA & NOMOR HP USER YANG LOGIN
        // =======================================================
        $user = auth()->user();
        if ($user) {
            $namaPetugas = $user->name;
            
            // NOTE: Sesuaikan 'no_hp' dengan nama kolom tabel database users Anda
            $nomorHp = $user->no_hp ?? ''; 
            
            if (str_starts_with($nomorHp, '0')) {
                $nomorHp = '62' . substr($nomorHp, 1);
            }

            $pesan .= "-----------------------------------\n";
            $pesan .= "*Dikirim Oleh:*\n";
            $pesan .= "Nama : " . $namaPetugas . "\n";
            $pesan .= "No. WA : @" . $nomorHp; 
        }

        return $pesan;
    }

    private function formatJsonArray($data)
    {
        if (empty($data)) return '-';
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        if (!is_array($data)) return '-';
        
        $validItems = array_filter($data, function($item) {
            return !empty($item['ket']) && trim($item['ket']) !== '';
        });
        
        if (empty($validItems)) return '-';
        
        return implode(', ', array_map(function($item) {
            return trim($item['ket']);
        }, $validItems));
    }

    private function formatJamTugas($jamArray)
    {
        if (!is_array($jamArray) || empty($jamArray)) return '-';
        
        $validJams = array_filter([
            $jamArray['jam_1'] ?? null,
            $jamArray['jam_2'] ?? null,
            $jamArray['jam_3'] ?? null,
        ], function($val) {
            return !empty($val) && $val !== '-';
        });

        if (empty($validJams)) return '-';
        return implode('/', $validJams);
    }
}