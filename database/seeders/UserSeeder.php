<?php

namespace Database\Seeders;

use App\Models\HR\Department;
use App\Models\Permission;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Retrieve departments and roles
        $departments = Department::all();

        foreach ($departments as $department) {
            // Create a department-specific upload permission, e.g., upload-archive-GD
            $permissionName = 'upload-archive-' . str_replace(' ', '-', strtolower($department->name));
            $uploadPermission = Permission::firstOrCreate(
                ['name' => $permissionName],
                [
                    'display_name' => 'Upload Archive for ' . $department->name,
                    'description' => 'Permission to upload files to the archive for the ' . $department->name . ' department'
                ]
            );

            // Create a manager role for each department
            $managerRoleName = substr(str_replace(' ', '-', strtoupper($department->name)), 0, 3) . '-manager';
            $managerRole = Role::firstOrCreate(
                ['name' => $managerRoleName],
                [
                    'display_name' => $department->name . ' Manager',
                    'description' => 'Manages the ' . $department->name . ' department'
                ]
            );

            // Assign department-specific upload permission to the manager role
            $managerRole->permissions()->attach($uploadPermission);

            // Create a manager user for each department and assign the role and profile
            $manager = User::firstOrCreate(
                ['email' => strtolower($managerRoleName) . '@example.com'],
                [
                    'name' => $department->name . ' Manager',
                    'password' => Hash::make('password'), // Default password for seeding
                ]
            );

            // Attach manager role
            $manager->roles()->attach($managerRole);

            // Create profile for manager with dummy data
            Profile::firstOrCreate(
                ['user_id' => $manager->id],
                [
                    'position' => $managerRoleName,
                    'department_id' => $department->id,
                    'contact_number' => '123-456-7890', // Dummy contact number
                    'address' => '123 Manager St, City, Country' // Dummy address
                ]
            );

            // Create a regular employee in each department
            $employeeRole = Role::firstOrCreate(
                ['name' => 'employee'],
                [
                    'display_name' => 'Employee',
                    'description' => 'Regular employee with limited access'
                ]
            );

            $employee = User::firstOrCreate(
                ['email' => strtolower($department->name) . '-employee@example.com'],
                [
                    'name' => $department->name . ' Employee',
                    'password' => Hash::make('password'), // Default password for seeding
                ]
            );

            // Attach employee role
            $employee->roles()->attach($employeeRole);

            // Create profile for employee with dummy data
            Profile::firstOrCreate(
                ['user_id' => $employee->id],
                [
                    'position' => 'Employee',
                    'department_id' => $department->id,
                    'contact_number' => '987-654-3210', // Dummy contact number
                    'address' => '456 Employee Ave, City, Country' // Dummy address
                ]
            );
        }
    }
}
