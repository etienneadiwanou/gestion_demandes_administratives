<?php

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $departement = Department::first();

        $comptes = [
            ['email' => 'employe@adminflow.test', 'name' => 'Employé Test', 'role' => RoleSlug::Employe],
            ['email' => 'agent@adminflow.test', 'name' => 'Agent Test', 'role' => RoleSlug::Agent],
            ['email' => 'validateur@adminflow.test', 'name' => 'Validateur Test', 'role' => RoleSlug::Validateur],
        ];

        foreach ($comptes as $compte) {
            $role = Role::where('slug', $compte['role']->value)->first();

            User::firstOrCreate(
                ['email' => $compte['email']],
                [
                    'name' => $compte['name'],
                    'password' => 'password', // hashé automatiquement (cast 'hashed')
                    'role_id' => $role?->id,
                    'department_id' => $departement?->id,
                    'actif' => true,
                ],
            );
        }
    }
}
