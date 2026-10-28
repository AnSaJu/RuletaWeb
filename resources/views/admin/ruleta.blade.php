@extends('admin.layout')

@section('title', 'Configurar ruleta')

@push('head')
    @vite('resources/js/admin-ruleta.js')
@endpush

@section('body')
    @include('admin.header', ['activa' => 'ruleta'])

    @php
        $campo = 'mt-1 block w-full rounded-xl border bg-slate-900/70 px-4 py-2.5 text-slate-100 outline-none transition focus:ring-2 focus:ring-fuchsia-400';
    @endphp

    <main class="mx-auto max-w-6xl px-6 py-10">
        <h1 class="text-3xl font-bold tracking-tight">Configurar la ruleta</h1>
        <p class="mt-1 text-slate-400">Los cambios se aplican a los próximos participantes.</p>

        @if (session('guardado'))
            <div role="status" class="mt-6 rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-emerald-300">
                {{ session('guardado') }}
            </div>
        @endif

        <form id="form-config-ruleta" method="POST" action="{{ route('admin.ruleta.update') }}" novalidate
              class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                {{-- Velocidad --}}
                <section class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h2 class="text-lg font-semibold">Velocidad</h2>
                    <label for="segundos_por_vuelta" class="mt-4 block text-sm font-medium text-slate-200">
                        Segundos que tarda una vuelta completa
                    </label>
                    <div class="flex items-center gap-3">
                        <input id="segundos_por_vuelta" name="segundos_por_vuelta" type="text" inputmode="decimal"
                               value="{{ old('segundos_por_vuelta', rtrim(rtrim(number_format($segundosPorVuelta, 2, '.', ''), '0'), '.')) }}"
                               class="{{ $campo }} max-w-32 {{ $errors->has('segundos_por_vuelta') ? 'border-rose-500' : 'border-white/10' }}">
                        <span class="text-sm text-slate-400">entre {{ $minSegundos }} y {{ $maxSegundos }}. Menos segundos = más rápida.</span>
                    </div>
                    @error('segundos_por_vuelta')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                </section>

                {{-- Opciones --}}
                <section class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h2 class="text-lg font-semibold">Opciones</h2>
                    <p class="mt-1 text-sm text-slate-400">
                        El tamaño es relativo: una opción con tamaño 2 ocupa el doble de espacio que una de tamaño 1.
                        Cuanto más pequeña, más difícil de acertar.
                    </p>

                    <div class="mt-5 space-y-4">
                        @foreach ($opciones as $i => $opcion)
                            <div data-opcion class="rounded-xl border border-white/10 bg-slate-900/40 p-4">
                                <input type="hidden" name="opciones[{{ $i }}][id]" value="{{ $opcion->id }}">

                                <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_5rem_8rem]">
                                    <div>
                                        <label for="nombre-{{ $i }}" class="text-sm font-medium text-slate-200">Nombre</label>
                                        <input id="nombre-{{ $i }}" name="opciones[{{ $i }}][nombre]" type="text" maxlength="60" data-campo="nombre"
                                               value="{{ old("opciones.$i.nombre", $opcion->nombre) }}"
                                               class="{{ $campo }} {{ $errors->has("opciones.$i.nombre") ? 'border-rose-500' : 'border-white/10' }}">
                                    </div>

                                    <div>
                                        <label for="color-{{ $i }}" class="text-sm font-medium text-slate-200">Color</label>
                                        <input id="color-{{ $i }}" name="opciones[{{ $i }}][color]" type="color" data-campo="color"
                                               value="{{ old("opciones.$i.color", $opcion->color) }}"
                                               class="mt-1 block h-[2.6rem] w-full cursor-pointer rounded-xl border border-white/10 bg-slate-900/70 p-1">
                                    </div>

                                    <div>
                                        <label for="tamano-{{ $i }}" class="text-sm font-medium text-slate-200">Tamaño</label>
                                        <div class="flex items-center gap-2">
                                            <input id="tamano-{{ $i }}" name="opciones[{{ $i }}][tamano]" type="number" min="1" max="{{ $maxTamano }}" step="1" data-campo="tamano"
                                                   value="{{ old("opciones.$i.tamano", $opcion->tamano) }}"
                                                   class="{{ $campo }} {{ $errors->has("opciones.$i.tamano") ? 'border-rose-500' : 'border-white/10' }}">
                                        </div>
                                    </div>
                                </div>

                                <p class="mt-2 text-sm text-slate-400">Ocupa <strong class="text-slate-200" data-porcentaje></strong> de la ruleta.</p>

                                @foreach (['nombre', 'color', 'tamano'] as $nombreCampo)
                                    @error("opciones.$i.$nombreCampo")<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    @error('opciones')<p class="mt-3 text-sm text-rose-400">{{ $message }}</p>@enderror
                </section>

                <button type="submit"
                        class="rounded-xl bg-gradient-to-r from-amber-400 to-fuchsia-500 px-8 py-3 text-base font-semibold text-slate-950 shadow-lg transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-amber-300">
                    Guardar cambios
                </button>
            </div>

            {{-- Vista previa --}}
            <aside class="lg:sticky lg:top-6 lg:self-start">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center">
                    <h2 class="text-lg font-semibold">Vista previa</h2>
                    <svg viewBox="-105 -118 210 223" class="mx-auto mt-4 w-full max-w-xs drop-shadow-2xl" role="img" aria-label="Vista previa de la ruleta">
                        <circle r="104" fill="#0f172a" stroke="#fbbf24" stroke-width="3" />
                        <g id="rueda"></g>
                        <circle r="8" fill="#0f172a" stroke="#fbbf24" stroke-width="2" />
                        <polygon points="-9,-116 9,-116 0,-92" fill="#f43f5e" stroke="#ffffff" stroke-width="2" stroke-linejoin="round" />
                    </svg>

                    <button id="probar-velocidad" type="button"
                            class="mt-4 rounded-xl border border-white/15 px-4 py-2 text-sm text-slate-200 transition hover:bg-white/10">
                        Probar velocidad
                    </button>
                </div>
            </aside>
        </form>
    </main>
@endsection
