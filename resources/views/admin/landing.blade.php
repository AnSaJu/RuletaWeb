@extends('admin.panel')

@section('title', 'Landing page')
@section('activa', 'landing')

@push('head')
    @vite('resources/js/admin-landing.js')
@endpush

@section('contenido')
    @php
        $campo = 'mt-1 block w-full rounded-xl border bg-slate-900/70 px-4 py-2.5 text-slate-100 outline-none transition focus:ring-2 focus:ring-fuchsia-400';
        $v = fn (string $nombre) => old($nombre, $valores[$nombre]);
        $borde = fn (string $nombre) => $errors->has($nombre) ? 'border-rose-500' : 'border-white/10';
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Landing page</h1>
            <p class="mt-1 text-slate-400">Cambia los textos y los colores de la página que ven los participantes.</p>
        </div>

        <a href="{{ route('home') }}" target="_blank" rel="noopener"
           class="rounded-xl border border-white/15 px-4 py-2.5 text-center text-sm text-slate-200 transition hover:bg-white/10">
            Ver landing page ↗
        </a>
    </div>

    @if (session('guardado'))
        <div role="status" class="mt-6 rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-emerald-300">
            {{ session('guardado') }}
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" class="mt-6 rounded-xl border border-rose-400/40 bg-rose-500/10 px-4 py-3 text-rose-300">
            Revisa los campos marcados: no se guardó ningún cambio.
        </div>
    @endif

    <form id="form-landing" method="POST" action="{{ route('admin.landing.update') }}" novalidate enctype="multipart/form-data"
          class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_24rem]">
        @csrf
        @method('PUT')

        <div class="space-y-8">
            {{-- Marca --}}
            <section class="rounded-2xl border border-white/10 bg-white/5 p-6">
                <h2 class="text-lg font-semibold">Marca</h2>
                <p class="mt-1 text-sm text-slate-400">Aparecen arriba de todo en la landing page.</p>

                <div class="mt-4 space-y-5">
                    <div>
                        <label for="nombre_principal" class="text-sm font-medium text-slate-200">Nombre principal <span class="text-slate-500">(opcional)</span></label>
                        <input id="nombre_principal" name="nombre_principal" type="text" maxlength="60" value="{{ $v('nombre_principal') }}" class="{{ $campo }} {{ $borde('nombre_principal') }}">
                        @error('nombre_principal')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="logo" class="text-sm font-medium text-slate-200">Logo <span class="text-slate-500">(opcional)</span></label>

                        @if (filled($valores['logo']))
                            <div class="mt-2 flex items-center gap-4 rounded-xl border border-white/10 bg-slate-900/40 p-3">
                                <img src="{{ \App\Support\LandingPage::urlLogo($valores['logo']) }}" alt="Logo actual" class="h-14 w-auto max-w-[8rem] rounded object-contain">
                                <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-300">
                                    <input type="checkbox" id="quitar_logo" name="quitar_logo" value="1" @checked(old('quitar_logo')) class="h-4 w-4 rounded border-white/20 bg-slate-900">
                                    Quitar el logo
                                </label>
                            </div>
                        @endif

                        <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/webp"
                               class="mt-2 block w-full cursor-pointer rounded-xl border bg-slate-900/70 text-sm text-slate-300 file:mr-4 file:cursor-pointer file:border-0 file:bg-fuchsia-500/20 file:px-4 file:py-2.5 file:text-fuchsia-200 {{ $borde('logo') }}">
                        <p class="mt-1 text-xs text-slate-500">
                            {{ filled($valores['logo']) ? 'Elige un archivo para reemplazar el logo actual. ' : '' }}PNG, JPG o WebP, máximo {{ $maxLogoMb }} MB. Un fondo transparente (PNG) se ve mejor.
                        </p>
                        @error('logo')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            {{-- Textos --}}
            <section class="rounded-2xl border border-white/10 bg-white/5 p-6">
                <h2 class="text-lg font-semibold">Textos principales</h2>

                <div class="mt-4 space-y-5">
                    <div>
                        <label for="insignia" class="text-sm font-medium text-slate-200">Insignia <span class="text-slate-500">(opcional)</span></label>
                        <input id="insignia" name="insignia" type="text" maxlength="60" value="{{ $v('insignia') }}" class="{{ $campo }} {{ $borde('insignia') }}">
                        <p class="mt-1 text-xs text-slate-500">La etiqueta pequeña sobre el título. Puedes usar un emoji.</p>
                        @error('insignia')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="titulo" class="text-sm font-medium text-slate-200">Título</label>
                        <input id="titulo" name="titulo" type="text" maxlength="120" value="{{ $v('titulo') }}" class="{{ $campo }} {{ $borde('titulo') }}">
                        <p class="mt-1 text-xs text-slate-500">Escribe entre asteriscos las palabras que quieres resaltar con color: <code class="rounded bg-white/10 px-1">Regístrate y *participa*</code>.</p>
                        @error('titulo')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="descripcion" class="text-sm font-medium text-slate-200">Descripción <span class="text-slate-500">(opcional)</span></label>
                        <textarea id="descripcion" name="descripcion" rows="3" maxlength="300" class="{{ $campo }} {{ $borde('descripcion') }}">{{ $v('descripcion') }}</textarea>
                        @error('descripcion')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="beneficios" class="text-sm font-medium text-slate-200">Lista de puntos <span class="text-slate-500">(opcional)</span></label>
                        <textarea id="beneficios" name="beneficios" rows="4" class="{{ $campo }} {{ $borde('beneficios') }}">{{ $v('beneficios') }}</textarea>
                        <p class="mt-1 text-xs text-slate-500">Uno por línea, máximo {{ $maxBeneficios }}. Déjalo vacío para ocultar la lista.</p>
                        @error('beneficios')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            {{-- Formulario --}}
            <section class="rounded-2xl border border-white/10 bg-white/5 p-6">
                <h2 class="text-lg font-semibold">Formulario</h2>

                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="formulario_titulo" class="text-sm font-medium text-slate-200">Título del formulario</label>
                        <input id="formulario_titulo" name="formulario_titulo" type="text" maxlength="60" value="{{ $v('formulario_titulo') }}" class="{{ $campo }} {{ $borde('formulario_titulo') }}">
                        @error('formulario_titulo')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="boton_texto" class="text-sm font-medium text-slate-200">Texto del botón</label>
                        <input id="boton_texto" name="boton_texto" type="text" maxlength="40" value="{{ $v('boton_texto') }}" class="{{ $campo }} {{ $borde('boton_texto') }}">
                        @error('boton_texto')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="formulario_subtitulo" class="text-sm font-medium text-slate-200">Subtítulo del formulario <span class="text-slate-500">(opcional)</span></label>
                        <input id="formulario_subtitulo" name="formulario_subtitulo" type="text" maxlength="120" value="{{ $v('formulario_subtitulo') }}" class="{{ $campo }} {{ $borde('formulario_subtitulo') }}">
                        @error('formulario_subtitulo')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="meta_titulo" class="text-sm font-medium text-slate-200">Título de la pestaña del navegador</label>
                        <input id="meta_titulo" name="meta_titulo" type="text" maxlength="80" value="{{ $v('meta_titulo') }}" class="{{ $campo }} {{ $borde('meta_titulo') }}">
                        @error('meta_titulo')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            {{-- Colores --}}
            <section class="rounded-2xl border border-white/10 bg-white/5 p-6">
                <h2 class="text-lg font-semibold">Colores</h2>
                <p class="mt-1 text-sm text-slate-400">Los demás tonos (tarjetas, campos, bordes) se calculan a partir de estos cuatro. También se aplican a la pantalla de la ruleta.</p>

                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    @foreach ([
                        'color_fondo' => ['Fondo', 'El color de toda la página.'],
                        'color_texto' => ['Texto', 'Debe contrastar bien con el fondo.'],
                        'color_acento_1' => ['Acento 1', 'Inicio del degradado del botón y los resaltes.'],
                        'color_acento_2' => ['Acento 2', 'Final del degradado y resplandor del fondo.'],
                    ] as $nombre => [$etiqueta, $ayuda])
                        <div>
                            <label for="{{ $nombre }}" class="text-sm font-medium text-slate-200">{{ $etiqueta }}</label>
                            <div class="mt-1 flex items-center gap-3">
                                <input id="{{ $nombre }}" name="{{ $nombre }}" type="color" value="{{ $v($nombre) }}"
                                       class="h-11 w-16 cursor-pointer rounded-xl border bg-slate-900/70 p-1 {{ $borde($nombre) }}">
                                <output for="{{ $nombre }}" class="font-mono text-sm uppercase text-slate-400" data-valor="{{ $nombre }}">{{ $v($nombre) }}</output>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $ayuda }}</p>
                            @error($nombre)<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                </div>
            </section>

            <button type="submit"
                    class="rounded-xl bg-gradient-to-r from-amber-400 to-fuchsia-500 px-8 py-3 text-base font-semibold text-slate-950 shadow-lg transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-amber-300">
                Guardar cambios
            </button>
        </div>

        {{-- Vista previa --}}
        <aside class="lg:sticky lg:top-6 lg:self-start" aria-label="Vista previa de la landing page">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <h2 class="px-2 pb-3 text-lg font-semibold">Vista previa</h2>

                <div id="vista-previa" class="tema relative isolate overflow-hidden rounded-xl p-6"
                     style="--lp-fondo: {{ $v('color_fondo') }}; --lp-texto: {{ $v('color_texto') }}; --lp-acento-1: {{ $v('color_acento_1') }}; --lp-acento-2: {{ $v('color_acento_2') }}">
                    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
                        <div class="tema-brillo-1 absolute -top-20 left-1/2 h-64 w-64 -translate-x-1/2 rounded-full blur-3xl"></div>
                        <div class="tema-brillo-2 absolute -bottom-20 -right-10 h-48 w-48 rounded-full blur-3xl"></div>
                    </div>

                    <div data-previa="marca" class="mb-4 flex items-center gap-3">
                        <img data-previa="logo" alt="" src="{{ filled($valores['logo']) ? \App\Support\LandingPage::urlLogo($valores['logo']) : '' }}"
                             class="h-10 w-auto max-w-[6rem] object-contain" @if (blank($valores['logo'])) hidden @endif>
                        <p data-previa="nombre_principal" class="text-lg font-bold tracking-tight"></p>
                    </div>

                    <span data-previa="insignia" class="tema-insignia inline-flex rounded-full px-3 py-0.5 text-xs font-medium"></span>
                    <h3 data-previa="titulo" class="mt-3 text-2xl font-bold leading-tight tracking-tight"></h3>
                    <p data-previa="descripcion" class="tema-suave mt-3 text-sm"></p>
                    <ul data-previa="beneficios" class="tema-suave mt-4 space-y-2 text-sm"></ul>

                    <div class="tema-tarjeta mt-5 rounded-2xl p-4">
                        <p data-previa="formulario_titulo" class="font-semibold"></p>
                        <p data-previa="formulario_subtitulo" class="tema-suave text-xs"></p>
                        <div class="tema-campo mt-3 h-8 rounded-lg"></div>
                        <div class="tema-campo mt-2 h-8 rounded-lg"></div>
                        <div data-previa="boton_texto" class="tema-boton mt-3 rounded-lg px-4 py-2 text-center text-sm font-semibold"></div>
                    </div>
                </div>
                <p class="px-2 pt-3 text-xs text-slate-500">Se actualiza mientras escribes. Pulsa «Guardar cambios» para publicarla.</p>
            </div>
        </aside>
    </form>

    <section class="mt-10 rounded-2xl border border-white/10 bg-white/5 p-6">
        <h2 class="text-lg font-semibold">Restaurar el diseño original</h2>
        <p class="mt-1 text-sm text-slate-400">Vuelve a los textos y colores con los que empezó la landing page. Pierdes los cambios guardados.</p>

        <form method="POST" action="{{ route('admin.landing.destroy') }}" class="mt-4"
              onsubmit="return confirm('¿Restaurar los textos y colores originales? Esta acción no se puede deshacer.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-xl border border-rose-400/30 px-5 py-2.5 text-sm text-rose-300 transition hover:bg-rose-500/10">
                Restaurar valores originales
            </button>
        </form>
    </section>
@endsection
