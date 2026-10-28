<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipanteRequest;
use App\Models\Participante;
use Illuminate\Http\RedirectResponse;

class ParticipanteController extends Controller
{
    /**
     * Store a newly registered participant.
     */
    public function store(StoreParticipanteRequest $request): RedirectResponse
    {
        Participante::create($request->validated());

        return redirect()
            ->route('home')
            ->with('registrado', '¡Registro exitoso! Tus datos fueron guardados correctamente.');
    }
}
