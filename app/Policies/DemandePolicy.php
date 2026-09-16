<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Enums\StatutDemande;
use App\Models\Demande;
use App\Models\User;

class DemandePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Demande $demande): bool
    {
        return $user->id === $demande->user_id
            || $user->hasRole(RoleSlug::Administrateur)
            || $user->hasRole(RoleSlug::Agent)
            || $user->hasRole(RoleSlug::Validateur);
    }

    public function create(User $user): bool
    {
        // Toute personne authentifiée peut initier une demande.
        return true;
    }

    public function update(User $user, Demande $demande): bool
    {
        return $user->id === $demande->user_id
            && in_array($demande->statut, [StatutDemande::Brouillon, StatutDemande::ComplementDemande], true);
    }

    public function delete(User $user, Demande $demande): bool
    {
        return $user->id === $demande->user_id && $demande->statut === StatutDemande::Brouillon;
    }

    public function submit(User $user, Demande $demande): bool
    {
        return $user->id === $demande->user_id && $demande->statut === StatutDemande::Brouillon;
    }

    public function uploadDocument(User $user, Demande $demande): bool
    {
        return $user->id === $demande->user_id
            || $user->hasRole(RoleSlug::Administrateur)
            || $user->hasRole(RoleSlug::Agent);
    }

    public function assign(User $user, Demande $demande): bool
    {
        return $user->hasRole(RoleSlug::Administrateur) || $user->hasRole(RoleSlug::Agent);
    }

    public function requestComplement(User $user, Demande $demande): bool
    {
        return $user->hasRole(RoleSlug::Administrateur)
            || $user->hasRole(RoleSlug::Agent)
            || $user->hasRole(RoleSlug::Validateur);
    }

    public function transmitForValidation(User $user, Demande $demande): bool
    {
        return $user->hasRole(RoleSlug::Administrateur) || $user->hasRole(RoleSlug::Agent);
    }

    public function decide(User $user, Demande $demande): bool
    {
        return $user->hasRole(RoleSlug::Administrateur) || $user->hasRole(RoleSlug::Validateur);
    }

    public function archive(User $user, Demande $demande): bool
    {
        return $user->hasRole(RoleSlug::Administrateur) || $user->hasRole(RoleSlug::Validateur);
    }
}
