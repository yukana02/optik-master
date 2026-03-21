<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackActivity
{
    /**
     * Waktu tidak aktif maksimum sebelum auto-logout (dalam menit).
     */
    const TIMEOUT_MINUTES = 30;

    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        // Skip untuk AJAX heartbeat (agar tidak terhitung sebagai "aktivitas" saat JS cek)
        $isHeartbeat = $request->routeIs('heartbeat') || $request->header('X-Heartbeat');

        $lastActivity = session('last_activity');
        $now          = now()->timestamp;

        // Cek timeout
        if ($lastActivity) {
            $idleSeconds = $now - $lastActivity;
            if ($idleSeconds > (self::TIMEOUT_MINUTES * 60)) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['timeout' => true], 401);
                }

                return redirect()->route('login')
                    ->with('error', '⏱️ Sesi Anda telah berakhir karena tidak ada aktivitas selama ' . self::TIMEOUT_MINUTES . ' menit. Silakan login kembali.');
            }
        }

        // Update timestamp aktivitas
        if (!$isHeartbeat) {
            session(['last_activity' => $now]);
        }

        return $next($request);
    }
}
