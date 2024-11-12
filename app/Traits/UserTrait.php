<?php

namespace App\Traits;

use App\Models\Archive\Category;
use App\Models\Profile;

trait UserTrait
{

    public function createProfile($data)
    {
        // Create a profile associated with the user
        Profile::create([
            'user_id' => $data['user_id'],
            'department_id' => $data['department_id'],
            'position' => $data['position'],
            'contact_number' => $data['contact_number'],
            'address' => $data['address'],
            'image' => $data['image']
        ]);
        return true;
    }

    public function createCategory($data)
    {
        $category = Category::create([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'],
            'department_id' => $data['department_id']
        ]);
        return $category;
    }
}
