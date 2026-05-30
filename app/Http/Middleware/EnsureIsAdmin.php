<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Akses ditolak. Halaman ini hanya untuk Admin.']);
        }

        return $next($request);
    }
}
