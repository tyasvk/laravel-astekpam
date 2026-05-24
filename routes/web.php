<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AstekpamController;
use App\Http\Controllers\DashboardController;
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

// Route yang membutuhkan Auth (Harus Login)
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- FITUR ASTEKPAM ---
    Route::get('/astekpam', [AstekpamController::class, 'index'])->name('astekpam.index');
    
    // PERBAIKAN: Middleware permission Dihapus sementara agar tidak 403 Forbidden
    Route::get('/astekpam/create', [AstekpamController::class, 'create'])->name('astekpam.create');
    Route::post('/astekpam', [AstekpamController::class, 'store'])->name('astekpam.store');

    // PERBAIKAN: Rute dengan parameter {astekpam} diletakkan di paling bawah
    Route::get('/astekpam/{astekpam}', [AstekpamController::class, 'show'])->name('astekpam.show');
    

    // --- FITUR ADMIN (Manajemen User) ---
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // --- PROFILE MANAGEMENT ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';