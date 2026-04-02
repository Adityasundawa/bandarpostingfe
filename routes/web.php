<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\Meta\LogController;
use App\Http\Controllers\Admin\Meta\TokenController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\Client\FileManagerController;
use App\Http\Controllers\Client\MetaPanelController;
use App\Http\Controllers\Client\TelegramPanelController;
use App\Http\Controllers\Client\TiktokPanelController;
use App\Http\Controllers\Client\XPanelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicFileController;
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
        Route::get('/files', [FileManagerController::class, 'adminIndex'])->name('files.index');

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

        // File  Panel
        Route::prefix('files')->name('files.')->group(function () {
            Route::get('/', [FileManagerController::class, 'index'])->name('index');
            Route::get('/folder-contents', [FileManagerController::class, 'getFolderContents'])->name('folder.contents');
            Route::post('/upload', [FileManagerController::class, 'upload'])->name('upload');
            Route::get('/download/{file}', [FileManagerController::class, 'download'])->name('download');
            Route::delete('/file/{file}', [FileManagerController::class, 'destroy'])->name('destroy');
            Route::patch('/file/{file}', [FileManagerController::class, 'update'])->name('update');
            Route::post('/file/{file}/toggle-public', [FileManagerController::class, 'togglePublic'])->name('toggle.public');
            Route::post('/folder', [FileManagerController::class, 'createFolder'])->name('folder.create');
            Route::delete('/folder/{folder}', [FileManagerController::class, 'destroyFolder'])->name('folder.destroy');
        });

        // Meta (Facebook/IG) Panel
        Route::prefix('meta')->name('meta.')->group(function () {
            Route::get('/setup', [ClientDashboard::class, 'setup'])->name('setup');
            Route::post('/setup', [MetaPanelController::class, 'storeToken'])->name('setup.store');
            Route::get('/verify', [MetaPanelController::class, 'verifyToken'])->name('verify');
            Route::post('/login-cookies', [MetaPanelController::class, 'loginCookies'])->name('login-cookies');
            Route::get('/', [MetaPanelController::class, 'index'])->name('index');
            // Asset Management Meta
            Route::get('/assets', [MetaPanelController::class, 'assets'])->name('assets.index');
            Route::post('/assets/sync', [MetaPanelController::class, 'syncAssets'])->name('assets.sync');
            Route::delete('/assets/{id}', [MetaPanelController::class, 'deleteAsset'])->name('assets.delete');
            Route::get('/assets-by-session', [MetaPanelController::class, 'assetsBySession'])->name('assets.by-session');
            // Post Management Meta
            Route::get('/posts-by-asset', [MetaPanelController::class, 'postsByAsset'])->name('posts.by-asset');
            Route::post('/posts/sync', [MetaPanelController::class, 'syncPosts'])->name('posts.sync');

            // Schedule Post
            Route::post('/schedule', [MetaPanelController::class, 'schedulePost'])->name('schedule');
            Route::get('/queue-status', [MetaPanelController::class, 'checkQueueStatus'])->name('queue.status');

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

Route::get('/files/{token}/{filename}', [PublicFileController::class, 'serve'])
    ->name('files.public')
    ->where('filename', '.*');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/blank', [AdminDashboard::class, 'blank'])->name('dashboard');

// Auth routes dari Laravel Breeze (login, register, dll)
require __DIR__.'/auth.php';
