<?php

namespace App\Http\Controllers;

use App\Models\Pejabat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PejabatController extends Controller
{
    public function index()
    {
        $pejabats = Pejabat::latest()->get();
        return Inertia::render('Pejabat/Index', [
            'pejabats' => $pejabats
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'kategori' => 'required|in:Pengawas Piket,Perwira Piket,Staff KPLP',
        ]);

        Pejabat::create($request->all());
        return redirect()->back()->with('message', 'Data berhasil ditambahkan');
    }

    public function destroy(Pejabat $pejabat)
    {
        $pejabat->delete();
        return redirect()->back()->with('message', 'Data berhasil dihapus');
    }
}