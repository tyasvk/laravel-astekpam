<?php

namespace App\Http\Controllers;

use App\Models\Astekpam;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
       // Ambil 1 data laporan Astekpam paling terakhir/terbaru
        $latestAstekpam = Astekpam::latest()->first();

        return Inertia::render('Dashboard', [
            'latestAstekpam' => $latestAstekpam
        ]);
    }
}