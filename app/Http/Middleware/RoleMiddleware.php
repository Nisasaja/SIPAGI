<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            // Jika pengguna belum login, arahkan ke halaman login
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Periksa apakah peran pengguna ada dalam daftar peran yang diizinkan
        if (!in_array($user->role, $roles)) {
            // Jika tidak sesuai, arahkan ke halaman tidak diizinkan
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Jika role sesuai, lanjutkan permintaan
        return $next($request);
    }

    // public function handle($request, Closure $next, $role)
    // {
    //     // Pastikan user terautentikasi sebelum memeriksa peran
    //     if (!auth()->check() || !auth()->user()->hasRole($role)) {
    //         abort(403, 'Unauthorized.');
    //     }

    //     return $next($request);
    // }

}
