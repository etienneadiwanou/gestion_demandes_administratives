<?php

namespace App\Services;

use App\Enums\DecisionValidation;
use App\Enums\StatutDemande;
use App\Models\Demande;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ValidationService
{
    public function __construct(
        private readonly AuditLogService $auditLog,
    ) {
    }

    /**
     * Enregistre la décision d'un validateur et applique la transition
     * de statut correspondante sur la demande.
     */
    public function enregistrerDecision(Demande $demande, User $validateur, DecisionValidation $decision, ?string $commentaire = null): Validation
    {
        if ($demande->statut !== StatutDemande::EnValidation) {
            throw ValidationException::withMessages([
                'statut' => 'Seule une demande en validation peut recevoir une décision.',
            ]);
        }

        return DB::transaction(function () use ($demande, $validateur, $decision, $commentaire) {
            $validation = $demande->validations()->create([
                'validateur_id' => $validateur->id,
                'decision' => $decision,
                'commentaire' => $commentaire,
            ]);

            $avant = $demande->toArray();

            $demande->statut = match ($decision) {
                DecisionValidation::Approuve => StatutDemande::Approuvee,
                DecisionValidation::Rejete => StatutDemande::Rejetee,
                DecisionValidation::ComplementDemande => StatutDemande::ComplementDemande,
            };

            if ($demande->statut !== StatutDemande::ComplementDemande) {
                $demande->date_cloture = now();
            }

            if ($commentaire) {
                $demande->commentaire = $commentaire;
            }

            $demande->save();

            $this->auditLog->log($validateur->id, 'decision_validation', $demande, $avant, $demande->fresh()->toArray());

            return $validation;
        });
    }
}
