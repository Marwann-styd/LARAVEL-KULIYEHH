<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    // Memproses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cari akun berdasarkan email
        $user = User::where('email', $credentials['email'])->first();

        // Periksa email dan password
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password yang Anda masukkan salah.',
                ])
                ->onlyInput('email');
        }

        // Periksa status akun
        if (!$user->is_active) {
            return back()
                ->withErrors([
                    'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Super Admin.',
                ])
                ->onlyInput('email');
        }

        // Login berhasil
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
