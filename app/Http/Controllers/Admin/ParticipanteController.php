<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participante;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParticipanteController extends Controller
{
    /**
     * List the registered participants, with optional search.
     */
    public function index(Request $request): View
    {
        $busqueda = trim((string) $request->query('q'));

        $participantes = Participante::query()
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $like = '%'.addcslashes($busqueda, '%_\\').'%';

                $query->where(function ($query) use ($like) {
                    $query->where('nombre', 'like', $like)
                        ->orWhere('cedula', 'like', $like)
                        ->orWhere('celular', 'like', $like);
                });
            })
            ->latest()
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.participantes', [
            'participantes' => $participantes,
            'busqueda' => $busqueda,
            'total' => Participante::count(),
        ]);
    }
}
