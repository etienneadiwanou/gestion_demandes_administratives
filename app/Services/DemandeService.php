<?php

namespace App\Services;

use App\Enums\StatutDemande;
use App\Models\Demande;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DemandeService
{
    public function __construct(
        private readonly AuditLogService $auditLog,
    ) {
    }

    /**
     * Crée une demande en brouillon avec ses valeurs de champs dynamiques.
     */
    public function creer(User $auteur, array $donnees): Demande
    {
        return DB::transaction(function () use ($auteur, $donnees) {
            $demande = Demande::create([
                'reference' => $this->genererReference(),
                'user_id' => $auteur->id,
                'type_demande_id' => $donnees['type_demande_id'],
                'department_id' => $donnees['department_id'] ?? $auteur->department_id,
                'statut' => StatutDemande::Brouillon,
                'priorite' => $donnees['priorite'] ?? 'normale',
                'commentaire' => $donnees['commentaire'] ?? null,
            ]);

            $this->synchroniserValeurs($demande, $donnees['valeurs'] ?? []);

            $this->auditLog->log($auteur->id, 'creation', $demande, null, $demande->fresh()->toArray());

            return $demande->fresh('valeurs');
        });
    }

    /**
     * Met à jour une demande tant qu'elle est modifiable par son auteur
     * (brouillon, ou complément demandé — auquel cas elle retourne en
     * vérification).
     */
    public function mettreAJour(Demande $demande, array $donnees): Demande
    {
        if (! $this->estModifiable($demande)) {
            throw ValidationException::withMessages([
                'statut' => "Cette demande ne peut plus être modifiée dans son statut actuel ({$demande->statut->value}).",
            ]);
        }

        return DB::transaction(function () use ($demande, $donnees) {
            $avant = $demande->toArray();

            $demande->fill([
                'department_id' => $donnees['department_id'] ?? $demande->department_id,
                'priorite' => $donnees['priorite'] ?? $demande->priorite,
                'commentaire' => $donnees['commentaire'] ?? $demande->commentaire,
            ]);

            if ($demande->statut === StatutDemande::ComplementDemande) {
                $demande->statut = StatutDemande::EnVerification;
            }

            $demande->save();

            if (array_key_exists('valeurs', $donnees)) {
                $this->synchroniserValeurs($demande, $donnees['valeurs']);
            }

            $this->auditLog->log($demande->user_id, 'modification', $demande, $avant, $demande->fresh()->toArray());

            return $demande->fresh('valeurs');
        });
    }

    /**
     * Soumet la demande : contrôle des champs obligatoires puis
     * transition brouillon -> soumise.
     */
    public function soumettre(Demande $demande): Demande
    {
        if ($demande->statut !== StatutDemande::Brouillon) {
            throw ValidationException::withMessages([
                'statut' => 'Seule une demande en brouillon peut être soumise.',
            ]);
        }

        $this->verifierChampsObligatoires($demande);

        $avant = $demande->toArray();

        $demande->statut = StatutDemande::Soumise;
        $demande->date_soumission = now();
        $demande->save();

        $this->auditLog->log($demande->user_id, 'soumission', $demande, $avant, $demande->fresh()->toArray());

        return $demande->fresh();
    }

    public function estModifiable(Demande $demande): bool
    {
        return in_array($demande->statut, [StatutDemande::Brouillon, StatutDemande::ComplementDemande], true);
    }

    private function verifierChampsObligatoires(Demande $demande): void
    {
        $demande->loadMissing(['typeDemande.champs', 'valeurs']);

        $valeursParChamp = $demande->valeurs->keyBy('champ_demande_id');

        $manquants = $demande->typeDemande->champs
            ->where('obligatoire', true)
            ->reject(fn ($champ) => filled($valeursParChamp->get($champ->id)?->valeur))
            ->pluck('label');

        if ($manquants->isNotEmpty()) {
            throw ValidationException::withMessages([
                'valeurs' => 'Champs obligatoires manquants : '.$manquants->implode(', '),
            ]);
        }
    }

    private function synchroniserValeurs(Demande $demande, array $valeurs): void
    {
        foreach ($valeurs as $valeur) {
            $demande->valeurs()->updateOrCreate(
                ['champ_demande_id' => $valeur['champ_demande_id']],
                ['valeur' => $valeur['valeur'] ?? null],
            );
        }
    }

    /**
     * Génère une référence lisible du type DEM-2026-000123.
     *
     * Approche simple pour le MVP : à surveiller en cas de fortes
     * créations concurrentes (préférer une séquence dédiée en base si
     * le volume l'exige).
     */
    private function genererReference(): string
    {
        $annee = now()->year;

        do {
            $sequence = Demande::whereYear('created_at', $annee)->count() + 1;
            $reference = sprintf('DEM-%d-%06d', $annee, $sequence);
        } while (Demande::where('reference', $reference)->exists());

        return $reference;
    }
}
