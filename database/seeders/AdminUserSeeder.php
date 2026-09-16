<?php

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $roleAdmin = Role::where('slug', RoleSlug::Administrateur->value)->first();

        User::firstOrCreate(
            ['email' => 'admin@adminflow.test'],
            [
                'name' => 'Administrateur AdminFlow',
                'password' => 'password', // hashé automatiquement (cast 'hashed' sur le model User)
                'role_id' => $roleAdmin?->id,
                'actif' => true,
            ]
        );
    }
}
