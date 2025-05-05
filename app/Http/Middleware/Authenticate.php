<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (in_array($user->role, ['Admin', 'Kader', 'Manager'])) {
                return $next($request);
            }
        }

        return redirect()->route('landingpage')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}
