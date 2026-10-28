<?php

namespace Tests\Feature;

use App\Models\OpcionRuleta;
use App\Models\Participante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistroRuletaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function datos(array $sobrescribir = []): array
    {
        return [
            'nombre' => 'Ana Torres',
            'cedula' => '1020304050',
            'direccion' => 'Calle 1 # 2-3, Bogotá',
            'celular' => '3001234567',
            ...$sobrescribir,
        ];
    }

    private function opcionId(int $posicion): int
    {
        return OpcionRuleta::ordenadas()->get()[$posicion]->id;
    }

    public function test_the_form_sends_the_participant_to_the_roulette_without_saving_yet(): void
    {
        $this->post(route('participantes.store'), $this->datos())
            ->assertRedirect(route('ruleta.show'));

        $this->assertDatabaseCount('participantes', 0);
    }

    public function test_the_form_is_still_validated(): void
    {
        $this->post(route('participantes.store'), $this->datos(['celular' => '12345']))
            ->assertSessionHasErrors('celular');

        $this->get(route('ruleta.show'))->assertRedirect(route('home'));
    }

    public function test_the_roulette_requires_a_completed_form(): void
    {
        $this->get(route('ruleta.show'))->assertRedirect(route('home'));
        $this->post(route('ruleta.store'), ['opcion' => $this->opcionId(0)])->assertRedirect(route('home'));

        $this->assertDatabaseCount('participantes', 0);
    }

    public function test_the_roulette_shows_every_option(): void
    {
        $this->post(route('participantes.store'), $this->datos());

        $this->get(route('ruleta.show'))
            ->assertOk()
            ->assertSeeText('Ana Torres')
            ->assertSee('Opción 1')
            ->assertSee('Opción 2')
            ->assertSee('Opción 3');
    }

    public function test_the_chosen_option_is_saved_with_the_participant(): void
    {
        $this->post(route('participantes.store'), $this->datos());

        $this->post(route('ruleta.store'), ['opcion' => $this->opcionId(1)])
            ->assertRedirect(route('home'))
            ->assertSessionHas('registrado');

        $this->assertDatabaseHas('participantes', [
            'nombre' => 'Ana Torres',
            'cedula' => '1020304050',
            'direccion' => 'Calle 1 # 2-3, Bogotá',
            'celular' => '3001234567',
            'opcion' => 'Opción 2',
        ]);
    }

    public function test_an_invalid_option_is_rejected_and_nothing_is_saved(): void
    {
        $this->post(route('participantes.store'), $this->datos());

        $this->post(route('ruleta.store'), ['opcion' => 9999])->assertSessionHasErrors('opcion');
        $this->post(route('ruleta.store'), [])->assertSessionHasErrors('opcion');

        $this->assertDatabaseCount('participantes', 0);
    }

    public function test_a_participant_cannot_spin_twice_with_the_same_form(): void
    {
        $this->post(route('participantes.store'), $this->datos());
        $this->post(route('ruleta.store'), ['opcion' => $this->opcionId(0)]);

        $this->post(route('ruleta.store'), ['opcion' => $this->opcionId(2)])->assertRedirect(route('home'));

        $this->assertDatabaseCount('participantes', 1);
        $this->assertDatabaseHas('participantes', ['opcion' => 'Opción 1']);
    }

    public function test_a_cedula_registered_in_the_meantime_is_not_duplicated(): void
    {
        $this->post(route('participantes.store'), $this->datos());
        Participante::factory()->create(['cedula' => '1020304050']);

        $this->post(route('ruleta.store'), ['opcion' => $this->opcionId(0)])
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('cedula');

        $this->assertDatabaseCount('participantes', 1);
    }
}
