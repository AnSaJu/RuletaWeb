<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpcionRuleta;
use App\Models\Participante;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const ZONA_HORARIA = 'America/Bogota';

    private const DIAS_DEL_GRAFICO = 14;

    /**
     * Summary of the registrations.
     */
    public function index(): View
    {
        $hoy = Carbon::now(self::ZONA_HORARIA)->startOfDay();
        $desde = $hoy->copy()->subDays(self::DIAS_DEL_GRAFICO - 1);

        $total = Participante::count();

        $porDia = Participante::query()
            ->where('created_at', '>=', $desde->copy()->utc())
            ->pluck('created_at')
            ->countBy(fn ($fecha) => $fecha->copy()->timezone(self::ZONA_HORARIA)->format('Y-m-d'));

        $dias = collect(range(0, self::DIAS_DEL_GRAFICO - 1))->map(function (int $i) use ($desde, $porDia) {
            $dia = $desde->copy()->addDays($i);

            return [
                'etiqueta' => $dia->format('d/m'),
                'cantidad' => $porDia->get($dia->format('Y-m-d'), 0),
            ];
        });

        $colores = OpcionRuleta::pluck('color', 'nombre');

        $conteos = Participante::query()
            ->selectRaw('opcion, count(*) as cantidad')
            ->groupBy('opcion')
            ->orderByDesc('cantidad')
            ->get();

        $opciones = $conteos->map(fn ($fila) => [
            'nombre' => $fila->opcion ?? 'Sin opción',
            'cantidad' => (int) $fila->cantidad,
            'porcentaje' => $total > 0 ? round($fila->cantidad / $total * 100, 1) : 0,
            'color' => $colores->get($fila->opcion, '#64748b'),
        ]);

        return view('admin.dashboard', [
            'total' => $total,
            'hoy' => $dias->last()['cantidad'],
            'ultimaSemana' => $dias->slice(-7)->sum('cantidad'),
            'dias' => $dias,
            'maxDia' => max($dias->max('cantidad'), 1),
            'opciones' => $opciones,
            'recientes' => Participante::latest()->latest('id')->take(6)->get(),
        ]);
    }
}
