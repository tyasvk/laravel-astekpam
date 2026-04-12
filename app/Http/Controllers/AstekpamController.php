<?php

namespace App\Http\Controllers;

use App\Models\AstekpamReport;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AstekpamController extends Controller
{
    public function create()
    {
        return Inertia::render('Astekpam/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required|string',
            'dari_rupam' => 'required',
            'ke_rupam' => 'required',
        ]);

        AstekpamReport::create([
            'user_id' => auth()->id(),
            ...$request->all()
        ]);

        return redirect()->route('dashboard')->with('message', 'Laporan Astekpam berhasil dikirim!');
    }
}