<?php

namespace App\Observers;

use App\Models\Archive\Category;
use App\Models\Permission;
use Illuminate\Support\Str;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        // Check if the category has a parent (i.e., it's a subcategory)
        if ($category->parent_id) {
            $parentCategory = Category::find($category->parent_id);

            // Generate the permission name using the parent and subcategory names
            $permissionName = 'upload-archive-' . Str::slug($parentCategory->name . '-' . $category->name);

            // Check if the permission already exists in the database
            if (!Permission::where('name', $permissionName)->exists()) {
                // Create the permission
                Permission::create(['name' => $permissionName]);
            }
        }
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }
}
