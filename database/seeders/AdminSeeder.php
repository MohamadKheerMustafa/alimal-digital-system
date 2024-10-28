<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Retrieve the "Admin" role created in LaratrustSeeder
        $adminRole = Role::where('name', 'admin')->first();

        // Create the Admin user if not already created
        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com' // Change email as needed
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'), // Replace with a secure password
        ]);

        // Attach the "Admin" role to the user
        if ($adminRole) {
            $adminUser->addRole($adminRole);
        }

        // Create a profile for the Admin user
        Profile::firstOrCreate([
            'user_id' => $adminUser->id
        ], [
            'position' => 'System Administrator',
            'department_id' => null,
            'contact_number' => '00963931443885',
            'address' => '123 Admin Lane, Admin City, Country',
        ]);
    }
}
