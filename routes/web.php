<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ReportController; // Asumsi controller laporan
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Halaman Welcome
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Dashboard Umum (Bisa diakses semua yang login)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Group Route yang butuh Login
Route::middleware('auth')->group(function () {
    
    // --- FITUR LAPORAN ASTEKPAM ---
    Route::resource('reports', ReportController::class);
    
    // Contoh Route Khusus yang butuh permission spesifik (misal: Verifikasi Laporan)
    Route::post('/reports/{id}/verify', [ReportController::class, 'verify'])
        ->middleware('permission:verify reports')
        ->name('reports.verify');

    // --- FITUR ADMIN (Management User & Role) ---
    // Hanya user dengan role 'admin' yang bisa masuk grup ini
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // --- PROFILE MANAGEMENT (Bawaan Breeze) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';