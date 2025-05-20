<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'name' => fake()->name(),
            'group_id' => Group::inRandomOrder()->first()->id ?? 1,
            'phone' => fake()->numerify('08##########'),
            'role' => fake()->randomElement(['admin', 'teacher']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
