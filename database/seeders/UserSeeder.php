<?php

namespace Database\Seeders;

use App\Models\HR\Department;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Retrieve all departments
        $departments = Department::all();

        // Define managers with assigned names and genders
        $managerNames = [
            'Marketing' => ['name' => 'Layla Kahlous', 'gender' => 'female'],
            'Software Development' => ['name' => 'Mohamad Kheer Mustafa', 'gender' => 'male'],
            'Graphic Design' => ['name' => 'Amr Nasr', 'gender' => 'male'],
            'E-commerce' => ['name' => 'Mustafa', 'gender' => 'male'],
            'CEO' => ['name' => 'Belal Nhlawe', 'gender' => 'male'],
        ];

        // Define employees with assigned names and genders, categorized by department
        $employeeNames = [
            'Marketing' => [
                ['name' => 'Nermien As', 'gender' => 'female'],
            ],
            'Software Development' => [
                ['name' => 'Tala JA', 'gender' => 'female'],
                ['name' => 'Sara Htaht', 'gender' => 'female'],
            ],
            'E-commerce' => [
                ['name' => 'Sara s', 'gender' => 'female'],
            ],
            'Graphic Design' => [
                ['name' => 'Bayan Ar', 'gender' => 'female'],
            ],
        ];

        foreach ($departments as $department) {
            // Check if manager data exists for the department
            if (!isset($managerNames[$department->name])) {
                Log::error("Manager data not found for department: " . $department->name);
                continue;
            }

            $managerData = $managerNames[$department->name];
            $managerAvatarPath = $this->getRandomAvatarPath($managerData['gender']);

            // Create the manager
            $manager = User::create([
                'name' => $managerData['name'],
                'email' => strtolower(str_replace(' ', '_', $managerData['name'])) . '@alimal-digital.com',
                'password' => bcrypt('password'), // Use a secure password in production
            ]);

            // Assign manager role and profile
            $managerRole = Role::where('name', strtolower(str_replace(' ', '_', $department->name)) . '_manager')->first();
            if ($managerRole) {
                $manager->addRole($managerRole);
            } else {
                Log::error("Role not found for manager of department: " . $department->name);
                continue;
            }

            Profile::create([
                'user_id' => $manager->id,
                'department_id' => $department->id,
                'is_manager' => true,
                'position' => $department->name . ' Manager',
                'gender' => $managerData['gender'],
                'image' => trim($managerAvatarPath, 'public'),
            ]);

            // Check if there are employees for the current department
            if (isset($employeeNames[$department->name])) {
                foreach ($employeeNames[$department->name] as $employeeData) {
                    $employeeAvatarPath = $this->getRandomAvatarPath($employeeData['gender']);

                    // Create each employee
                    $employee = User::create([
                        'name' => $employeeData['name'],
                        'email' => strtolower(str_replace(' ', '_', $employeeData['name'])) . "@alimal-digital.com",
                        'password' => bcrypt('password'), // Use a secure password in production
                    ]);

                    // Assign employee role and profile
                    $employeeRole = Role::where('name', strtolower(str_replace(' ', '_', $department->name)) . '_employee')->first();
                    if ($employeeRole) {
                        $employee->addRole($employeeRole);
                    } else {
                        Log::error("Role not found for employee in department: " . $department->name);
                        continue;
                    }

                    Profile::create([
                        'user_id' => $employee->id,
                        'department_id' => $department->id,
                        'position' => $department->name . ' Employee',
                        'is_manager' => false,
                        'gender' => $employeeData['gender'],
                        'image' => trim($employeeAvatarPath, 'public'),
                    ]);
                }
            } else {
                Log::info("No employees found for department: " . $department->name);
            }
        }
    }

    /**
     * Get a random avatar path based on gender.
     *
     * @param  string  $gender
     * @return string
     */
    private function getRandomAvatarPath(string $gender): string
    {
        // Define folder paths based on gender
        $folderPath = $gender === 'male' ? 'public/uploads/avatars/men' : 'public/uploads/avatars/women';

        // Get all files in the specified folder
        $files = Storage::files($folderPath);

        // Return a random file path
        return Arr::random($files);
    }
}
