<?php

namespace App\Http\Controllers;

use App\Models\Astekpam;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil 1 laporan terbaru
        $latestReport = Astekpam::with('user')->latest()->first();
        
        $stats = [
            'total' => Astekpam::count(),
            'today' => Astekpam::whereDate('created_at', Carbon::today())->count(),
        ];

        return Inertia::render('Dashboard', [
            'latestReport' => $latestReport,
            'stats' => $stats
        ]);
    }
}