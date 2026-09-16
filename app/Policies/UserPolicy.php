<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrateur);
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasRole(RoleSlug::Administrateur);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrateur);
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasRole(RoleSlug::Administrateur);
    }

    public function delete(User $user, User $model): bool
    {
        // Un administrateur ne peut pas se désactiver lui-même.
        return $user->hasRole(RoleSlug::Administrateur) && $user->id !== $model->id;
    }
}
