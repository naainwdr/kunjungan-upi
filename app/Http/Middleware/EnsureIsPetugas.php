<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsPetugas
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isPetugas()) {
            Auth::logout();
            return redirect()->route('petugas.login')
                ->withErrors(['email' => 'Akses ditolak. Halaman ini hanya untuk Petugas Presensi.']);
        }

        return $next($request);
    }
}
