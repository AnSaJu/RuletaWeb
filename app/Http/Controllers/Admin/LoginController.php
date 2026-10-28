<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the administrator login form.
     */
    public function create(): View
    {
        return view('admin.login');
    }

    /**
     * Authenticate an administrator.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Ingresa tu correo.',
            'email.email' => 'Ingresa un correo válido.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        if (! Auth::attempt([...$credentials, 'is_admin' => true], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no son correctas.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.participantes.index'));
    }

    /**
     * Log the administrator out.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
