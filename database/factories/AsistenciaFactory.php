<?php

namespace Database\Factories;

use App\Models\Asistencia;
use App\Models\Evento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asistencia>
 */
class AsistenciaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Asistencia::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'evento_id' => Evento::factory(),
            'empleado_id' => $this->faker->numberBetween(1, 10),
            'asistio' => $this->faker->randomElement(['Sí', 'No', 'Pendiente']),
            'fecha_asistencia' => $this->faker->date(),
            'hora_asistencia' => $this->faker->time(),
            'comentario' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement(['Registrada', 'Confirmada', 'Cancelada']),
        ];
    }
} 