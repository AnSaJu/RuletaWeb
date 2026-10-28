@extends('admin.layout')

@section('body')
    @php
        $activa = trim($__env->yieldContent('activa'));
        $usuario = auth()->user();

        $secciones = [
            ['clave' => 'dashboard', 'texto' => 'Resumen', 'icono' => '🏠', 'url' => route('admin.dashboard')],
            ['clave' => 'participantes', 'texto' => 'Participantes', 'icono' => '👥', 'url' => route('admin.participantes.index')],
        ];

        if ($usuario->can('administrar')) {
            $secciones[] = ['clave' => 'ruleta', 'texto' => 'Ruleta', 'icono' => '🎡', 'url' => route('admin.ruleta.edit')];
            $secciones[] = ['clave' => 'landing', 'texto' => 'Landing page', 'icono' => '🎨', 'url' => route('admin.landing.edit')];
            $secciones[] = ['clave' => 'usuarios', 'texto' => 'Usuarios', 'icono' => '🔑', 'url' => route('admin.usuarios.index')];
        }
    @endphp

    <div class="min-h-screen lg:flex">
        {{-- Interruptor del menú en pantallas pequeñas --}}
        <input type="checkbox" id="menu-movil" class="peer sr-only">

        <header class="sticky top-0 z-20 flex items-center justify-between border-b border-white/10 bg-slate-900 px-4 py-3 lg:hidden">
            <span class="flex items-center gap-2 font-semibold"><span aria-hidden="true">🎡</span> {{ config('app.name') }}</span>
            <label for="menu-movil" class="cursor-pointer rounded-lg border border-white/15 px-3 py-1.5 text-sm text-slate-200">Menú</label>
        </header>

        <aside class="hidden w-full shrink-0 flex-col border-white/10 bg-slate-900 peer-checked:flex lg:sticky lg:top-0 lg:flex lg:h-screen lg:w-64 lg:border-r">
            <div class="hidden items-center gap-3 px-6 py-6 lg:flex">
                <span class="text-2xl" aria-hidden="true">🎡</span>
                <div class="leading-tight">
                    <p class="font-semibold">{{ config('app.name') }}</p>
                    <p class="text-xs text-slate-400">Panel de administración</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-4 lg:py-2" aria-label="Administración">
                @foreach ($secciones as $seccion)
                    <a href="{{ $seccion['url'] }}" @if ($activa === $seccion['clave']) aria-current="page" @endif
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $activa === $seccion['clave'] ? 'bg-fuchsia-500/20 text-fuchsia-100' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <span class="w-6 text-center text-lg" aria-hidden="true">{{ $seccion['icono'] }}</span>
                        {{ $seccion['texto'] }}
                    </a>
                @endforeach

                <a href="{{ route('home') }}" target="_blank" rel="noopener"
                   class="mt-4 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-400 transition hover:bg-white/5 hover:text-white">
                    <span class="w-6 text-center text-lg" aria-hidden="true">↗</span>
                    Ver landing page
                </a>
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-fuchsia-500 font-bold text-slate-950" aria-hidden="true">
                        {{ mb_strtoupper(mb_substr($usuario->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 leading-tight">
                        <p class="truncate text-sm font-medium">{{ $usuario->name }}</p>
                        <p class="truncate text-xs text-slate-400">{{ $usuario->rol?->etiqueta() }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full rounded-lg border border-white/15 px-3 py-2 text-sm text-slate-200 transition hover:bg-white/10">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

        <main class="min-w-0 flex-1 px-4 py-8 sm:px-8 lg:py-10">
            <div class="mx-auto max-w-6xl">
                @yield('contenido')
            </div>
        </main>
    </div>
@endsection
