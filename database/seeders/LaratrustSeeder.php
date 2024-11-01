<?php

namespace Database\Seeders;

use App\Models\HR\Department;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define general roles
        $roles = [
            'admin' => 'Has access to all system features and settings',
            'manager' => 'Can manage team and department resources',
            'employee' => 'Regular employee with limited access'
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(['name' => $name], ['display_name' => ucfirst($name), 'description' => $description]);
        }

        // Define and create general permissions
        $permissions = [
            ['name' => 'manage-users', 'display_name' => 'Manage Users', 'description' => 'Create, edit, and delete users'],
            ['name' => 'view-departments', 'display_name' => 'View Departments', 'description' => 'View department data'],
            ['name' => 'create-departments', 'display_name' => 'Create Departments', 'description' => 'Create new departments'],
            ['name' => 'update-departments', 'display_name' => 'Update Departments', 'description' => 'Edit department information'],
            ['name' => 'delete-departments', 'display_name' => 'Delete Departments', 'description' => 'Remove departments from system'],
            ['name' => 'view-archives', 'display_name' => 'View Archives', 'description' => 'Access archives'],
            ['name' => 'upload-archives', 'display_name' => 'upload Archives only admin', 'description' => 'Access archives'],
            ['name' => 'delete-archives', 'display_name' => 'Delete Archives', 'description' => 'Delete files from archives'],
        ];

        // Collect all permissions
        $allPermissions = [];

        foreach ($permissions as $permData) {
            $permission = Permission::firstOrCreate(['name' => $permData['name']], $permData);
            $allPermissions[] = $permission->id; // Collect permission IDs
        }

        // Retrieve departments and create department-specific roles and upload permissions
        $departments = Department::all();

        foreach ($departments as $department) {
            $roleName = substr(str_replace(' ', '-', strtoupper($department->name)), 0, 3) . '-manager';
            $managerRole = Role::firstOrCreate(
                ['name' => $roleName],
                [
                    'display_name' => $department->name . ' Manager',
                    'description' => 'Manages the ' . $department->name . ' department'
                ]
            );

            $permissionName = 'upload-archive-' . str_replace(' ', '-', strtolower($department->name));
            $uploadPermission = Permission::firstOrCreate(
                ['name' => $permissionName],
                [
                    'display_name' => 'Upload Archive for ' . $department->name,
                    'description' => 'Upload files to the archive for the ' . $department->name . ' department'
                ]
            );

            $managerRole->permissions()->attach($uploadPermission);
            $allPermissions[] = $uploadPermission->id; // Collect department-specific permission IDs
        }

        // Assign all collected permissions to the admin role
        $admin = Role::where('name', 'admin')->first();
        $admin->permissions()->sync($allPermissions);

        // Assign relevant permissions to general manager and employee roles
        $generalManagerPermissions = Permission::whereIn('name', [
            'view-departments',
            'update-departments',
            'view-archives',
        ])->get();

        $departmentUploadPermissions = Permission::where('name', 'like', 'upload-archive-%')->get();
        $generalManagerPermissions = $generalManagerPermissions->merge($departmentUploadPermissions);
        $manager = Role::where('name', 'manager')->first();
        $manager->permissions()->sync($generalManagerPermissions);

        $employeePermissions = Permission::whereIn('name', ['view-departments', 'view-archives'])->get();
        $employee = Role::where('name', 'employee')->first();
        $employee->permissions()->sync($employeePermissions);

        // Assign permissions to department-specific manager roles
        foreach ($departments as $department) {
            $roleName = substr(str_replace(' ', '-', strtoupper($department->name)), 0, 3) . '-manager';
            $departmentManagerRole = Role::where('name', $roleName)->first();

            $permissionsForRole = $generalManagerPermissions->filter(function ($permission) use ($department) {
                return str_contains($permission->name, 'upload-archive-' . str_replace(' ', '-', strtolower($department->name))) ||
                    in_array($permission->name, ['view-departments', 'view-archives']);
            });

            $departmentManagerRole->permissions()->sync($permissionsForRole);
        }
    }
}
