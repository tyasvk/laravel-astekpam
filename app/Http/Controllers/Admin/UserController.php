<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->get();
        return Inertia::render('Admin/UserIndex', ['users' => $users]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nip' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,user,pejabat',
            'regu' => ['nullable', Rule::in(['I', 'II', 'III', 'IV'])],
            'jabatan' => ['nullable', Rule::in([
                'Karupam I', 'Karupam II', 'Karupam III', 'Karupam IV',
                'Wakarupam I', 'Wakarupam II', 'Wakarupam III', 'Wakarupam IV',
                'Kasatgas I', 'Kasatgas II', 'Kasatgas III', 'Kasatgas IV',
                'Wakasatgas I', 'Wakasatgas II', 'Wakasatgas III', 'Wakasatgas IV',
                'Anggota I', 'Anggota II', 'Anggota III', 'Anggota IV'
            ])],
        ]);

        $validated['password'] = bcrypt($validated['password']);
        
        $role = $validated['role'];
        unset($validated['role']);

        $user = User::create($validated);
        $user->syncRoles([$role]);

        return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nip' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:admin,user,pejabat',
            'regu' => ['nullable', Rule::in(['I', 'II', 'III', 'IV'])],
            'jabatan' => ['nullable', Rule::in([
                'Karupam I', 'Karupam II', 'Karupam III', 'Karupam IV',
                'Wakarupam I', 'Wakarupam II', 'Wakarupam III', 'Wakarupam IV',
                'Kasatgas I', 'Kasatgas II', 'Kasatgas III', 'Kasatgas IV',
                'Wakasatgas I', 'Wakasatgas II', 'Wakasatgas III', 'Wakasatgas IV',
                'Anggota I', 'Anggota II', 'Anggota III', 'Anggota IV'
            ])],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $role = $validated['role'];
        unset($validated['role']);

        $user->update($validated);
        $user->syncRoles([$role]);

        return redirect()->back()->with('success', 'Pengguna berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'Pengguna berhasil dihapus');
    }
}