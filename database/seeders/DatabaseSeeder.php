<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Toujours sûr à exécuter, y compris en production : données de
     * référence (rôles, permissions, départements) + le compte admin
     * (avec les identifiants définis dans .env, voir AdminUserSeeder).
     *
     * DemoUsersSeeder (comptes de test employe/agent/validateur avec
     * le mot de passe "password") ne tourne qu'en dehors de la
     * production, pour ne jamais exposer ces identifiants publics.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            DepartmentSeeder::class,
            AdminUserSeeder::class,
            TypeDemandeSeeder::class,
        ]);

        if (! app()->environment('production')) {
            $this->call([
                DemoUsersSeeder::class,
            ]);
        }
    }
}
