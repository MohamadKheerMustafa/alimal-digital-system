<?php

namespace App\Observers;

use App\Models\Archive\Category;
use App\Models\Permission;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $department = $user->profile()->department();

        // Main Category!!
        $parentCategory = Category::where('name', $department->name)->first();

        // Generate the permission name using the parent and subcategory names
        $permissionName = 'upload-archive-' . Str::slug($parentCategory->name . '-' . $department->name);

        // Check if the permission already exists in the database
        if (!Permission::where('name', $permissionName)->exists()) {
            // Create the permission
            $permission = Permission::create(['name' => $permissionName]);
            $user->givePermission($permission);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
