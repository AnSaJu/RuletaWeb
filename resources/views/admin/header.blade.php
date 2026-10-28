@php
    $enlaces = [
        'participantes' => ['Participantes', route('admin.participantes.index')],
        'ruleta' => ['Ruleta', route('admin.ruleta.edit')],
    ];
@endphp

<header class="border-b border-white/10 bg-white/5">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-6 py-4">
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-3">
                <span class="text-2xl" aria-hidden="true">🎡</span>
                <span class="text-lg font-semibold">{{ config('app.name') }}</span>
            </div>

            <nav class="flex gap-1 text-sm" aria-label="Administración">
                @foreach ($enlaces as $clave => [$texto, $url])
                    <a href="{{ $url }}"
                       @if ($activa === $clave) aria-current="page" @endif
                       class="rounded-lg px-3 py-1.5 transition {{ $activa === $clave ? 'bg-fuchsia-500/20 text-fuchsia-200' : 'text-slate-300 hover:bg-white/10' }}">
                        {{ $texto }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="flex items-center gap-4 text-sm">
            <span class="hidden text-slate-400 sm:inline">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="rounded-lg border border-white/15 px-3 py-1.5 text-slate-200 transition hover:bg-white/10">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</header>
