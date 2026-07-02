<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AstekpamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KuisionerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PejabatController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // =======================================================
    // AKSES TERBUKA (Bisa diakses Admin, Petugas, dan Pejabat)
    // =======================================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/astekpam', [AstekpamController::class, 'index'])->name('astekpam.index');
    Route::get('/astekpam/download', [AstekpamController::class, 'download'])->name('astekpam.download');
    Route::resource('pejabat', PejabatController::class)->only(['index', 'store', 'destroy']);

    // =======================================================
    // AKSES INPUT (Hanya Admin dan Petugas, Pejabat DITOLAK)
    // Rute Create HARUS DI ATAS rute {astekpam}
    // =======================================================
    Route::middleware('role:admin|user')->group(function () {
        Route::get('/astekpam/create', [AstekpamController::class, 'create'])->name('astekpam.create');
        Route::post('/astekpam', [AstekpamController::class, 'store'])->name('astekpam.store');
        
        Route::get('/kuisioner', [KuisionerController::class, 'fillForm'])->name('kuisioner.fill');
        Route::post('/kuisioner', [KuisionerController::class, 'submitForm'])->name('kuisioner.submit');
    });

    // =======================================================
    // RUTE PARAMETER DINAMIS (HARUS DI BAWAH CREATE)
    // =======================================================
    Route::get('/astekpam/{astekpam}', [AstekpamController::class, 'show'])->name('astekpam.show');

    // =======================================================
    // AKSES SUPER ADMIN (Petugas & Pejabat DITOLAK)
    // =======================================================
    Route::middleware('role:admin')->group(function () {
        
        // Pengaturan Kuisioner
        Route::get('/admin/kuisioner/pertanyaan', [KuisionerController::class, 'adminQuestions'])->name('admin.kuisioner.questions');
        Route::post('/admin/kuisioner/pertanyaan', [KuisionerController::class, 'storeQuestion'])->name('admin.kuisioner.store');
        Route::put('/admin/kuisioner/pertanyaan/{question}', [KuisionerController::class, 'updateQuestion'])->name('admin.kuisioner.update');
        Route::delete('/admin/kuisioner/pertanyaan/{question}', [KuisionerController::class, 'destroyQuestion'])->name('admin.kuisioner.destroy');
        Route::get('/admin/kuisioner/hasil', [KuisionerController::class, 'adminResults'])->name('admin.kuisioner.results');
        Route::get('/admin/kuisioner/hasil/export', [KuisionerController::class, 'exportCsv'])->name('admin.kuisioner.export');
        Route::post('/admin/kuisioner/toggle-status', [KuisionerController::class, 'toggleStatus'])->name('admin.kuisioner.toggle');

        // Manajemen Pengguna & Edit Laporan Khusus Admin
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

            Route::get('/astekpam/{astekpam}/edit', [AstekpamController::class, 'edit'])->name('astekpam.edit');
            Route::put('/astekpam/{astekpam}', [AstekpamController::class, 'update'])->name('astekpam.update');
        });
    });

    // --- PROFILE MANAGEMENT ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';