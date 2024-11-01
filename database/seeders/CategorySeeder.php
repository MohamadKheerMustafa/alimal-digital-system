<?php

namespace Database\Seeders;

use App\Models\Archive\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Graphic Design', 'Software Development', 'Marketing', 'General', 'E-commerce'];
        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
