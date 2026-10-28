<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreParticipanteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize the input before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => trim((string) $this->input('nombre')),
            'direccion' => trim((string) $this->input('direccion')),
            'cedula' => preg_replace('/\D+/', '', (string) $this->input('cedula')),
            'celular' => preg_replace('/\D+/', '', (string) $this->input('celular')),
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
            'nombre' => ['required', 'string', 'min:3', 'max:120'],
            'cedula' => ['required', 'digits_between:6,10', 'unique:participantes,cedula'],
            'direccion' => ['required', 'string', 'min:5', 'max:200'],
            'celular' => ['required', 'regex:/^3\d{9}$/'],
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
            'nombre.required' => 'Ingresa tu nombre completo.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre no puede superar 120 caracteres.',
            'cedula.required' => 'Ingresa tu número de cédula.',
            'cedula.digits_between' => 'La cédula debe tener entre 6 y 10 dígitos.',
            'cedula.unique' => 'Esta cédula ya está registrada.',
            'direccion.required' => 'Ingresa tu dirección.',
            'direccion.min' => 'La dirección debe tener al menos 5 caracteres.',
            'direccion.max' => 'La dirección no puede superar 200 caracteres.',
            'celular.required' => 'Ingresa tu número de celular.',
            'celular.regex' => 'El celular debe tener 10 dígitos y empezar por 3.',
        ];
    }
}
