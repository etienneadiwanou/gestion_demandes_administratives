<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'departements' => ['gerer_departements'],
            'roles' => ['gerer_roles'],
            'permissions' => ['gerer_permissions'],
            'utilisateurs' => ['gerer_utilisateurs'],
            'types_demandes' => ['gerer_types_demandes'],
            'demandes' => [
                'creer_demande',
                'voir_toutes_demandes',
                'affecter_demande',
                'valider_demande',
                'archiver_demande',
            ],
        ];

        foreach ($permissions as $module => $slugs) {
            foreach ($slugs as $slug) {
                Permission::firstOrCreate(
                    ['slug' => $slug],
                    ['nom' => ucfirst(str_replace('_', ' ', $slug)), 'module' => $module],
                );
            }
        }

        // L'administrateur hérite de toutes les permissions existantes.
        $admin = Role::where('slug', 'administrateur')->first();
        $admin?->permissions()->sync(Permission::pluck('id'));
    }
}
