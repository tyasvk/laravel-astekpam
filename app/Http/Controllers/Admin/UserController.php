<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna.
     */
    public function index()
    {
        $users = User::latest()->get();

        return Inertia::render('Admin/UserIndex', [
            'users' => $users,
        ]);
    }

    /**
     * Menyimpan pengguna baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nip' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'regu' => ['required', Rule::in(['I', 'II', 'III', 'IV'])],
            'jabatan' => ['required', Rule::in([
    'Karupam I', 'Karupam II', 'Karupam III', 'Karupam IV',
    'Wakarupam I', 'Wakarupam II', 'Wakarupam III', 'Wakarupam IV',
    'Anggota I', 'Anggota II', 'Anggota III', 'Anggota IV' // <--- INI YANG DIUBAH
])],
        ]);

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan');
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nip' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'regu' => ['required', Rule::in(['I', 'II', 'III', 'IV'])],
            'jabatan' => ['required', Rule::in([
    'Karupam I', 'Karupam II', 'Karupam III', 'Karupam IV',
    'Wakarupam I', 'Wakarupam II', 'Wakarupam III', 'Wakarupam IV',
    'Anggota I', 'Anggota II', 'Anggota III', 'Anggota IV' // <--- INI YANG DIUBAH
])],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Pengguna berhasil diperbarui');
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'Pengguna berhasil dihapus');
    }
}