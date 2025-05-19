<?php

namespace Database\Factories;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['1 SMP', '2 SMP', '3 SMP', '1 SMA', '2 SMA', '3 SMA']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Create a Classroom with a specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }
} 