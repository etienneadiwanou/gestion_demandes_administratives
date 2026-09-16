<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        // Nécessaire pour l'assignation d'un rôle à un utilisateur.
        return true;
    }

    public function view(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrateur);
    }

    public function update(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrateur);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrateur);
    }
}
