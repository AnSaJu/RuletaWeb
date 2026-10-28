<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipanteRequest;
use Illuminate\Http\RedirectResponse;

class ParticipanteController extends Controller
{
    /**
     * Keep the validated data until the participant picks an option on the
     * roulette; the record is created once the wheel stops.
     */
    public function store(StoreParticipanteRequest $request): RedirectResponse
    {
        $request->session()->put('participante_pendiente', $request->validated());

        return redirect()->route('ruleta.show');
    }
}
