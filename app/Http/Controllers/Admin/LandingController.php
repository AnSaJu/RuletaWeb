<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLandingRequest;
use App\Support\LandingPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Show the landing page editor.
     */
    public function edit(): View
    {
        return view('admin.landing', [
            'valores' => LandingPage::valores(),
            'maxBeneficios' => UpdateLandingRequest::MAX_BENEFICIOS,
        ]);
    }

    /**
     * Save the texts and colors of the landing page.
     */
    public function update(UpdateLandingRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['color_fondo'] = strtolower($datos['color_fondo']);
        $datos['color_texto'] = strtolower($datos['color_texto']);
        $datos['color_acento_1'] = strtolower($datos['color_acento_1']);
        $datos['color_acento_2'] = strtolower($datos['color_acento_2']);

        LandingPage::guardar($datos);

        return redirect()
            ->route('admin.landing.edit')
            ->with('guardado', 'La landing page se actualizó correctamente.');
    }

    /**
     * Go back to the original texts and colors.
     */
    public function destroy(): RedirectResponse
    {
        LandingPage::restaurar();

        return redirect()
            ->route('admin.landing.edit')
            ->with('guardado', 'Se restauraron los textos y colores originales.');
    }
}
