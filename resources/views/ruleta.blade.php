<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} · Gira la ruleta</title>

    @vite(['resources/css/app.css', 'resources/js/ruleta.js'])
    @include('partials.tema')
</head>
<body class="tema min-h-screen font-sans antialiased">
    <main class="relative isolate flex min-h-screen flex-col items-center justify-center overflow-hidden px-6 py-12 text-center">
        <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
            <div class="tema-brillo-1 absolute -top-40 left-1/2 h-[40rem] w-[40rem] -translate-x-1/2 rounded-full blur-3xl"></div>
            <div class="tema-brillo-2 absolute -bottom-40 -right-20 h-[30rem] w-[30rem] rounded-full blur-3xl"></div>
        </div>

        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">¡Hola, {{ $nombre }}!</h1>
        <p class="tema-suave mt-3 max-w-md">
            La ruleta está girando. Presiona <strong>Detener</strong> cuando la flecha apunte a la opción que quieres elegir.
        </p>

        <div id="ruleta" data-opciones="{{ json_encode($opciones, JSON_UNESCAPED_UNICODE) }}" data-segundos-por-vuelta="{{ $segundosPorVuelta }}" class="relative mt-8 w-full max-w-sm">
            <svg viewBox="-105 -118 210 223" class="w-full drop-shadow-2xl" role="img" aria-label="Ruleta con {{ count($opciones) }} opciones">
                <circle r="104" style="fill: var(--lp-fondo); stroke: var(--lp-acento-1)" stroke-width="3" />
                <g id="rueda"></g>
                <circle r="8" style="fill: var(--lp-fondo); stroke: var(--lp-acento-1)" stroke-width="2" />
                {{-- Flecha fija --}}
                <polygon points="-9,-116 9,-116 0,-92" fill="#f43f5e" stroke="#ffffff" stroke-width="2" stroke-linejoin="round" />
            </svg>
        </div>

        @if ($errors->has('opcion'))
            <p class="mt-4 text-rose-500" role="alert">{{ $errors->first('opcion') }}</p>
        @endif

        <p id="resultado" class="tema-insignia mt-6 hidden rounded-xl px-5 py-3 text-xl font-semibold" role="status" aria-live="polite"></p>

        <button id="detener" type="button"
                class="tema-boton mt-6 rounded-xl px-10 py-4 text-lg font-bold shadow-lg transition disabled:cursor-not-allowed disabled:opacity-60">
            Detener
        </button>

        <form id="form-ruleta" method="POST" action="{{ route('ruleta.store') }}">
            @csrf
            <input type="hidden" name="opcion" id="campo-opcion" value="">
        </form>

        <noscript>
            <p class="mt-6 text-rose-500">Necesitas activar JavaScript para jugar con la ruleta.</p>
        </noscript>
    </main>
</body>
</html>
