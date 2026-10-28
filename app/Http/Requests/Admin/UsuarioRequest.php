<?php

namespace App\Http\Requests\Admin;

use App\Enums\Rol;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('administrar');
    }

    /**
     * Normalize the input before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => strtolower(trim((string) $this->input('email'))),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $usuario = $this->route('usuario');

        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario)],
            'rol' => ['required', Rule::enum(Rol::class)],
            // Al editar, dejar la contrasena vacia conserva la actual.
            'password' => [$usuario ? 'nullable' : 'required', 'confirmed', Password::min(8)],
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
            'name.required' => 'Ingresa el nombre.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.max' => 'El nombre no puede superar 120 caracteres.',
            'email.required' => 'Ingresa el correo.',
            'email.email' => 'Ingresa un correo válido.',
            'email.max' => 'El correo no puede superar 255 caracteres.',
            'email.unique' => 'Ya existe un usuario con este correo.',
            'rol.required' => 'Elige un rol.',
            'rol.enum' => 'El rol elegido no es válido.',
            'password.required' => 'Ingresa una contraseña.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
