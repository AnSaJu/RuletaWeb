<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\OpcionRuleta;
use App\Models\Participante;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RuletaController extends Controller
{
    /**
     * Show the roulette to a participant who already filled the form.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $pendiente = $request->session()->get('participante_pendiente');

        if (! $pendiente) {
            return redirect()->route('home');
        }

        $opciones = OpcionRuleta::ordenadas()->get(['id', 'nombre', 'color', 'tamano']);

        return view('ruleta', [
            'nombre' => $pendiente['nombre'],
            'opciones' => $opciones,
            'segundosPorVuelta' => Ajuste::segundosPorVuelta(),
        ]);
    }

    /**
     * Save the participant together with the option the wheel stopped on.
     */
    public function store(Request $request): RedirectResponse
    {
        $pendiente = $request->session()->get('participante_pendiente');

        if (! $pendiente) {
            return redirect()->route('home');
        }

        $datos = $request->validate([
            'opcion' => ['required', 'integer', Rule::exists('opciones_ruleta', 'id')],
        ], [
            'opcion.*' => 'No pudimos leer la opción elegida. Intenta de nuevo.',
        ]);

        $opcion = OpcionRuleta::findOrFail($datos['opcion'])->nombre;

        try {
            Participante::create([...$pendiente, 'opcion' => $opcion]);
        } catch (UniqueConstraintViolationException) {
            $request->session()->forget('participante_pendiente');

            return redirect()->route('home')->withErrors(['cedula' => 'Esta cédula ya está registrada.']);
        }

        $request->session()->forget('participante_pendiente');

        return redirect()
            ->route('home')
            ->with('registrado', "¡Listo, {$pendiente['nombre']}! La ruleta se detuvo en «{$opcion}» y tus datos quedaron guardados.");
    }
}
