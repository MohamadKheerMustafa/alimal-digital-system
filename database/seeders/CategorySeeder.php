<?php

namespace Database\Seeders;

use App\Models\Archive\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Graphic Design', 'Developers', 'Marketing', 'General', 'E-Commerce'];
        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
