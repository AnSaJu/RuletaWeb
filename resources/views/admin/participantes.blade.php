@extends('admin.panel')

@section('title', 'Participantes')

@section('activa', 'participantes')

@section('contenido')

    <div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Personas registradas</h1>
                <p class="mt-1 text-slate-400">
                    {{ number_format($total, 0, ',', '.') }} {{ $total === 1 ? 'registro en total' : 'registros en total' }}
                </p>
            </div>

            <form method="GET" action="{{ route('admin.participantes.index') }}" class="flex w-full gap-2 sm:w-auto" role="search">
                <input type="search" name="q" value="{{ $busqueda }}" placeholder="Buscar por nombre, cédula o celular"
                       aria-label="Buscar participantes"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/70 px-4 py-2.5 text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-fuchsia-400 sm:w-80">
                <button type="submit" class="rounded-xl bg-fuchsia-500 px-4 py-2.5 font-medium text-white transition hover:bg-fuchsia-400">
                    Buscar
                </button>
                @if ($busqueda !== '')
                    <a href="{{ route('admin.participantes.index') }}" class="rounded-xl border border-white/15 px-4 py-2.5 text-slate-200 transition hover:bg-white/10">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <div class="mt-8 overflow-x-auto rounded-2xl border border-white/10 bg-white/5">
            <table class="w-full min-w-[48rem] text-left text-sm">
                <thead class="border-b border-white/10 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th scope="col" class="px-5 py-3">#</th>
                        <th scope="col" class="px-5 py-3">Nombre</th>
                        <th scope="col" class="px-5 py-3">Cédula</th>
                        <th scope="col" class="px-5 py-3">Dirección</th>
                        <th scope="col" class="px-5 py-3">Celular</th>
                        <th scope="col" class="px-5 py-3">Opción</th>
                        <th scope="col" class="px-5 py-3">Registrado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($participantes as $participante)
                        <tr class="transition hover:bg-white/5">
                            <td class="px-5 py-3 text-slate-500">{{ $participante->id }}</td>
                            <td class="px-5 py-3 font-medium">{{ $participante->nombre }}</td>
                            <td class="px-5 py-3 tabular-nums">{{ $participante->cedula }}</td>
                            <td class="px-5 py-3 text-slate-300">{{ $participante->direccion }}</td>
                            <td class="px-5 py-3 tabular-nums">{{ $participante->celular }}</td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                @if ($participante->opcion)
                                    <span class="rounded-full bg-fuchsia-500/15 px-3 py-1 text-fuchsia-300">{{ $participante->opcion }}</span>
                                @else
                                    <span class="text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-400">
                                {{ $participante->created_at->timezone('America/Bogota')->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"class="px-5 py-12 text-center text-slate-400">
                                {{ $busqueda !== '' ? 'No se encontraron resultados para tu búsqueda.' : 'Aún no hay personas registradas.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($participantes->hasPages())
            <div class="mt-6">
                {{ $participantes->links() }}
            </div>
        @endif
    </div>
@endsection
