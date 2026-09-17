<?php

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Crée le compte administrateur initial. Les identifiants se
     * configurent via .env (ADMIN_NAME / ADMIN_EMAIL / ADMIN_PASSWORD) :
     * en local, les valeurs par défaut ci-dessous suffisent ; en
     * production, définis ADMIN_EMAIL et ADMIN_PASSWORD dans le .env
     * du serveur pour créer TON compte, pas celui de démo.
     */
    public function run(): void
    {
        $roleAdmin = Role::where('slug', RoleSlug::Administrateur->value)->first();

        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@adminflow.test')],
            [
                'name' => env('ADMIN_NAME', 'Administrateur AdminFlow'),
                'password' => env('ADMIN_PASSWORD', 'password'), // hashé automatiquement (cast 'hashed')
                'role_id' => $roleAdmin?->id,
                'actif' => true,
            ]
        );
    }
}
