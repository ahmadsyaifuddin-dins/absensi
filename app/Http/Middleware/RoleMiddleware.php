<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek dulu apakah pengguna sudah login atau belum.
        if (!Auth::check()) {
            return redirect('login');
        }

        // Ambil role dari pengguna yang sedang login.
        $userRole = Auth::user()->role;

        // Cek apakah role pengguna ada di dalam daftar role yang diizinkan ($roles).
        if (in_array($userRole, $roles)) {
            // Jika cocok, izinkan pengguna melanjutkan request.
            return $next($request);
        }

        // Jika tidak cocok, tolak akses (Forbidden).
        abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
    }
}