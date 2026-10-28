<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} · Regístrate y participa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <div class="relative isolate overflow-hidden">
        {{-- Fondo decorativo --}}
        <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
            <div class="absolute -top-40 left-1/2 h-[40rem] w-[40rem] -translate-x-1/2 rounded-full bg-fuchsia-600/30 blur-3xl"></div>
            <div class="absolute -bottom-40 -right-20 h-[30rem] w-[30rem] rounded-full bg-amber-500/20 blur-3xl"></div>
        </div>

        <main class="mx-auto grid min-h-screen max-w-6xl items-center gap-12 px-6 py-16 lg:grid-cols-2">
            {{-- Columna de presentación --}}
            <section class="text-center lg:text-left">
                <span class="inline-flex items-center gap-2 rounded-full border border-amber-400/40 bg-amber-400/10 px-4 py-1 text-sm font-medium text-amber-300">
                    🎡 Gira la ruleta y gana
                </span>
                <h1 class="mt-6 text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                    Regístrate y <span class="bg-gradient-to-r from-amber-300 to-fuchsia-400 bg-clip-text text-transparent">participa</span> en la ruleta
                </h1>
                <p class="mt-6 text-lg text-slate-300">
                    Completa el formulario con tus datos y quedarás inscrito para participar. Solo toma un minuto.
                </p>

                <ul class="mt-8 space-y-3 text-slate-300">
                    <li class="flex items-center justify-center gap-3 lg:justify-start">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">✓</span>
                        Registro rápido y sencillo
                    </li>
                    <li class="flex items-center justify-center gap-3 lg:justify-start">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">✓</span>
                        Una inscripción por cédula
                    </li>
                    <li class="flex items-center justify-center gap-3 lg:justify-start">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">✓</span>
                        Tus datos se guardan de forma segura
                    </li>
                </ul>
            </section>

            {{-- Formulario --}}
            <section class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur">
                <h2 class="text-2xl font-semibold">Tus datos</h2>
                <p class="mt-1 text-sm text-slate-400">Todos los campos son obligatorios.</p>

                @if (session('registrado'))
                    <div role="status" class="mt-6 rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-emerald-300">
                        {{ session('registrado') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('participantes.store') }}" class="mt-6 space-y-5" novalidate>
                    @csrf

                    @php
                        $campo = 'mt-1 block w-full rounded-xl border bg-slate-900/70 px-4 py-3 text-slate-100 placeholder-slate-500 outline-none transition focus:ring-2 focus:ring-fuchsia-400';
                    @endphp

                    <div>
                        <label for="nombre" class="text-sm font-medium text-slate-200">Nombre completo</label>
                        <input id="nombre" name="nombre" type="text" value="{{ old('nombre') }}" autocomplete="name" required
                               placeholder="Ej: María Fernanda López"
                               class="{{ $campo }} {{ $errors->has('nombre') ? 'border-rose-500' : 'border-white/10' }}">
                        @error('nombre')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="cedula" class="text-sm font-medium text-slate-200">Cédula</label>
                        <input id="cedula" name="cedula" type="text" inputmode="numeric" maxlength="10" value="{{ old('cedula') }}" required
                               placeholder="Solo números"
                               class="{{ $campo }} {{ $errors->has('cedula') ? 'border-rose-500' : 'border-white/10' }}">
                        @error('cedula')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="direccion" class="text-sm font-medium text-slate-200">Dirección</label>
                        <input id="direccion" name="direccion" type="text" value="{{ old('direccion') }}" autocomplete="street-address" required
                               placeholder="Ej: Calle 10 # 5-20, Bogotá"
                               class="{{ $campo }} {{ $errors->has('direccion') ? 'border-rose-500' : 'border-white/10' }}">
                        @error('direccion')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="celular" class="text-sm font-medium text-slate-200">Celular</label>
                        <input id="celular" name="celular" type="tel" inputmode="numeric" maxlength="10" value="{{ old('celular') }}" autocomplete="tel" required
                               placeholder="Ej: 3001234567"
                               class="{{ $campo }} {{ $errors->has('celular') ? 'border-rose-500' : 'border-white/10' }}">
                        @error('celular')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-gradient-to-r from-amber-400 to-fuchsia-500 px-6 py-3 text-base font-semibold text-slate-950 shadow-lg transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-amber-300">
                        Quiero participar
                    </button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
