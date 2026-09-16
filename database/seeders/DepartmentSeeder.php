<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['nom' => 'Ressources Humaines', 'code' => 'RH'],
            ['nom' => 'Informatique', 'code' => 'IT'],
            ['nom' => 'Finance', 'code' => 'FIN'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['code' => $department['code']], $department);
        }
    }
}
