<?php

namespace Database\Seeders;

use App\Models\ChampDemande;
use App\Models\TypeDemande;
use Illuminate\Database\Seeder;

class TypeDemandeSeeder extends Seeder
{
    public function run(): void
    {
        $congeAnnuel = TypeDemande::firstOrCreate(
            ['code' => 'conge-annuel'],
            ['nom' => 'Congé annuel', 'categorie' => 'Congés', 'actif' => true],
        );

        $this->creerChamps($congeAnnuel, [
            ['label' => 'Date de début', 'nom_technique' => 'date_debut', 'type_champ' => 'date', 'obligatoire' => true, 'ordre' => 1],
            ['label' => 'Date de fin', 'nom_technique' => 'date_fin', 'type_champ' => 'date', 'obligatoire' => true, 'ordre' => 2],
            ['label' => 'Motif', 'nom_technique' => 'motif', 'type_champ' => 'textarea', 'obligatoire' => false, 'ordre' => 3],
        ]);

        $attestation = TypeDemande::firstOrCreate(
            ['code' => 'attestation-travail'],
            ['nom' => 'Attestation de travail', 'categorie' => 'Attestations', 'actif' => true],
        );

        $this->creerChamps($attestation, [
            ['label' => 'Motif de la demande', 'nom_technique' => 'motif', 'type_champ' => 'textarea', 'obligatoire' => true, 'ordre' => 1],
            ['label' => 'Destinataire', 'nom_technique' => 'destinataire', 'type_champ' => 'texte', 'obligatoire' => false, 'ordre' => 2],
        ]);
    }

    private function creerChamps(TypeDemande $typeDemande, array $champs): void
    {
        foreach ($champs as $champ) {
            ChampDemande::firstOrCreate(
                [
                    'type_demande_id' => $typeDemande->id,
                    'nom_technique' => $champ['nom_technique'],
                ],
                $champ,
            );
        }
    }
}
