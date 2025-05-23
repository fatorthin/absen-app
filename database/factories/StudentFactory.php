<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'name' => fake()->name(),
            'birthdate' => fake()->dateTimeBetween('-20 years', '-14 years'),
            'gender' => fake()->randomElement(['L', 'P']),
            'no_wa' => fake()->numerify('08##########'),
            'group_id' => Group::inRandomOrder()->first()->id ?? 1, // Fallback to ID 1 if no groups exist
            'class' => fake()->randomElement(['1 SMA', '2 SMA', '3 SMA', '1 SMP', '2 SMP', '3 SMP', 'GP MT']),
            'avatar' => '01JK7C4EH4WRE2DA74X19YJNEE.jpg',
            'parent_name' => fake()->name(),
            'parent_phone' => fake()->numerify('08##########'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Define the Student's class.
     */
    public function inClass(string $class): static
    {
        return $this->state(fn (array $attributes) => [
            'class' => $class,
        ]);
    }

    /**
     * Define the Student's gender.
     */
    public function withGender(string $gender): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => $gender,
        ]);
    }
} 