<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $lp['meta_titulo'] }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.tema')
</head>
<body class="tema min-h-screen font-sans antialiased">
    <div class="relative isolate overflow-hidden">
        {{-- Fondo decorativo --}}
        <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
            <div class="tema-brillo-1 absolute -top-40 left-1/2 h-[40rem] w-[40rem] -translate-x-1/2 rounded-full blur-3xl"></div>
            <div class="tema-brillo-2 absolute -bottom-40 -right-20 h-[30rem] w-[30rem] rounded-full blur-3xl"></div>
        </div>

        <main class="mx-auto grid min-h-screen max-w-6xl items-center gap-12 px-6 py-16 lg:grid-cols-2">
            {{-- Columna de presentación --}}
            <section class="text-center lg:text-left">
                {{-- Marca: logo y nombre principal --}}
                @if (filled($lp['logo']) || filled($lp['nombre_principal']))
                    <div class="mb-8 flex items-center justify-center gap-4 lg:justify-start">
                        @if (filled($lp['logo']))
                            <img src="{{ \App\Support\LandingPage::urlLogo($lp['logo']) }}"
                                 alt="{{ filled($lp['nombre_principal']) ? '' : 'Logo' }}"
                                 class="h-16 w-auto max-w-[10rem] object-contain sm:h-20">
                        @endif

                        @if (filled($lp['nombre_principal']))
                            <p class="text-2xl font-bold tracking-tight sm:text-3xl">{{ $lp['nombre_principal'] }}</p>
                        @endif
                    </div>
                @endif

                @if (filled($lp['insignia']))
                    <span class="tema-insignia inline-flex items-center gap-2 rounded-full px-4 py-1 text-sm font-medium">
                        {{ $lp['insignia'] }}
                    </span>
                @endif

                <h1 class="mt-6 text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                    @foreach (\App\Support\LandingPage::segmentosDelTitulo($lp['titulo']) as $segmento)
                        @if ($segmento['resaltado'])<span class="tema-texto-degradado">{{ $segmento['texto'] }}</span>@else{{ $segmento['texto'] }}@endif
                    @endforeach
                </h1>

                @if (filled($lp['descripcion']))
                    <p class="tema-suave mt-6 text-lg">{{ $lp['descripcion'] }}</p>
                @endif

                @php
                    $beneficios = \App\Support\LandingPage::listaDeBeneficios($lp['beneficios']);
                @endphp
                @if ($beneficios)
                    <ul class="tema-suave mt-8 space-y-3">
                        @foreach ($beneficios as $beneficio)
                            <li class="flex items-center justify-center gap-3 lg:justify-start">
                                <span class="tema-check flex h-6 w-6 shrink-0 items-center justify-center rounded-full" aria-hidden="true">✓</span>
                                {{ $beneficio }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Formulario --}}
            <section class="tema-tarjeta rounded-3xl p-8 shadow-2xl backdrop-blur">
                <h2 class="text-2xl font-semibold">{{ $lp['formulario_titulo'] }}</h2>
                @if (filled($lp['formulario_subtitulo']))
                    <p class="tema-suave mt-1 text-sm">{{ $lp['formulario_subtitulo'] }}</p>
                @endif

                @if (session('registrado'))
                    <div role="status" class="mt-6 rounded-xl border border-emerald-500/40 bg-emerald-500/15 px-4 py-3 font-medium text-emerald-600">
                        {{ session('registrado') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('participantes.store') }}" class="mt-6 space-y-5" novalidate>
                    @csrf

                    @php
                        $campo = 'tema-campo mt-1 block w-full rounded-xl px-4 py-3 transition';
                    @endphp

                    <div>
                        <label for="nombre" class="text-sm font-medium">Nombre completo</label>
                        <input id="nombre" name="nombre" type="text" value="{{ old('nombre') }}" autocomplete="name" required
                               placeholder="Ej: María Fernanda López" @if ($errors->has('nombre')) aria-invalid="true" @endif
                               class="{{ $campo }}">
                        @error('nombre')<p class="mt-1 text-sm text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="cedula" class="text-sm font-medium">Cédula</label>
                        <input id="cedula" name="cedula" type="text" inputmode="numeric" maxlength="10" value="{{ old('cedula') }}" required
                               placeholder="Solo números" @if ($errors->has('cedula')) aria-invalid="true" @endif
                               class="{{ $campo }}">
                        @error('cedula')<p class="mt-1 text-sm text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="direccion" class="text-sm font-medium">Dirección</label>
                        <input id="direccion" name="direccion" type="text" value="{{ old('direccion') }}" autocomplete="street-address" required
                               placeholder="Ej: Calle 10 # 5-20, Bogotá" @if ($errors->has('direccion')) aria-invalid="true" @endif
                               class="{{ $campo }}">
                        @error('direccion')<p class="mt-1 text-sm text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="celular" class="text-sm font-medium">Celular</label>
                        <input id="celular" name="celular" type="tel" inputmode="numeric" maxlength="10" value="{{ old('celular') }}" autocomplete="tel" required
                               placeholder="Ej: 3001234567" @if ($errors->has('celular')) aria-invalid="true" @endif
                               class="{{ $campo }}">
                        @error('celular')<p class="mt-1 text-sm text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="tema-boton w-full rounded-xl px-6 py-3 text-base font-semibold shadow-lg transition">
                        {{ $lp['boton_texto'] }}
                    </button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
