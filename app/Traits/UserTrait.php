<?php

namespace App\Traits;

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
            'address' => $data['address']
        ]);
        return true;
    }
}
