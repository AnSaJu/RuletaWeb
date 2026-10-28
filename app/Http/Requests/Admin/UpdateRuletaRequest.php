<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRuletaRequest extends FormRequest
{
    public const MIN_SEGUNDOS = 0.5;

    public const MAX_SEGUNDOS = 10;

    public const MAX_TAMANO = 100;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /**
     * Accept a decimal comma ("1,5") as well as a decimal point.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'segundos_por_vuelta' => str_replace(',', '.', (string) $this->input('segundos_por_vuelta')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'segundos_por_vuelta' => ['required', 'numeric', 'between:'.self::MIN_SEGUNDOS.','.self::MAX_SEGUNDOS],
            'opciones' => ['required', 'array', 'min:2'],
            'opciones.*.id' => ['required', 'integer', 'distinct', Rule::exists('opciones_ruleta', 'id')],
            'opciones.*.nombre' => ['required', 'string', 'max:60', 'distinct:ignore_case'],
            'opciones.*.color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'opciones.*.tamano' => ['required', 'integer', 'between:1,'.self::MAX_TAMANO],
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
            'segundos_por_vuelta.required' => 'Indica cuántos segundos tarda una vuelta.',
            'segundos_por_vuelta.numeric' => 'La velocidad debe ser un número.',
            'segundos_por_vuelta.between' => 'Una vuelta debe tardar entre '.self::MIN_SEGUNDOS.' y '.self::MAX_SEGUNDOS.' segundos.',
            'opciones.required' => 'Faltan las opciones de la ruleta.',
            'opciones.*.nombre.required' => 'Cada opción necesita un nombre.',
            'opciones.*.nombre.max' => 'El nombre no puede superar 60 caracteres.',
            'opciones.*.nombre.distinct' => 'Dos opciones no pueden tener el mismo nombre.',
            'opciones.*.color.required' => 'Elige un color para cada opción.',
            'opciones.*.color.regex' => 'El color no es válido.',
            'opciones.*.tamano.required' => 'Indica el tamaño de cada opción.',
            'opciones.*.tamano.integer' => 'El tamaño debe ser un número entero.',
            'opciones.*.tamano.between' => 'El tamaño debe estar entre 1 y '.self::MAX_TAMANO.'.',
            'opciones.*.id.*' => 'Hay una opción que no existe. Recarga la página.',
        ];
    }
}
