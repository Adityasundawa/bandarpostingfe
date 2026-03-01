<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\Meta\LogController;
use App\Http\Controllers\Admin\Meta\TokenController;
use App\Http\Controllers\Admin\UserController;


use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\XPanelController;
use App\Http\Controllers\Client\MetaPanelController;
use App\Http\Controllers\Client\TiktokPanelController;
use App\Http\Controllers\Client\TelegramPanelController;


use App\Http\Controllers\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Home → redirect ke login jika belum login
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('client.dashboard');
    }

    return redirect()->route('login');
});

// ─────────────────────────────────────────────
// ADMIN ROUTES (Hanya bisa diakses oleh Admin)
// ─────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::prefix('meta')->name('meta.')->group(function () {
            // Token Management
            Route::resource('tokens', TokenController::class)->except(['edit']);
            Route::post('tokens/{id}/toggle', [TokenController::class, 'toggleActive'])->name('tokens.toggle');
            Route::post('tokens/{id}/sessions', [TokenController::class, 'assignSessions'])->name('tokens.sessions.assign');
            Route::post('tokens/{id}/sessions/revoke', [TokenController::class, 'revokeSession'])->name('tokens.sessions.revoke');
            // Log Management
            Route::get('logs', [LogController::class, 'index'])->name('logs.index');
        });
    });

// ─────────────────────────────────────────────
// CLIENT ROUTES (Hanya bisa diakses oleh Client)
// ─────────────────────────────────────────────
Route::prefix('client-area')
    ->name('client.')
    ->middleware(['auth', 'verified', 'client'])
    ->group(function () {

        // Dashboard utama client
        Route::get('/', [ClientDashboard::class, 'index'])->name('dashboard');

        // Meta (Facebook/IG) Panel
        Route::prefix('meta')->name('meta.')->group(function () {
            Route::get('/setup', [ClientDashboard::class, 'setup'])->name('setup');
            Route::post('/setup', [MetaPanelController::class, 'storeToken'])->name('setup.store');
            Route::get('/verify', [MetaPanelController::class, 'verifyToken'])->name('verify');
            Route::post('/login-cookies', [MetaPanelController::class, 'loginCookies'])->name('login-cookies');
            Route::get('/', [MetaPanelController::class, 'index'])->name('index');
        });

        // X (Twitter) Panel
        Route::prefix('x')->name('x.')->group(function () {
            Route::get('/', [XPanelController::class, 'index'])->name('index');
        });

        // Tiktok Panel
        Route::prefix('tiktok')->name('tiktok.')->group(function () {
            Route::get('/', [TiktokPanelController::class, 'index'])->name('index');
        });

        // Telegram Panel
        Route::prefix('telegram')->name('telegram.')->group(function () {
            Route::get('/', [TelegramPanelController::class, 'index'])->name('index');
        });
    });
// ─────────────────────────────────────────────
// PROFILE ROUTES (Untuk semua user yang login)
// ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/blank', [AdminDashboard::class, 'blank'])->name('dashboard');

// Auth routes dari Laravel Breeze (login, register, dll)
require __DIR__.'/auth.php';
