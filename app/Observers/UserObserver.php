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
        // $profile = $user->profile();
        // $department = $profile->department();
        // $permissionName = 'upload-archive-' . $user->name;

        // // Main Category!!
        // $parentCategory = Category::where('name', $department->name)->first();

        // $subCategoryCreation = Category::create([
        //     'name' => $permissionName,
        //     'parent_id' => $parentCategory->id
        // ]);


        // // Check if the permission already exists in the database
        // if (!Permission::where('name', $permissionName)->exists()) {
        //     // Create the permission
        //     $permission = Permission::create(['name' => $permissionName]);
        //     $user->givePermission($permission);
        // }
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
