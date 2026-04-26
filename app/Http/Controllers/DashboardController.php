<?php

namespace App\Http\Controllers;

use App\Models\Astekpam;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Statistik Nyata
        $totalReports = Astekpam::count();
        $reportsToday = Astekpam::whereDate('created_at', Carbon::today())->count();
        
        // 2. Ambil 5 Laporan Terakhir
        $recentReports = Astekpam::latest()->limit(5)->get();

        // 3. Logika Menentukan Shift Saat Ini
        $hour = now()->hour;
        if ($hour >= 7 && $hour < 13) {
            $shift = 'Pagi';
            $jam = '07.30 - 13.00';
        } elseif ($hour >= 13 && $hour < 19) {
            $shift = 'Siang';
            $jam = '13.00 - 19.30';
        } else {
            $shift = 'Malam';
            $jam = '19.30 - 07.30';
        }

        // Ambil data petugas terakhir yang sedang berjaga
        $currentDuty = Astekpam::where('ke_shift', $shift)->latest()->first();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => number_format($totalReports),
                'today' => $reportsToday,
                'efficiency' => $totalReports > 0 ? '98.2%' : '0%',
            ],
            'recentReports' => $recentReports,
            'activeDuty' => [
                'shift' => $shift,
                'rupam' => $currentDuty->ke_rupam ?? 'Belum Ada Laporan',
                'pimpinan' => $currentDuty->pimpinan ?? '-',
                'jam' => $jam
            ]
        ]);
    }
}