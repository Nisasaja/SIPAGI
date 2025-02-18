<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        $judul = "Login ke Aplikasi";
        Log::info('Menampilkan form login dengan judul: ' . $judul); // Log untuk debugging
        return view('auth.login', compact('judul'));
    }

    /**
     * Proses login.
     */

     public function login(Request $request)
     {
         // Validasi input
         $credentials = $request->validate([
             'username' => ['required'],
             'password' => ['required'],
         ]);
     
         // Coba autentikasi menggunakan username dan password
         if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->filled('remember'))) {
             $request->session()->regenerate();
     
             // Arahkan pengguna berdasarkan peran mereka
             $user = Auth::user();
             if ($user->role === 'Admin') {
                 return redirect()->intended('/admin');
             } elseif ($user->role === 'Kader') {
                 return redirect()->intended('/kader');
             } elseif ($user->role === 'Manager') {
                 return redirect()->intended('/manager');
             } else {
                 Auth::logout();
                 return redirect()->route('login')->withErrors([
                     'username' => 'Role pengguna tidak valid.',
                 ]);
             }
         }
     
         // Jika autentikasi gagal
         return back()->withErrors([
             'username' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
         ])->onlyInput('username');
     }
    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
