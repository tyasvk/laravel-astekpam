<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia; 
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'nip' => 'required|string|max:30|unique:'.User::class,
            'regu_pengamanan' => 'required|string|in:Rupam I,Rupam II,Rupam III,Rupam IV',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'regu_pengamanan' => $request->regu_pengamanan,
            'password' => Hash::make($request->password),
        ]);

        // JIKA SPATIE PERMISSION DIGUNAKAN: 
        // Berikan permission secara otomatis kepada user yang baru mendaftar
        // Hapus tanda komentar (//) di bawah ini jika permission 'create reports' sudah ada di database
        
        // $user->givePermissionTo('create reports');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}