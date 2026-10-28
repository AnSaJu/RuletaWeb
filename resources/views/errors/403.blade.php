<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Acceso denegado · {{ config('app.name') }}</title>

    @vite('resources/css/app.css')
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-950 px-6 font-sans text-slate-100 antialiased">
    <main class="max-w-md text-center">
        <p class="text-6xl font-bold text-fuchsia-400">403</p>
        <h1 class="mt-4 text-2xl font-semibold">No tienes permiso para ver esta página</h1>
        <p class="mt-2 text-slate-400">Tu rol no incluye esta sección. Si crees que es un error, habla con un super administrador.</p>

        <a href="{{ auth()->check() ? route('admin.participantes.index') : route('home') }}"
           class="mt-8 inline-block rounded-xl bg-gradient-to-r from-amber-400 to-fuchsia-500 px-6 py-3 font-semibold text-slate-950 shadow-lg transition hover:brightness-110">
            Volver al inicio
        </a>
    </main>
</body>
</html>
