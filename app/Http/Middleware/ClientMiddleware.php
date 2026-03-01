<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     * Hanya izinkan user dengan role Client (role = 2)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek apakah user adalah Client
        if (auth()->user()->role !== User::ROLE_CLIENT) {
            // Jika bukan client, redirect ke halaman yang sesuai rolenya
            if (auth()->user()->role === User::ROLE_ADMIN) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Akses ditolak. Halaman ini khusus untuk Client.');
            }

            abort(403, 'Akses Ditolak.');
        }

        return $next($request);
    }
}
