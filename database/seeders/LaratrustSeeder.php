<?php

namespace Database\Seeders;

use App\Models\HR\Department;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define permissions
        $permissions = [
            // User management
            ['name' => 'create_users', 'display_name' => 'Create Users', 'description' => 'Create new users'],
            ['name' => 'view_users', 'display_name' => 'View Users', 'description' => 'View users list'],
            ['name' => 'update_users', 'display_name' => 'Update Users', 'description' => 'Update user information'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'description' => 'Delete users'],

            // Department management
            ['name' => 'create_departments', 'display_name' => 'Create Departments', 'description' => 'Create new departments'],
            ['name' => 'view_departments', 'display_name' => 'View Departments', 'description' => 'View departments list'],
            ['name' => 'update_departments', 'display_name' => 'Update Departments', 'description' => 'Update department information'],
            ['name' => 'delete_departments', 'display_name' => 'Delete Departments', 'description' => 'Delete departments'],

            // Category management
            ['name' => 'create_categories', 'display_name' => 'Create Categories', 'description' => 'Create new categories'],
            ['name' => 'view_categories', 'display_name' => 'View Categories', 'description' => 'View categories list'],
            ['name' => 'update_categories', 'display_name' => 'Update Categories', 'description' => 'Update category information'],
            ['name' => 'delete_categories', 'display_name' => 'Delete Categories', 'description' => 'Delete categories'],

            // Archive permissions for employees and managers
            ['name' => 'manage_own_archive', 'display_name' => 'Manage Own Archive', 'description' => 'Upload and download files in own archive'],
            ['name' => 'view_department_archives', 'display_name' => 'View Department Archives', 'description' => 'View all archives within the department'],
            ['name' => 'approve_archive_requests', 'display_name' => 'Approve Archive Requests', 'description' => 'Approve or reject requests for updating or deleting archives'],
            ['name' => 'delete_archives', 'display_name' => 'Delete Archives', 'description' => 'Delete files within department archives'],
            ['name' => 'update_archives', 'display_name' => 'Update Archives', 'description' => 'Update files within department archives'],

            // Full system access for Super Admin
            ['name' => 'full_system_access', 'display_name' => 'Full System Access', 'description' => 'Access all parts of the system'],
        ];

        // Create permissions
        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(['name' => $permissionData['name']], $permissionData);
        }

        // Define roles with permissions
        $roles = [
            // Super Admin with all permissions
            'super_admin' => Permission::pluck('name')->toArray(),

            // Department-specific roles
            'software_development_manager' => ['view_department_archives', 'approve_archive_requests', 'delete_archives', 'update_archives'],
            'graphic_design_manager' => ['view_department_archives', 'approve_archive_requests', 'delete_archives', 'update_archives'],
            'marketing_manager' => ['view_department_archives', 'approve_archive_requests', 'delete_archives', 'update_archives'],
            'e-commerce_manager' => ['view_department_archives', 'approve_archive_requests', 'delete_archives', 'update_archives'],

            // Employee roles
            'software_development_employee' => ['manage_own_archive'],
            'graphic_design_employee' => ['manage_own_archive'],
            'marketing_employee' => ['manage_own_archive'],
            'e-commerce_employee' => ['manage_own_archive'],
        ];

        // Create roles and attach permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            foreach ($rolePermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                $role->givePermission($permission);
            }
        }

        // Create a Super Admin user and assign the super_admin role
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'), // Change 'password' to a secure password
            ]
        );

        $superAdminUser->profile()->create([
            'department_id' => null,
            'is_manager' => true,
            'position' => 'Super Admin',
            'contact_number' => '+963931443885',
            'address' => 'Damascus',
            'image' => '/uploads/profile_images/super-admin.jpeg'
        ]);

        if ($superAdminUser) {
            $superAdminUser->addRole('super_admin'); // Assign Super Admin role to user
        }
    }
}
