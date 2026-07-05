<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Menampilkan halaman login
    public function index()
    {
        return view('auth.login');
    }

    // Memproses request login
    public function authenticate(Request $request)
    {
        // Validasi input form
        $credentials = $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);

        // Proses autentikasi session
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect ke dashboard utama
            return redirect()->intended('/dashboard');
        }

        // Jika gagal, balikkan ke halaman login dengan error
        return back()->withErrors([
            'nim' => 'NIM atau Password yang Anda masukkan salah.',
        ])->onlyInput('nim');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/dashboard');
    }
}
