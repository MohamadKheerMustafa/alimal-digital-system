<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        $permissions = [
            ['name' => 'view-category', 'display_name' => 'View Category', 'description' => 'View Categories'],
            ['name' => 'add-category', 'display_name' => 'Add New Category', 'description' => 'Add New Categories'],
            ['name' => 'update-category', 'display_name' => 'Update an existing Category', 'description' => 'Update an existing Categories'],
            ['name' => 'delete-category', 'display_name' => 'Delete Category', 'description' => 'Delete Categories'],
        ];

        foreach ($permissions as $permission) {
            $permissionCreated = Permission::create($permission);
            $adminRole->givePermission($permissionCreated);
        }
    }
}
