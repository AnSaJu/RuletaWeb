<?php

namespace App\Console\Commands;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

#[Signature('admin:crear {--name= : Nombre del usuario} {--email= : Correo con el que inicia sesión} {--password= : Contraseña (si se omite, se pide sin mostrarla)}')]
#[Description('Crea un super administrador del panel (o restablece su contraseña si el correo ya existe)')]
class CrearAdministrador extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Nombre', 'Administrador');
        $email = $this->option('email') ?: $this->ask('Correo electrónico');
        $password = $this->option('password') ?: $this->secret('Contraseña (mínimo 8 caracteres)');

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', Password::min(8)],
            ],
            attributes: ['name' => 'nombre', 'email' => 'correo', 'password' => 'contraseña'],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $usuario = User::firstOrNew(['email' => $email]);
        $existia = $usuario->exists;

        if ($existia && ! $this->option('no-interaction') && ! $this->confirm("Ya existe {$email}. ¿Restablecer su contraseña y dejarlo como super administrador?")) {
            $this->warn('No se hizo ningún cambio.');

            return self::SUCCESS;
        }

        $usuario->forceFill([
            'name' => $name,
            'password' => $password,
            'rol' => Rol::SuperAdmin,
            'email_verified_at' => $usuario->email_verified_at ?? now(),
        ])->save();

        $this->info($existia
            ? "Se actualizó el super administrador {$email}."
            : "Se creó el super administrador {$email}.");

        return self::SUCCESS;
    }
}
