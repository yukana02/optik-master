<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckConcurrentLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user         = auth()->user();
            $sessionToken = session('session_token');

            // Jika token di session tidak cocok dengan yang tersimpan di DB
            // berarti ada login baru dari perangkat lain
            if ($sessionToken && $user->session_token !== $sessionToken) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', '⚠️ Sesi Anda telah berakhir karena akun ini masuk dari perangkat lain. Silakan login kembali.');
            }
        }

        return $next($request);
    }
}
