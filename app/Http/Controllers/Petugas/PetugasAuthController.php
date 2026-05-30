<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isPetugas()
                ? redirect()->route('petugas.scanner')
                : redirect()->route('admin.dashboard');
        }
        return view('petugas.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Arahkan berdasarkan role
            if (Auth::user()->isPetugas()) {
                return redirect()->route('petugas.scanner');
            }

            // Jika bukan petugas (misal: admin), logout dan tolak
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun ini bukan akun Petugas Presensi. Gunakan halaman login Admin.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('petugas.login');
    }
}
