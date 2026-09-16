<?php

namespace App\Services;

use App\Enums\StatutDemande;
use App\Models\Affectation;
use App\Models\Demande;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AffectationService
{
    public function __construct(
        private readonly AuditLogService $auditLog,
    ) {
    }

    /**
     * Affecte une demande à un agent. Clôture l'affectation active
     * précédente s'il y en a une, et fait passer la demande soumise en
     * vérification.
     */
    public function affecter(Demande $demande, User $agent, User $affectePar, ?string $commentaire = null): Affectation
    {
        return DB::transaction(function () use ($demande, $agent, $affectePar, $commentaire) {
            $demande->affectations()
                ->where('statut', 'active')
                ->update(['statut' => 'terminee', 'date_fin' => now()]);

            $affectation = $demande->affectations()->create([
                'agent_id' => $agent->id,
                'affecte_par_id' => $affectePar->id,
                'statut' => 'active',
                'commentaire' => $commentaire,
                'date_affectation' => now(),
            ]);

            if ($demande->statut === StatutDemande::Soumise) {
                $demande->update(['statut' => StatutDemande::EnVerification]);
            }

            $this->auditLog->log($affectePar->id, 'affectation', $demande, null, [
                'agent_id' => $agent->id,
                'affectation_id' => $affectation->id,
            ]);

            return $affectation;
        });
    }
}
