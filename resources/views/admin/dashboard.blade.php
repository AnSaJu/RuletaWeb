@extends('admin.panel')

@section('title', 'Resumen')
@section('activa', 'dashboard')

@section('contenido')
    <h1 class="text-3xl font-bold tracking-tight">Hola, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
    <p class="mt-1 text-slate-400">Así va la participación en la ruleta.</p>

    {{-- Tarjetas de resumen --}}
    <section class="mt-8 grid gap-4 sm:grid-cols-3" aria-label="Resumen de registros">
        @foreach ([['Total de registros', $total, 'Desde el inicio'], ['Hoy', $hoy, 'Personas registradas hoy'], ['Últimos 7 días', $ultimaSemana, 'Incluye hoy']] as [$titulo, $cifra, $nota])
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-slate-400">{{ $titulo }}</p>
                <p class="mt-2 text-4xl font-bold tabular-nums">{{ number_format($cifra, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $nota }}</p>
            </div>
        @endforeach
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-5">
        {{-- Registros por día --}}
        <section class="rounded-2xl border border-white/10 bg-white/5 p-6 lg:col-span-3" aria-labelledby="titulo-dias">
            <h2 id="titulo-dias" class="text-lg font-semibold">Registros por día</h2>
            <p class="text-sm text-slate-400">Últimos {{ $dias->count() }} días</p>

            <div class="mt-6 flex h-44 items-end gap-1.5" role="img"
                 aria-label="Gráfico de registros por día: {{ $dias->map(fn ($d) => $d['etiqueta'].': '.$d['cantidad'])->implode(', ') }}">
                @foreach ($dias as $dia)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-1" title="{{ $dia['etiqueta'] }}: {{ $dia['cantidad'] }}">
                        <span class="text-[10px] tabular-nums text-slate-400">{{ $dia['cantidad'] ?: '' }}</span>
                        <div class="w-full rounded-t-md bg-gradient-to-t from-fuchsia-600 to-amber-400 {{ $dia['cantidad'] ? '' : 'opacity-25' }}"
                             style="height: {{ $dia['cantidad'] ? max(round($dia['cantidad'] / $maxDia * 100), 4) : 2 }}%"></div>
                    </div>
                @endforeach
            </div>
            <div class="mt-2 flex gap-1.5 text-[10px] text-slate-500" aria-hidden="true">
                @foreach ($dias as $i => $dia)
                    <span class="flex-1 text-center">{{ $i % 2 === 0 ? $dia['etiqueta'] : '' }}</span>
                @endforeach
            </div>
        </section>

        {{-- Opciones elegidas --}}
        <section class="rounded-2xl border border-white/10 bg-white/5 p-6 lg:col-span-2" aria-labelledby="titulo-opciones">
            <h2 id="titulo-opciones" class="text-lg font-semibold">Opciones elegidas</h2>
            <p class="text-sm text-slate-400">Dónde se detuvo la ruleta</p>

            @forelse ($opciones as $opcion)
                <div class="mt-5">
                    <div class="flex items-baseline justify-between gap-3 text-sm">
                        <span class="flex items-center gap-2 font-medium">
                            <span class="h-3 w-3 rounded-full" style="background-color: {{ $opcion['color'] }}" aria-hidden="true"></span>
                            {{ $opcion['nombre'] }}
                        </span>
                        <span class="tabular-nums text-slate-400">{{ $opcion['cantidad'] }} · {{ str_replace('.', ',', $opcion['porcentaje']) }} %</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/10">
                        <div class="h-full rounded-full" style="width: {{ $opcion['porcentaje'] }}%; background-color: {{ $opcion['color'] }}"></div>
                    </div>
                </div>
            @empty
                <p class="mt-8 text-center text-slate-500">Aún no hay datos.</p>
            @endforelse
        </section>
    </div>

    {{-- Últimos registros --}}
    <section class="mt-6 rounded-2xl border border-white/10 bg-white/5" aria-labelledby="titulo-recientes">
        <div class="flex items-center justify-between px-6 pt-6">
            <h2 id="titulo-recientes" class="text-lg font-semibold">Últimos registros</h2>
            <a href="{{ route('admin.participantes.index') }}" class="text-sm text-fuchsia-300 transition hover:text-fuchsia-200">Ver todos →</a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[32rem] text-left text-sm">
                <thead class="border-y border-white/10 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nombre</th>
                        <th scope="col" class="px-6 py-3">Celular</th>
                        <th scope="col" class="px-6 py-3">Opción</th>
                        <th scope="col" class="px-6 py-3">Registrado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($recientes as $participante)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $participante->nombre }}</td>
                            <td class="px-6 py-3 tabular-nums text-slate-300">{{ $participante->celular }}</td>
                            <td class="px-6 py-3 text-slate-300">{{ $participante->opcion ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-3 text-slate-400">{{ $participante->created_at->timezone('America/Bogota')->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">Aún no hay personas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('administrar')
        <section class="mt-6 grid gap-4 sm:grid-cols-3" aria-label="Accesos rápidos">
            @foreach ([['🎡', 'Configurar la ruleta', 'Velocidad, opciones y tamaños', route('admin.ruleta.edit')], ['🎨', 'Editar la landing page', 'Textos y colores', route('admin.landing.edit')], ['🔑', 'Gestionar usuarios', 'Roles y accesos', route('admin.usuarios.index')]] as [$icono, $titulo, $nota, $url])
                <a href="{{ $url }}" class="flex items-center gap-4 rounded-2xl border border-white/10 bg-white/5 p-5 transition hover:bg-white/10">
                    <span class="text-3xl" aria-hidden="true">{{ $icono }}</span>
                    <span>
                        <span class="block font-medium">{{ $titulo }}</span>
                        <span class="block text-sm text-slate-400">{{ $nota }}</span>
                    </span>
                </a>
            @endforeach
        </section>
    @endcan
@endsection
