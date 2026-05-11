<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = $validated['login'];
        $field = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : (is_numeric($login) ? 'nis' : 'username');

        if (! Auth::attempt([$field => $login, 'password' => $validated['password']], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => 'Username/NIS/email atau password tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();

        ActivityLog::record('login', 'User berhasil login.', request: $request);

        return $request->user()->isAdmin()
            ? redirect()->intended(route('admin.dashboard'))
            : redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
