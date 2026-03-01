<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Hanya izinkan user dengan role Admin (role = 1)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek apakah user adalah Admin
        if (auth()->user()->role !== User::ROLE_ADMIN) {
            // Jika bukan admin, redirect ke halaman yang sesuai rolenya
            if (auth()->user()->role === User::ROLE_CLIENT) {
                return redirect()->route('client.dashboard')
                    ->with('error', 'Akses ditolak. Halaman ini khusus untuk Admin.');
            }

            abort(403, 'Akses Ditolak.');
        }

        return $next($request);
    }
}
