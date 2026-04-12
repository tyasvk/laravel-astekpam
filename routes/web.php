<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AstekpamController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Welcome / Landing Page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Dashboard Utama (Akses untuk semua User yang sudah Login)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grouping Route yang membutuhkan Autentikasi
Route::middleware('auth')->group(function () {

    // --- FITUR ASTEKPAM (Laporan Serah Terima) ---
    // User bisa melihat riwayat laporan
    Route::get('/astekpam', [AstekpamController::class, 'index'])->name('astekpam.index');
    
    // Hanya user dengan permission 'create reports' yang bisa buat laporan
    Route::get('/astekpam/create', [AstekpamController::class, 'create'])
        ->middleware('permission:create reports')
        ->name('astekpam.create');
        
    Route::post('/astekpam', [AstekpamController::class, 'store'])
        ->middleware('permission:create reports')
        ->name('astekpam.store');

    // --- FITUR ADMIN (Manajemen User & Hak Akses) ---
    // Hanya user dengan role 'admin' yang bisa masuk ke area ini
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // --- PROFILE MANAGEMENT (Bawaan Laravel Breeze) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Memuat rute autentikasi bawaan (Login, Register, dll)
require __DIR__.'/auth.php';