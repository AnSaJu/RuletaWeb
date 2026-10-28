<?php

namespace Database\Factories;

use App\Models\Participante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Participante>
 */
class ParticipanteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'cedula' => (string) fake()->unique()->numberBetween(100000, 9999999999),
            'direccion' => fake()->streetAddress(),
            'celular' => '3'.fake()->numerify('#########'),
        ];
    }
}
