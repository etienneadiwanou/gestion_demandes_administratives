<?php

namespace Database\Factories;

use App\Models\TypeDemande;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChampDemandeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type_demande_id' => TypeDemande::factory(),
            'label' => fake()->words(2, true),
            'nom_technique' => fake()->unique()->lexify('champ_????'),
            'type_champ' => 'texte',
            'obligatoire' => false,
            'options' => null,
            'ordre' => 0,
        ];
    }
}
