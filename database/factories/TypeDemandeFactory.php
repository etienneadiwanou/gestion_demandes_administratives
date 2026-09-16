<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TypeDemandeFactory extends Factory
{
    public function definition(): array
    {
        $nom = fake()->unique()->words(3, true);

        return [
            'nom' => $nom,
            'code' => Str::slug($nom),
            'categorie' => fake()->randomElement(['Congés', 'Attestations', 'Autorisations']),
            'description' => fake()->sentence(),
            'actif' => true,
        ];
    }
}
