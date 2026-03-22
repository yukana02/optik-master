<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Generate token unik untuk sesi ini
        $token = Str::random(60);

        // Simpan ke session
        session(['session_token' => $token, 'last_activity' => now()->timestamp]);

        // Simpan ke DB (untuk deteksi login ganda dari perangkat lain)
        Auth::user()->update(['session_token' => $token]);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        // Hapus session token dari DB saat logout normal
        if (Auth::check()) {
            Auth::user()->update(['session_token' => null]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
