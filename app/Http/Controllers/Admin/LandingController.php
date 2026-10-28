<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLandingRequest;
use App\Support\LandingPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
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
            'maxLogoMb' => UpdateLandingRequest::MAX_LOGO_KB / 1024,
        ]);
    }

    /**
     * Save the texts, colors and logo of the landing page.
     */
    public function update(UpdateLandingRequest $request): RedirectResponse
    {
        $datos = Arr::except($request->validated(), ['logo', 'quitar_logo']);

        foreach (['color_fondo', 'color_texto', 'color_acento_1', 'color_acento_2'] as $color) {
            $datos[$color] = strtolower($datos[$color]);
        }

        // Los navegadores envian los saltos de linea como CRLF; se guardan como LF.
        $datos['beneficios'] = str_replace("\r\n", "\n", (string) ($datos['beneficios'] ?? ''));

        $datos['logo'] = $this->logoResultante($request);

        LandingPage::guardar($datos);

        return redirect()
            ->route('admin.landing.edit')
            ->with('guardado', 'La landing page se actualizó correctamente.');
    }

    /**
     * Go back to the original texts, colors and logo.
     */
    public function destroy(): RedirectResponse
    {
        LandingPage::restaurar();

        return redirect()
            ->route('admin.landing.edit')
            ->with('guardado', 'Se restauraron los textos y colores originales.');
    }

    /**
     * Path of the logo that should remain after this save, storing a new
     * upload or removing the current one when asked.
     */
    private function logoResultante(UpdateLandingRequest $request): string
    {
        $actual = LandingPage::valores()['logo'];

        if ($request->hasFile('logo')) {
            LandingPage::borrarLogo($actual);

            return $request->file('logo')->store('logos', LandingPage::DISCO);
        }

        if ($request->boolean('quitar_logo')) {
            LandingPage::borrarLogo($actual);

            return '';
        }

        return $actual;
    }
}
