<?php

namespace Tests\Feature\Console;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CrearAdministradorTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_un_super_administrador_con_las_opciones(): void
    {
        $this->artisan('admin:crear', [
            '--name' => 'Ana Ruiz',
            '--email' => 'ana@example.com',
            '--password' => 'clave-segura-1',
            '--no-interaction' => true,
        ])->assertSuccessful();

        $usuario = User::where('email', 'ana@example.com')->firstOrFail();

        $this->assertSame(Rol::SuperAdmin, $usuario->rol);
        $this->assertSame('Ana Ruiz', $usuario->name);
        $this->assertTrue(Hash::check('clave-segura-1', $usuario->password));
        $this->assertNotNull($usuario->email_verified_at);
    }

    public function test_pide_los_datos_de_forma_interactiva(): void
    {
        $this->artisan('admin:crear')
            ->expectsQuestion('Nombre', 'Luis Pérez')
            ->expectsQuestion('Correo electrónico', 'luis@example.com')
            ->expectsQuestion('Contraseña (mínimo 8 caracteres)', 'clave-segura-2')
            ->expectsOutputToContain('Se creó el super administrador luis@example.com.')
            ->assertSuccessful();

        $this->assertSame(Rol::SuperAdmin, User::where('email', 'luis@example.com')->firstOrFail()->rol);
    }

    public function test_rechaza_una_contrasena_corta_o_un_correo_invalido(): void
    {
        $this->artisan('admin:crear', [
            '--name' => 'Ana',
            '--email' => 'no-es-correo',
            '--password' => 'corta',
            '--no-interaction' => true,
        ])->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_restablece_la_contrasena_de_un_usuario_existente(): void
    {
        $existente = User::factory()->administrador()->create([
            'email' => 'ana@example.com',
            'password' => 'clave-antigua-1',
        ]);

        $this->artisan('admin:crear', [
            '--name' => 'Ana Ruiz',
            '--email' => 'ana@example.com',
            '--password' => 'clave-nueva-123',
            '--no-interaction' => true,
        ])->assertSuccessful();

        $existente->refresh();

        $this->assertDatabaseCount('users', 1);
        $this->assertSame(Rol::SuperAdmin, $existente->rol);
        $this->assertTrue(Hash::check('clave-nueva-123', $existente->password));
    }

    public function test_no_cambia_nada_si_se_rechaza_la_confirmacion(): void
    {
        $existente = User::factory()->administrador()->create([
            'email' => 'ana@example.com',
            'password' => 'clave-antigua-1',
        ]);

        $this->artisan('admin:crear', [
            '--name' => 'Ana Ruiz',
            '--email' => 'ana@example.com',
            '--password' => 'clave-nueva-123',
        ])
            ->expectsConfirmation('Ya existe ana@example.com. ¿Restablecer su contraseña y dejarlo como super administrador?', 'no')
            ->assertSuccessful();

        $existente->refresh();

        $this->assertSame(Rol::Administrador, $existente->rol);
        $this->assertTrue(Hash::check('clave-antigua-1', $existente->password));
    }
}
