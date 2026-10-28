<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UsuarioRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * List the users that can enter the panel.
     */
    public function index(): View
    {
        return view('admin.usuarios.index', [
            'usuarios' => User::whereNotNull('rol')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.usuarios.form', ['usuario' => null, 'roles' => Rol::cases()]);
    }

    public function store(UsuarioRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        (new User)->forceFill([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'rol' => $datos['rol'],
            'email_verified_at' => now(),
        ])->save();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('estado', "El usuario {$datos['name']} se creó correctamente.");
    }

    public function edit(User $usuario): View
    {
        return view('admin.usuarios.form', ['usuario' => $usuario, 'roles' => Rol::cases()]);
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        $datos = $request->validated();

        if ($this->esElUnicoSuperAdmin($usuario) && $datos['rol'] !== Rol::SuperAdmin->value) {
            return back()
                ->withInput()
                ->withErrors(['rol' => 'Debe quedar al menos un super administrador.']);
        }

        $usuario->forceFill([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'rol' => $datos['rol'],
        ]);

        if (filled($datos['password'] ?? null)) {
            $usuario->password = $datos['password'];
        }

        $usuario->save();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('estado', "El usuario {$usuario->name} se actualizó correctamente.");
    }

    public function destroy(Request $request, User $usuario): RedirectResponse
    {
        if ($usuario->is($request->user())) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        if ($this->esElUnicoSuperAdmin($usuario)) {
            return back()->with('error', 'Debe quedar al menos un super administrador.');
        }

        $usuario->delete();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('estado', "El usuario {$usuario->name} se eliminó.");
    }

    private function esElUnicoSuperAdmin(User $usuario): bool
    {
        return $usuario->esSuperAdmin()
            && User::where('rol', Rol::SuperAdmin->value)->count() === 1;
    }
}
