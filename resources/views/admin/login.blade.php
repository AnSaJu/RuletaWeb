@extends('admin.layout')

@section('title', 'Ingreso administrador')

@section('body')
    <main class="relative isolate flex min-h-screen items-center justify-center overflow-hidden px-6 py-16">
        <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
            <div class="absolute -top-40 left-1/2 h-[36rem] w-[36rem] -translate-x-1/2 rounded-full bg-fuchsia-600/30 blur-3xl"></div>
        </div>

        <section class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur">
            <h1 class="text-2xl font-semibold">Panel de administración</h1>
            <p class="mt-1 text-sm text-slate-400">Ingresa con tu cuenta de administrador.</p>

            <form method="POST" action="{{ route('admin.login') }}" class="mt-6 space-y-5" novalidate>
                @csrf

                <div>
                    <label for="email" class="text-sm font-medium text-slate-200">Correo</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus
                           class="mt-1 block w-full rounded-xl border bg-slate-900/70 px-4 py-3 text-slate-100 outline-none transition focus:ring-2 focus:ring-fuchsia-400 {{ $errors->has('email') ? 'border-rose-500' : 'border-white/10' }}">
                    @error('email')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="text-sm font-medium text-slate-200">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="mt-1 block w-full rounded-xl border bg-slate-900/70 px-4 py-3 text-slate-100 outline-none transition focus:ring-2 focus:ring-fuchsia-400 {{ $errors->has('password') ? 'border-rose-500' : 'border-white/10' }}">
                    @error('password')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-white/20 bg-slate-900">
                    Mantener la sesión iniciada
                </label>

                <button type="submit"
                        class="w-full rounded-xl bg-gradient-to-r from-amber-400 to-fuchsia-500 px-6 py-3 text-base font-semibold text-slate-950 shadow-lg transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-amber-300">
                    Ingresar
                </button>
            </form>
        </section>
    </main>
@endsection
