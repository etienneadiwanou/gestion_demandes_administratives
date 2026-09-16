<?php

namespace App\Services;

use App\Enums\StatutDemande;
use App\Models\Demande;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkflowService
{
    public function __construct(
        private readonly AuditLogService $auditLog,
    ) {
    }

    /**
     * Transmet une demande vérifiée à l'étape de validation.
     */
    public function transmettreEnValidation(Demande $demande, User $agent): Demande
    {
        $this->verifierStatut($demande, [StatutDemande::EnVerification]);

        return $this->changerStatut($demande, StatutDemande::EnValidation, $agent, 'transmission_validation');
    }

    /**
     * Renvoie une demande à l'utilisateur pour complément
     * d'information ou de documents.
     */
    public function demanderComplement(Demande $demande, User $acteur, string $commentaire): Demande
    {
        $this->verifierStatut($demande, [StatutDemande::EnVerification, StatutDemande::EnValidation]);

        $demande->commentaire = $commentaire;

        return $this->changerStatut($demande, StatutDemande::ComplementDemande, $acteur, 'demande_complement');
    }

    /**
     * Archive une demande clôturée (approuvée ou rejetée) sans perdre
     * son historique.
     */
    public function archiver(Demande $demande, User $acteur): Demande
    {
        $this->verifierStatut($demande, [StatutDemande::Approuvee, StatutDemande::Rejetee]);

        return $this->changerStatut($demande, StatutDemande::Archivee, $acteur, 'archivage');
    }

    private function verifierStatut(Demande $demande, array $statutsAttendus): void
    {
        if (! in_array($demande->statut, $statutsAttendus, true)) {
            throw ValidationException::withMessages([
                'statut' => "Cette action n'est pas autorisée depuis le statut actuel ({$demande->statut->value}).",
            ]);
        }
    }

    private function changerStatut(Demande $demande, StatutDemande $nouveauStatut, User $acteur, string $action): Demande
    {
        return DB::transaction(function () use ($demande, $nouveauStatut, $acteur, $action) {
            $avant = $demande->toArray();

            $demande->statut = $nouveauStatut;

            if ($nouveauStatut === StatutDemande::Archivee) {
                $demande->date_cloture ??= now();
            }

            $demande->save();

            $this->auditLog->log($acteur->id, $action, $demande, $avant, $demande->fresh()->toArray());

            return $demande->fresh();
        });
    }
}
