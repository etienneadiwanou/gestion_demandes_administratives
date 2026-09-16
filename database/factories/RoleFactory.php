<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoleFactory extends Factory
{
    public function definition(): array
    {
        $nom = fake()->unique()->jobTitle();

        return [
            'nom' => $nom,
            'slug' => Str::slug($nom),
            'description' => fake()->sentence(),
        ];
    }
}
