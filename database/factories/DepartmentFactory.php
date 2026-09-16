<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => fake()->unique()->company(),
            'code' => strtoupper(fake()->unique()->lexify('DEP-???')),
            'description' => fake()->sentence(),
            'actif' => true,
        ];
    }
}
