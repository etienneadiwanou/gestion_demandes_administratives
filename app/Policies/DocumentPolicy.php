<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    // L'autorisation d'ajout d'un document se fait via
    // DemandePolicy::uploadDocument (le document n'existe pas encore).

    public function view(User $user, Document $document): bool
    {
        return $user->id === $document->demande->user_id
            || $user->hasRole(RoleSlug::Administrateur)
            || $user->hasRole(RoleSlug::Agent)
            || $user->hasRole(RoleSlug::Validateur);
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->id === $document->user_id || $user->hasRole(RoleSlug::Administrateur);
    }
}
