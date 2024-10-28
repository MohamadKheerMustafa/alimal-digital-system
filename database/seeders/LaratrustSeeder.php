<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles
        $roles = [
            'admin' => 'Has access to all system features and settings',
            'manager' => 'Can manage team and department resources',
            'employee' => 'Regular employee with limited access'
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(['name' => $name], ['display_name' => ucfirst($name), 'description' => $description]);
        }

        // Define permissions
        $permissions = [
            // Permissions for user management
            ['name' => 'manage-users', 'display_name' => 'Manage Users', 'description' => 'Create, edit, and delete users'],

            // Permissions for department and profile management
            ['name' => 'view-departments', 'display_name' => 'View Departments', 'description' => 'View department data'],
            ['name' => 'create-departments', 'display_name' => 'Create Departments', 'description' => 'Create new departments'],
            ['name' => 'update-departments', 'display_name' => 'Update Departments', 'description' => 'Edit department information'],
            ['name' => 'delete-departments', 'display_name' => 'Delete Departments', 'description' => 'Remove departments from system'],

            // Permissions for archives
            ['name' => 'view-archives', 'display_name' => 'View Archives', 'description' => 'Access archives'],
            ['name' => 'upload-archives', 'display_name' => 'Upload Archives', 'description' => 'Upload files to archives'],
            ['name' => 'delete-archives', 'display_name' => 'Delete Archives', 'description' => 'Delete files from archives'],
        ];

        foreach ($permissions as $permData) {
            Permission::firstOrCreate(['name' => $permData['name']], $permData);
        }

        // Assign permissions to roles
        $admin = Role::where('name', 'admin')->first();
        $manager = Role::where('name', 'manager')->first();
        $employee = Role::where('name', 'employee')->first();

        $adminPermissions = Permission::all();
        $managerPermissions = Permission::whereIn('name', [
            'view-departments',
            'update-departments',
            'view-archives',
            'upload-archives'
        ])->get();
        $employeePermissions = Permission::whereIn('name', ['view-departments', 'view-archives'])->get();

        $admin->permissions()->sync($adminPermissions);
        $manager->permissions()->sync($managerPermissions);
        $employee->permissions()->sync($employeePermissions);
    }
}
