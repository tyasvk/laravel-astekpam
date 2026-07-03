<?php

namespace App\Http\Controllers;

use App\Models\Astekpam;
use App\Models\Pejabat;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AstekpamController extends Controller
{
    /**
     * Menampilkan daftar riwayat laporan Astekpam.
     */
    public function index(Request $request)
    {
        $query = Astekpam::with('user')->latest();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        $astekpams = $query->get();

        return Inertia::render('Astekpam/Index', [
            'astekpams' => $astekpams,
            'filters' => $request->only(['start_date', 'end_date']) 
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
            'foto_laporan' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', 
        ], [
            'foto_laporan.image' => 'File harus berupa gambar.',
            'foto_laporan.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'foto_laporan.max'   => 'Ukuran foto maksimal adalah 10MB.',
            'dari_rupam.required' => 'Kolom "Dari Regu (Lama)" wajib dipilih.',
        ]);

        $data = $request->all();

        $kolomAngka = [
            'kapasitas', 'narapidana', 'blok_a', 'blok_b', 'dapur', 'klinik', 
            'dalam_lapas', 'luar_lapas', 'total_wbp', 
            'rupam_jumlah', 'rupam_hadir', 'p2u_jumlah', 'p2u_hadir'
        ];
        foreach ($kolomAngka as $kolom) {
            $data[$kolom] = $data[$kolom] ?? 0;
        }

        $kolomTeks = [
            'dari_shift', 'ke_rupam', 'ke_shift', 'pimpinan', 
            'rupam_pilihan', 'rupam_keterangan', 'p2u_keterangan'
        ];
        foreach ($kolomTeks as $kolom) {
            $data[$kolom] = $data[$kolom] ?? '-';
        }

        $data['rawat_inap_items'] = $data['rawat_inap_items'] ?? [];
        $data['berobat_items']    = $data['berobat_items'] ?? [];
        $data['bon_luar_items']   = $data['bon_luar_items'] ?? [];
        $data['tugas']            = $data['tugas'] ?? [];

        if ($request->hasFile('foto_laporan')) {
            $path = $request->file('foto_laporan')->store('foto_laporan', 'public');
            $data['foto_laporan'] = $path;
        }

        $data['user_id'] = auth()->id(); 
        $astekpam = Astekpam::create($data);

        $pesanWA = $this->generatePesanLaporan($astekpam);

        // =====================================================================
        // PROSES PENGIRIMAN WA (URUTAN: FOTO DULUAN, LALU TEKS PANJANG)
        // =====================================================================
        try {
            $targetWa = env('FONNTE_TARGET', '120363408257421349@g.us');
            $tokenWa  = env('FONNTE_TOKEN', 'rZxtE0g#XU9m5E+jW9ZJ');

            // LANGKAH 1: KIRIM FOTO SECARA FISIK DULUAN
            if (!empty($astekpam->foto_laporan) && Storage::disk('public')->exists($astekpam->foto_laporan)) {
                
                $fotoPath = Storage::disk('public')->path($astekpam->foto_laporan);
                
                // Ambil ekstensi asli, jika tidak ada paksa jadi .jpg agar Fonnte tidak error
                $ekstensi = pathinfo($fotoPath, PATHINFO_EXTENSION) ?: 'jpg';
                $namaFile = 'bukti_laporan.' . $ekstensi;
                
                // MENGGUNAKAN file_get_contents AGAR TIDAK ADA BUG STREAM (100% BEKERJA)
                $resFoto = Http::withoutVerifying()
                    ->timeout(60)
                    ->withHeaders([
                        'Authorization' => $tokenWa
                    ])
                    ->attach('file', file_get_contents($fotoPath), $namaFile)
                    ->post('https://api.fonnte.com/send', [
                        'target' => $targetWa,
                        'message' => "*📷 LAMPIRAN BUKTI FOTO LAPORAN ASTEKPAM*\nTanggal: " . Carbon::parse($astekpam->tanggal)->translatedFormat('d F Y'),
                    ]);

                if ($resFoto->failed()) {
                    Log::error('Fonnte API gagal kirim foto: ' . $resFoto->body());
                } else {
                    Log::info('Notif WA Foto Astekpam Berhasil Dikirim: ' . $resFoto->body());
                }

                // Beri jeda 2 detik agar API WA selesai merender gambar di dalam grup
                sleep(2); 
            }

            // LANGKAH 2: KIRIM TEKS LAPORAN PANJANG MENYUSUL DI BAWAHNYA
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->retry(3, 2000)
                ->withHeaders([
                    'Authorization' => $tokenWa
                ])->post('https://api.fonnte.com/send', [
                    'target' => $targetWa, 
                    'message' => $pesanWA,
                ]);

            if ($response->failed()) {
                Log::error('Fonnte API menolak pengiriman teks laporan: ' . $response->body());
            } else {
                Log::info('Notif WA Teks Astekpam Berhasil Dikirim: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Gagal total menghubungi API Fonnte Astekpam: ' . $e->getMessage());
        }

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

    public function edit(Astekpam $astekpam)
    {
        $users = \App\Models\User::all();
        $pejabats = Pejabat::all();

        return Inertia::render('Astekpam/Edit', [
            'astekpam' => $astekpam,
            'users'    => $users,
            'pejabats' => $pejabats,
        ]);
    }

    public function update(Request $request, Astekpam $astekpam)
    {
        $validated = $request->validate([
            'tanggal' => 'required',
            'pukul' => 'required',
            'dari_rupam' => 'required',
            'tugas' => 'required|array',
            'foto_laporan' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', 
        ], [
            'foto_laporan.image' => 'File harus berupa gambar.',
            'foto_laporan.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'foto_laporan.max'   => 'Ukuran foto maksimal adalah 10MB.',
            'dari_rupam.required' => 'Kolom "Dari Regu (Lama)" wajib dipilih.',
        ]);

        $data = $request->all();

        $kolomAngka = [
            'kapasitas', 'narapidana', 'blok_a', 'blok_b', 'dapur', 'klinik', 
            'dalam_lapas', 'luar_lapas', 'total_wbp', 
            'rupam_jumlah', 'rupam_hadir', 'p2u_jumlah', 'p2u_hadir'
        ];
        foreach ($kolomAngka as $kolom) {
            $data[$kolom] = $data[$kolom] ?? 0;
        }

        $kolomTeks = [
            'dari_shift', 'ke_rupam', 'ke_shift', 'pimpinan', 
            'rupam_pilihan', 'rupam_keterangan', 'p2u_keterangan'
        ];
        foreach ($kolomTeks as $kolom) {
            $data[$kolom] = $data[$kolom] ?? '-';
        }

        $data['rawat_inap_items'] = $data['rawat_inap_items'] ?? [];
        $data['berobat_items']    = $data['berobat_items'] ?? [];
        $data['bon_luar_items']   = $data['bon_luar_items'] ?? [];
        $data['tugas']            = $data['tugas'] ?? [];

        if ($request->hasFile('foto_laporan')) {
            if ($astekpam->foto_laporan) {
                Storage::disk('public')->delete($astekpam->foto_laporan);
            }
            $path = $request->file('foto_laporan')->store('foto_laporan', 'public');
            $data['foto_laporan'] = $path;
        }

        $astekpam->update($data);

        Log::info('Laporan Astekpam ID: ' . $astekpam->id . ' telah diedit oleh Admin: ' . auth()->user()->name);

        return redirect()->route('astekpam.index')->with('success', 'Laporan berhasil diperbarui oleh Admin!');
    }

    /**
     * Menghapus laporan (Khusus Admin)
     */
    public function destroy(Astekpam $astekpam)
    {
        if ($astekpam->foto_laporan) {
            Storage::disk('public')->delete($astekpam->foto_laporan);
        }

        $astekpam->delete();

        Log::info('Laporan Astekpam ID: ' . $astekpam->id . ' telah dihapus oleh Admin: ' . auth()->user()->name);

        return redirect()->back()->with('success', 'Laporan berhasil dihapus secara permanen!');
    }

    public function download(Request $request)
    {
        $query = Astekpam::with('user')->latest();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        $laporanData = $query->get();

        return response()->json([
            'message' => 'Data berhasil difilter',
            'rentang' => $request->start_date . ' s/d ' . $request->end_date,
            'total' => $laporanData->count(),
            'data' => $laporanData
        ]);
    }

    // =========================================================================
    // FUNGSI BANTUAN UNTUK MEMBENTUK TEKS WHATSAPP & API FONNTE
    // =========================================================================
    private function generatePesanLaporan($data)
    {
        Carbon::setLocale('id');
        $tanggalIndo = Carbon::parse($data->tanggal)->translatedFormat('l, d F Y');
        
        $pesan = "";
        
        $pesan .= "*ASTEKPAM LAPAS KELAS I PALEMBANG*\n\n";
        $pesan .= "Assalamu’alaikum Warahmatullahi Wabarakatuh\n";
        $pesan .= "Selamat " . ($data->ke_shift ?? '-') . "....\n\n";
        
        $pesan .= "Hari/Tgl : *" . $tanggalIndo . "*\n";
        $pesan .= "Pukul    : *" . $data->pukul . " WIB*\n\n";
        
        $pesan .= "Berikut, ASTEKPAM dari *" . ($data->dari_rupam ?? '-') . "* (Shift " . ($data->dari_shift ?? '-') . ") ke *" . ($data->ke_rupam ?? '-') . "* (Shift " . ($data->ke_shift ?? '-') . ") Dipimpin oleh *" . ($data->pimpinan ?? '-') . "* berjalan aman dan tertib.\n\n";
        
        $pesan .= "Dengan rincian sebagai berikut :\n\n";
        
        $pesan .= "*A. JUMLAH PENGHUNI*\n";
        $pesan .= "1. Kapasitas  : " . ($data->kapasitas ?? 0) . " Org\n";
        $pesan .= "2. Narapidana : " . ($data->narapidana ?? 0) . " Org\n";
        $pesan .= "3. Isi Blok Hunian :\n";
        $pesan .= "   - Blok A         : " . ($data->blok_a ?? 0) . " Org\n";
        $pesan .= "   - Blok B         : " . ($data->blok_b ?? 0) . " Org\n";
        $pesan .= "   - Dapur          : " . ($data->dapur ?? 0) . " Org\n";
        $pesan .= "   - Klinik         : " . ($data->klinik ?? 0) . " Org\n";
        $pesan .= "   - Di Dalam Lapas : " . ($data->dalam_lapas ?? 0) . " Org\n";
        $pesan .= "   - Di Luar Lapas  : " . ($data->luar_lapas ?? 0) . " Org\n\n";

        $pesan .= "4. Keterangan Luar :\n";
        $pesan .= "   - Rawat Inap RS : " . $this->formatJsonArray($data->rawat_inap_items) . "\n";
        $pesan .= "   - Berobat RS      : " . $this->formatJsonArray($data->berobat_items) . "\n";
        $pesan .= "   - Lain-lain (Bon): " . $this->formatJsonArray($data->bon_luar_items) . "\n\n";
        
        $pesan .= "*5. Total Jumlah WBP : " . ($data->total_wbp ?? 0) . " Org*\n\n";

        $pesan .= "*B. PERSONIL PENGAMANAN*\n";
        $pesan .= "1. *" . ($data->rupam_pilihan ?? 'Rupam -') . "*\n";
        $pesan .= "   - Jumlah          : " . ($data->rupam_jumlah ?? 0) . " Org\n";
        $pesan .= "   - Hadir             : " . ($data->rupam_hadir ?? 0) . " Org\n";
        $kurangRupam = (int)($data->rupam_jumlah ?? 0) - (int)($data->rupam_hadir ?? 0);
        $pesan .= "   - Tidak Hadir  : " . ($kurangRupam > 0 ? $kurangRupam . " Org" : "-") . "\n";
        $pesan .= "   - Keterangan   : " . ($data->rupam_keterangan ?: '-') . "\n\n";

        $pesan .= "2. *SATGAS P2U*\n";
        $pesan .= "   - Jumlah         : " . ($data->p2u_jumlah ?? 0) . " Org\n";
        $pesan .= "   - Hadir            : " . ($data->p2u_hadir ?? 0) . " Org\n";
        $pesan .= "   - Keterangan  : " . ($data->p2u_keterangan ?: '-') . "\n\n";

        $tugas = is_string($data->tugas) ? json_decode($data->tugas, true) : $data->tugas;
        
        if ($tugas && is_array($tugas)) {
            $pesan .= "*3. Pembagian Tugas :*\n";
            $pesan .= "a. Ka. Rupam : " . ($tugas['ka_rupam'] ?? '-') . "\n";
            $pesan .= "   Wakarupam : " . ($tugas['wakarupam'] ?? '-') . "\n\n";

            $pesan .= "b. Petugas P2U :\n";
            $pesan .= "   - Kasatgas    : " . ($tugas['kasatgas_p2u'] ?? '-') . "\n";
            $pesan .= "   - Wakasatgas : " . ($tugas['wakasatgas_p2u'] ?? '-') . "\n\n";
            
            $pesan .= "c. Petugas Blok :\n";
            $pesan .= "   - Blok A : " . $this->formatJamTugas($tugas['blok_a'] ?? []) . "\n";
            $pesan .= "   - Blok B : " . $this->formatJamTugas($tugas['blok_b'] ?? []) . "\n\n";
            
            $pesan .= "d. Petugas Pos Atas :\n";
            $pesan .= "   - Menara 1 : " . $this->formatJamTugas($tugas['menara_1'] ?? []) . "\n";
            $pesan .= "   - Menara 2 : " . $this->formatJamTugas($tugas['menara_2'] ?? []) . "\n";
            $pesan .= "   - Menara 3 : " . $this->formatJamTugas($tugas['menara_3'] ?? []) . "\n";
            $pesan .= "   - Menara 4 : " . $this->formatJamTugas($tugas['menara_4'] ?? []) . "\n\n";

            $pesan .= "e. Jaga RS         : " . ($tugas['jaga_rs'] ?? '-') . "\n\n";
            $pesan .= "f. Piket Dapur     : " . ($tugas['piket_dapur'] ?? '-') . "\n\n";
            $pesan .= "g. Pengawas Piket  : " . ($tugas['perwira_piket'] ?? '-') . "\n\n";
            $pesan .= "h. Perwira Piket   : " . ($tugas['perwira_kontrol'] ?? '-') . "\n\n";
            $pesan .= "i. Banjaga         : " . ($tugas['banjaga'] ?? '-') . "\n\n";
            $pesan .= "j. Staff KPLP      : " . ($tugas['staff_kplp'] ?? '-') . "\n\n";
            $pesan .= "k. Amanah          : " . ($tugas['amanah'] ?? '-') . "\n\n";
            $pesan .= "l. Petugas Laporan : " . strtoupper($tugas['petugas_laporan'] ?? '-') . "\n";
        }

        $pesan .= "\nDemikian Laporan ini, kami sampaikan dan diucapkan terima kasih.\n\n";
        $pesan .= "Wassalamu'alaikum Warahmatullaahi wabarakaatuh\n";
        $pesan .= "Salam Sejahtera\n";
        $pesan .= "Salam Sehat Selalu…🙏\n\n";

        $user = auth()->user();
        if ($user) {
            $namaPetugas = $user->name;
            $nomorHp = $user->no_hp ?? ''; 
            
            if (str_starts_with($nomorHp, '0')) {
                $nomorHp = '62' . substr($nomorHp, 1);
            }

            $pesan .= "-----------------------------------\n";
            $pesan .= "*Dikirim Oleh:*\n";
            $pesan .= "Nama   : " . $namaPetugas . "\n";
            $pesan .= "No. WA : @" . $nomorHp . "\n\n";
        }

        $pesan .= "*Link Detail Laporan (Website):*\n";
        $pesan .= route('astekpam.show', $data->id) . "\n";

        if (!empty($data->foto_laporan)) {
            $pesan .= "\n*Link Akses Foto (Full):*\n";
            $pesan .= asset('storage/' . $data->foto_laporan) . "\n";
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
        
        $jams = [
            $jamArray['jam_1'] ?? null,
            $jamArray['jam_2'] ?? null,
            $jamArray['jam_3'] ?? null,
        ];

        $validJams = [];

        foreach ($jams as $jam) {
            if (is_array($jam)) {
                $filtered = array_filter($jam, function($val) {
                    return !empty($val) && (string)$val !== '-';
                });
                if (!empty($filtered)) {
                    $validJams[] = implode(' & ', $filtered);
                }
            } else {
                if (!empty($jam) && (string)$jam !== '-') {
                    $validJams[] = $jam;
                }
            }
        }

        if (empty($validJams)) return '-';
        
        return implode(' / ', $validJams);
    }
}