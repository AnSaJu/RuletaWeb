@extends('admin.panel')

@section('title', 'Usuarios')

@section('activa', 'usuarios')

@section('contenido')

    <div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Usuarios del panel</h1>
                <p class="mt-1 text-slate-400">Quiénes pueden entrar a la administración y qué pueden hacer.</p>
            </div>

            <a href="{{ route('admin.usuarios.create') }}"
               class="rounded-xl bg-gradient-to-r from-amber-400 to-fuchsia-500 px-5 py-2.5 text-center font-semibold text-slate-950 shadow-lg transition hover:brightness-110">
                Nuevo usuario
            </a>
        </div>

        @if (session('estado'))
            <div role="status" class="mt-6 rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-emerald-300">
                {{ session('estado') }}
            </div>
        @endif

        @if (session('error'))
            <div role="alert" class="mt-6 rounded-xl border border-rose-400/40 bg-rose-500/10 px-4 py-3 text-rose-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="mt-8 overflow-x-auto rounded-2xl border border-white/10 bg-white/5">
            <table class="w-full min-w-[40rem] text-left text-sm">
                <thead class="border-b border-white/10 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th scope="col" class="px-5 py-3">Nombre</th>
                        <th scope="col" class="px-5 py-3">Correo</th>
                        <th scope="col" class="px-5 py-3">Rol</th>
                        <th scope="col" class="px-5 py-3">Creado</th>
                        <th scope="col" class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($usuarios as $usuario)
                        <tr class="transition hover:bg-white/5">
                            <td class="px-5 py-3 font-medium">
                                {{ $usuario->name }}
                                @if ($usuario->is(auth()->user()))
                                    <span class="ml-2 rounded-full bg-white/10 px-2 py-0.5 text-xs text-slate-300">Tú</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-slate-300">{{ $usuario->email }}</td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="rounded-full px-3 py-1 {{ $usuario->esSuperAdmin() ? 'bg-amber-400/15 text-amber-300' : 'bg-fuchsia-500/15 text-fuchsia-300' }}">
                                    {{ $usuario->rol->etiqueta() }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-400">
                                {{ $usuario->created_at->timezone('America/Bogota')->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}"
                                       class="rounded-lg border border-white/15 px-3 py-1.5 text-slate-200 transition hover:bg-white/10">
                                        Editar
                                    </a>

                                    @unless ($usuario->is(auth()->user()))
                                        <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}"
                                              onsubmit="return confirm('¿Eliminar a {{ e($usuario->name) }}? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg border border-rose-400/30 px-3 py-1.5 text-rose-300 transition hover:bg-rose-500/10">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
