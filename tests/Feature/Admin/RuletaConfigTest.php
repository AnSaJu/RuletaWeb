<?php

namespace Tests\Feature\Admin;

use App\Models\Ajuste;
use App\Models\OpcionRuleta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuletaConfigTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Valid form data for the three default options, with optional overrides.
     *
     * @return array<string, mixed>
     */
    private function datos(array $sobrescribir = []): array
    {
        $opciones = OpcionRuleta::ordenadas()->get()->map(fn (OpcionRuleta $opcion) => [
            'id' => $opcion->id,
            'nombre' => $opcion->nombre,
            'color' => $opcion->color,
            'tamano' => $opcion->tamano,
        ])->all();

        return [
            'segundos_por_vuelta' => '2',
            'opciones' => $opciones,
            ...$sobrescribir,
        ];
    }

    private function admin(): User
    {
        return User::factory()->superAdmin()->create();
    }

    public function test_the_defaults_are_three_equal_options_at_two_seconds_per_turn(): void
    {
        $this->assertSame(['Opción 1', 'Opción 2', 'Opción 3'], OpcionRuleta::ordenadas()->pluck('nombre')->all());
        $this->assertSame([1, 1, 1], OpcionRuleta::ordenadas()->pluck('tamano')->all());
        $this->assertSame(2.0, Ajuste::segundosPorVuelta());
    }

    public function test_only_admins_can_open_the_settings(): void
    {
        $this->get(route('admin.ruleta.edit'))->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create())
            ->get(route('admin.ruleta.edit'))
            ->assertForbidden();

        $this->actingAs($this->admin())
            ->get(route('admin.ruleta.edit'))
            ->assertOk()
            ->assertSee('Opción 1');
    }

    public function test_only_admins_can_change_the_settings(): void
    {
        $this->put(route('admin.ruleta.update'), $this->datos(['segundos_por_vuelta' => '5']))
            ->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create())
            ->put(route('admin.ruleta.update'), $this->datos(['segundos_por_vuelta' => '5']))
            ->assertForbidden();

        $this->assertSame(2.0, Ajuste::segundosPorVuelta());
    }

    public function test_admin_can_change_speed_names_colors_and_sizes(): void
    {
        $datos = $this->datos(['segundos_por_vuelta' => '1,5']);
        $datos['opciones'][0] = [...$datos['opciones'][0], 'nombre' => 'Premio mayor', 'color' => '#FF0000', 'tamano' => 1];
        $datos['opciones'][1] = [...$datos['opciones'][1], 'nombre' => 'Premio medio', 'tamano' => 3];
        $datos['opciones'][2] = [...$datos['opciones'][2], 'nombre' => 'Sigue intentando', 'tamano' => 6];

        $this->actingAs($this->admin())
            ->put(route('admin.ruleta.update'), $datos)
            ->assertRedirect(route('admin.ruleta.edit'))
            ->assertSessionHas('guardado');

        $this->assertSame(1.5, Ajuste::segundosPorVuelta());

        $opciones = OpcionRuleta::ordenadas()->get();
        $this->assertSame(['Premio mayor', 'Premio medio', 'Sigue intentando'], $opciones->pluck('nombre')->all());
        $this->assertSame([1, 3, 6], $opciones->pluck('tamano')->all());
        $this->assertSame('#ff0000', $opciones[0]->color);
    }

    public function test_the_public_roulette_uses_the_saved_settings(): void
    {
        $datos = $this->datos(['segundos_por_vuelta' => '4']);
        $datos['opciones'][0] = [...$datos['opciones'][0], 'nombre' => 'Premio mayor', 'tamano' => 1];
        $datos['opciones'][1] = [...$datos['opciones'][1], 'tamano' => 5];

        $this->actingAs($this->admin())->put(route('admin.ruleta.update'), $datos);
        auth()->logout();

        $this->post(route('participantes.store'), [
            'nombre' => 'Ana Torres',
            'cedula' => '1020304050',
            'direccion' => 'Calle 1 # 2-3',
            'celular' => '3001234567',
        ]);

        $this->get(route('ruleta.show'))
            ->assertOk()
            ->assertSee('Premio mayor')
            ->assertSee('data-segundos-por-vuelta="4"', false);
    }

    public function test_a_participant_choice_stores_the_current_option_name(): void
    {
        $datos = $this->datos();
        $datos['opciones'][1] = [...$datos['opciones'][1], 'nombre' => 'Premio medio'];
        $this->actingAs($this->admin())->put(route('admin.ruleta.update'), $datos);
        auth()->logout();

        $this->post(route('participantes.store'), [
            'nombre' => 'Ana Torres',
            'cedula' => '1020304050',
            'direccion' => 'Calle 1 # 2-3',
            'celular' => '3001234567',
        ]);
        $this->post(route('ruleta.store'), ['opcion' => $datos['opciones'][1]['id']]);

        $this->assertDatabaseHas('participantes', ['cedula' => '1020304050', 'opcion' => 'Premio medio']);
    }

    public function test_invalid_settings_are_rejected(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.ruleta.update'), $this->datos(['segundos_por_vuelta' => '0.1']))
            ->assertSessionHasErrors('segundos_por_vuelta');

        $this->actingAs($admin)
            ->put(route('admin.ruleta.update'), $this->datos(['segundos_por_vuelta' => '30']))
            ->assertSessionHasErrors('segundos_por_vuelta');

        $datos = $this->datos();
        $datos['opciones'][0]['tamano'] = 0;
        $this->actingAs($admin)
            ->put(route('admin.ruleta.update'), $datos)
            ->assertSessionHasErrors('opciones.0.tamano');

        $datos = $this->datos();
        $datos['opciones'][0]['nombre'] = '';
        $this->actingAs($admin)
            ->put(route('admin.ruleta.update'), $datos)
            ->assertSessionHasErrors('opciones.0.nombre');

        $datos = $this->datos();
        $datos['opciones'][0]['color'] = 'rojo';
        $this->actingAs($admin)
            ->put(route('admin.ruleta.update'), $datos)
            ->assertSessionHasErrors('opciones.0.color');

        $datos = $this->datos();
        $datos['opciones'][1]['nombre'] = strtoupper($datos['opciones'][0]['nombre']);
        $this->actingAs($admin)
            ->put(route('admin.ruleta.update'), $datos)
            ->assertSessionHasErrors('opciones.1.nombre');

        $this->assertSame(2.0, Ajuste::segundosPorVuelta());
        $this->assertSame(['Opción 1', 'Opción 2', 'Opción 3'], OpcionRuleta::ordenadas()->pluck('nombre')->all());
    }

    public function test_unknown_options_are_rejected(): void
    {
        $datos = $this->datos();
        $datos['opciones'][0]['id'] = 9999;

        $this->actingAs($this->admin())
            ->put(route('admin.ruleta.update'), $datos)
            ->assertSessionHasErrors('opciones.0.id');
    }
}
