<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRuletaRequest;
use App\Models\Ajuste;
use App\Models\OpcionRuleta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RuletaController extends Controller
{
    /**
     * Show the roulette settings form.
     */
    public function edit(): View
    {
        return view('admin.ruleta', [
            'opciones' => OpcionRuleta::ordenadas()->get(['id', 'nombre', 'color', 'tamano']),
            'segundosPorVuelta' => Ajuste::segundosPorVuelta(),
            'minSegundos' => UpdateRuletaRequest::MIN_SEGUNDOS,
            'maxSegundos' => UpdateRuletaRequest::MAX_SEGUNDOS,
            'maxTamano' => UpdateRuletaRequest::MAX_TAMANO,
        ]);
    }

    /**
     * Save the speed and the options of the roulette.
     */
    public function update(UpdateRuletaRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        DB::transaction(function () use ($datos) {
            Ajuste::guardarSegundosPorVuelta((float) $datos['segundos_por_vuelta']);

            foreach ($datos['opciones'] as $orden => $opcion) {
                OpcionRuleta::whereKey($opcion['id'])->update([
                    'nombre' => $opcion['nombre'],
                    'color' => strtolower($opcion['color']),
                    'tamano' => $opcion['tamano'],
                    'orden' => $orden + 1,
                ]);
            }
        });

        return redirect()
            ->route('admin.ruleta.edit')
            ->with('guardado', 'La configuración de la ruleta se guardó correctamente.');
    }
}
