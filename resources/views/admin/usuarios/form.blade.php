@extends('admin.panel')

@section('title', $usuario ? 'Editar usuario' : 'Nuevo usuario')

@section('activa', 'usuarios')

@section('contenido')

    @php
        $campo = 'mt-1 block w-full rounded-xl border bg-slate-900/70 px-4 py-2.5 text-slate-100 outline-none transition focus:ring-2 focus:ring-fuchsia-400';
        $rolActual = old('rol', $usuario?->rol?->value);
    @endphp

    <div class="max-w-2xl">
        <a href="{{ route('admin.usuarios.index') }}" class="text-sm text-slate-400 transition hover:text-slate-200">← Volver a usuarios</a>

        <h1 class="mt-3 text-3xl font-bold tracking-tight">{{ $usuario ? 'Editar usuario' : 'Nuevo usuario' }}</h1>

        <form method="POST" novalidate
              action="{{ $usuario ? route('admin.usuarios.update', $usuario) : route('admin.usuarios.store') }}"
              class="mt-8 space-y-6 rounded-2xl border border-white/10 bg-white/5 p-6">
            @csrf
            @if ($usuario)
                @method('PUT')
            @endif

            <div>
                <label for="name" class="text-sm font-medium text-slate-200">Nombre</label>
                <input id="name" name="name" type="text" value="{{ old('name', $usuario?->name) }}" required autocomplete="off"
                       class="{{ $campo }} {{ $errors->has('name') ? 'border-rose-500' : 'border-white/10' }}">
                @error('name')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="text-sm font-medium text-slate-200">Correo</label>
                <input id="email" name="email" type="email" value="{{ old('email', $usuario?->email) }}" required autocomplete="off"
                       class="{{ $campo }} {{ $errors->has('email') ? 'border-rose-500' : 'border-white/10' }}">
                @error('email')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
            </div>

            <fieldset>
                <legend class="text-sm font-medium text-slate-200">Rol</legend>
                <div class="mt-2 space-y-3">
                    @foreach ($roles as $rol)
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition has-[:checked]:border-fuchsia-400 has-[:checked]:bg-fuchsia-500/10 {{ $errors->has('rol') ? 'border-rose-500' : 'border-white/10' }}">
                            <input type="radio" name="rol" value="{{ $rol->value }}" class="mt-1" @checked($rolActual === $rol->value) required>
                            <span>
                                <span class="block font-medium">{{ $rol->etiqueta() }}</span>
                                <span class="block text-sm text-slate-400">{{ $rol->descripcion() }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('rol')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
            </fieldset>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="password" class="text-sm font-medium text-slate-200">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" @required(! $usuario)
                           class="{{ $campo }} {{ $errors->has('password') ? 'border-rose-500' : 'border-white/10' }}">
                    @error('password')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="text-sm font-medium text-slate-200">Confirmar contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" @required(! $usuario)
                           class="{{ $campo }} border-white/10">
                </div>
            </div>
            <p class="-mt-3 text-sm text-slate-400">
                {{ $usuario ? 'Déjala vacía para conservar la contraseña actual. ' : '' }}Mínimo 8 caracteres.
            </p>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="rounded-xl bg-gradient-to-r from-amber-400 to-fuchsia-500 px-8 py-3 font-semibold text-slate-950 shadow-lg transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-amber-300">
                    {{ $usuario ? 'Guardar cambios' : 'Crear usuario' }}
                </button>
                <a href="{{ route('admin.usuarios.index') }}" class="rounded-xl border border-white/15 px-6 py-3 text-slate-200 transition hover:bg-white/10">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
