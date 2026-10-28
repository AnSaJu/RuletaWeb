<?php

namespace Tests\Feature\Admin;

use App\Models\Participante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_users_without_a_role_cannot_see_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_the_dashboard_summarizes_the_registrations(): void
    {
        Participante::factory()->count(3)->create(['opcion' => 'Opción 1']);
        Participante::factory()->create(['opcion' => 'Opción 2']);
        Participante::factory()->create(['opcion' => null]);
        Participante::factory()->create(['nombre' => 'Registro Antiguo', 'opcion' => 'Opción 3', 'created_at' => now()->subDays(40)]);

        $respuesta = $this->actingAs(User::factory()->administrador()->create())
            ->get(route('admin.dashboard'));

        $respuesta->assertOk()
            ->assertViewHas('total', 6)
            ->assertViewHas('hoy', 5)
            ->assertViewHas('ultimaSemana', 5)
            ->assertSeeText('Opción 1')
            ->assertSeeText('Sin opción');

        $porcentajes = $respuesta->viewData('opciones')->pluck('porcentaje', 'nombre');
        $this->assertSame(50.0, $porcentajes['Opción 1']);
    }

    public function test_the_dashboard_works_without_registrations(): void
    {
        $this->actingAs(User::factory()->administrador()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('total', 0)
            ->assertSeeText('Aún no hay personas registradas.');
    }

    public function test_the_sidebar_depends_on_the_role(): void
    {
        $this->actingAs(User::factory()->administrador()->create())
            ->get(route('admin.dashboard'))
            ->assertSee(route('admin.participantes.index'))
            ->assertDontSee(route('admin.landing.edit'))
            ->assertDontSee(route('admin.ruleta.edit'))
            ->assertDontSee(route('admin.usuarios.index'));

        $this->actingAs(User::factory()->superAdmin()->create())
            ->get(route('admin.dashboard'))
            ->assertSee(route('admin.landing.edit'))
            ->assertSee(route('admin.ruleta.edit'))
            ->assertSee(route('admin.usuarios.index'));
    }
}
