<?php

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            RoleSlug::Employe->value => 'Employé',
            RoleSlug::Agent->value => 'Agent administratif',
            RoleSlug::Validateur->value => 'Responsable / Validateur',
            RoleSlug::Administrateur->value => 'Administrateur',
        ];

        foreach ($roles as $slug => $nom) {
            Role::firstOrCreate(['slug' => $slug], ['nom' => $nom]);
        }
    }
}
