<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman edit profil.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'status' => session('status'),
            'user' => $request->user(), // Mengirimkan data user secara langsung agar terbaca utuh oleh frontend
        ]);
    }

    /**
     * Memperbarui informasi data profil user.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Mengizinkan semua user memperbarui data personal & kedinasan mereka sendiri
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp' => 'nullable|string|max:30',
            'nip' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'regu' => ['nullable', Rule::in(['I', 'II', 'III', 'IV'])],
            'jabatan' => ['nullable', Rule::in([
                'Karupam I', 'Karupam II', 'Karupam III', 'Karupam IV',
                'Wakarupam I', 'Wakarupam II', 'Wakarupam III', 'Wakarupam IV',
                'Kasatgas I', 'Kasatgas II', 'Kasatgas III', 'Kasatgas IV',
                'Wakasatgas I', 'Wakasatgas II', 'Wakasatgas III', 'Wakasatgas IV',
                'Anggota I', 'Anggota II', 'Anggota III', 'Anggota IV'
            ])],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
}