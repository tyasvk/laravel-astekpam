<?php

namespace App\Http\Controllers;

use App\Models\Astekpam;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
       // Ambil 1 data laporan Astekpam paling terakhir/terbaru
        $latestAstekpam = Astekpam::latest()->first();
        // Ambil status kuisioner dari database
        $isKuisionerActive = Setting::where('key', 'kuisioner_active')->first()?->value === '1';

        return Inertia::render('Dashboard', [
            'latestAstekpam' => $latestAstekpam,
            'isKuisionerActive' => $isKuisionerActive, // Kirim ke Vue
        ]);
    }
    public function __invoke()
    {
        // Cek status apakah kuisioner sedang ON (1) atau OFF (0)
        $isKuisionerActive = Setting::where('key', 'kuisioner_status')->value('value') === '1';

        return Inertia::render('Dashboard', [
            'isKuisionerActive' => $isKuisionerActive
        ]);
    }
}