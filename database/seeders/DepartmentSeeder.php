<?php

namespace Database\Seeders;

use App\Models\HR\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Graphic Design', 'description' => 'Handles all graphic design work'],
            ['name' => 'Software Development', 'description' => 'Develops software solutions and maintains codebase'],
            ['name' => 'Marketing', 'description' => 'Responsible for marketing and outreach'],
            ['name' => 'E-commerce', 'description' => 'Manages online sales and product listings'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
