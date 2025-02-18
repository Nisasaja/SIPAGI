<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AccessLog; // Buat model AccessLog

class LogAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log akses jika pengguna login
        if ($request->user()) {
            AccessLog::create([
                'user_id' => $request->user()->id,
                'accessed_at' => now(),
            ]);
        }

        return $next($request);
    }
}
