<?php

namespace App\Http\Requests\Admin;

use App\Support\LandingPage;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLandingRequest extends FormRequest
{
    public const MAX_BENEFICIOS = 6;

    public const MAX_LOGO_KB = 2048;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('administrar');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $color = ['required', 'regex:/^#[0-9a-fA-F]{6}$/'];

        return [
            'meta_titulo' => ['required', 'string', 'max:80'],
            'nombre_principal' => ['nullable', 'string', 'max:60'],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:'.self::MAX_LOGO_KB],
            'quitar_logo' => ['nullable', 'boolean'],
            'insignia' => ['nullable', 'string', 'max:60'],
            'titulo' => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string', 'max:300'],
            'beneficios' => ['nullable', 'string', 'max:700', function (string $atributo, mixed $valor, Closure $fallar) {
                $lineas = LandingPage::listaDeBeneficios((string) $valor);

                if (count($lineas) > self::MAX_BENEFICIOS) {
                    $fallar('Puedes escribir máximo '.self::MAX_BENEFICIOS.' beneficios, uno por línea.');
                }

                foreach ($lineas as $linea) {
                    if (mb_strlen($linea) > 100) {
                        $fallar('Cada beneficio puede tener máximo 100 caracteres.');

                        return;
                    }
                }
            }],
            'formulario_titulo' => ['required', 'string', 'max:60'],
            'formulario_subtitulo' => ['nullable', 'string', 'max:120'],
            'boton_texto' => ['required', 'string', 'max:40'],
            'color_fondo' => $color,
            'color_texto' => $color,
            'color_acento_1' => $color,
            'color_acento_2' => $color,
        ];
    }

    /**
     * Get the validation messages in Spanish.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'meta_titulo.required' => 'Escribe el título de la pestaña del navegador.',
            'meta_titulo.max' => 'El título de la pestaña no puede superar 80 caracteres.',
            'nombre_principal.max' => 'El nombre principal no puede superar 60 caracteres.',
            'logo.mimes' => 'El logo debe ser una imagen PNG, JPG o WebP.',
            'logo.max' => 'El logo no puede pesar más de 2 MB.',
            'logo.*' => 'No se pudo subir el logo. Prueba con otra imagen PNG, JPG o WebP de máximo 2 MB.',
            'insignia.max' => 'La insignia no puede superar 60 caracteres.',
            'titulo.required' => 'Escribe el título principal.',
            'titulo.max' => 'El título no puede superar 120 caracteres.',
            'descripcion.max' => 'La descripción no puede superar 300 caracteres.',
            'beneficios.max' => 'Los beneficios son demasiado largos.',
            'formulario_titulo.required' => 'Escribe el título del formulario.',
            'formulario_titulo.max' => 'El título del formulario no puede superar 60 caracteres.',
            'formulario_subtitulo.max' => 'El subtítulo del formulario no puede superar 120 caracteres.',
            'boton_texto.required' => 'Escribe el texto del botón.',
            'boton_texto.max' => 'El texto del botón no puede superar 40 caracteres.',
            'color_fondo.*' => 'Elige un color de fondo válido.',
            'color_texto.*' => 'Elige un color de texto válido.',
            'color_acento_1.*' => 'Elige un primer color de acento válido.',
            'color_acento_2.*' => 'Elige un segundo color de acento válido.',
        ];
    }
}
