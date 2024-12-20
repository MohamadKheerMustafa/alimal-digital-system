<?php

namespace App\Services;

use App\ApiCode;
use App\Http\Resources\Archives\ArchivesCollection;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Interfaces\CategoryInterface;
use App\Models\Archive\Category;
use Illuminate\Support\Facades\Log;

class CategoryService implements CategoryInterface
{
    public function all($request)
    {
        $user = auth()->user();
        $limit = $request->query('limit', 12);
        $page = $request->query('page', 1);
        $search = $request->query('search', null);
        $mainCategory = $request->query('mainCategory', false);
        $categoriesGrouped = $request->query('categoriesGrouped', false);

        // If grouped categories are requested
        if ($categoriesGrouped) {
            // Only fetch necessary fields and use eager loading for parent-child relationships
            $categories = Category::with('children:id,name,parent_id,department_id,owner_id')
                ->select('id', 'name', 'parent_id', 'department_id', 'owner_id')
                ->where('parent_id', null)
                ->when($search, fn($query) => $query->where('name', 'LIKE', "%$search%"))
                ->get()
                ->groupBy('parent_id')
                ->map(function ($items, $parentId) {
                    return [
                        'categories' => $items->map(fn($item) => [
                            'id' => $item->id,
                            'name' => $item->name,
                            'children' => $item->children->map(fn($child) => [
                                'id' => $child->id,
                                'name' => $child->name,
                                'department_id' => $child->department_id,
                                'owner_id' => $child->owner_id
                            ]),
                        ]),
                    ];
                })
                ->values();

            return [
                'data' => $categories,
                'message' => 'Here are all categories grouped by parent ID.',
                'statusCode' => ApiCode::SUCCESS,
            ];
        }

        // Standard paginated response for categories if not grouping
        $categories = Category::query()
            ->when($mainCategory, fn($query) => $query->where('parent_id', null))
            ->when($search, fn($query) => $query->where('name', 'LIKE', "%$search%"))
            ->latest()
            ->paginate($limit, ['*'], 'page', $page);

        return [
            'data' => new CategoryCollection($categories),
            'message' => 'Here are all categories.',
            'statusCode' => ApiCode::SUCCESS,
        ];
    }

    public function get($id)
    {
        $category = Category::with('subCategory')->findOrFail($id);

        return ['data' => CategoryResource::make($category), 'message' => 'Retrived Successfully.', 'statusCode' => ApiCode::SUCCESS];
    }

    public function store($request)
    {
        $category = Category::create([
            'name' => $request->name,
            'parent_id' => isset($request->parent_id) ? $request->parent_id : null,
            'department_id' => $request->department_id
        ]);

        return ['data' => CategoryResource::make($category), 'message' => 'Created Successfully.', 'statusCode' => ApiCode::CREATED];
    }

    public function update($request, $id)
    {
        $category = Category::findOrFail($id);

        if (isset($request->name) && $request->name != null)
            $category->name = $request->name;

        if (isset($request->parent_id) && $request->parent_id != null)
            $category->parent_id = $request->parent_id;

        $category->save();

        return ['data' => CategoryResource::make($category), 'message' => 'Updated Successfully.', 'statusCode' => ApiCode::SUCCESS];
    }

    public function delete($id)
    {
        Category::findOrFail($id)->delete();

        return ['data' => null, 'message' => 'Deleted Successfully.', 'statusCode' => ApiCode::SUCCESS];
    }

    public function getArchivesBasedOnCategory($request, $category_id)
    {
        $limit = $request->query('limit', 12);
        $page = $request->query('page', 1);
        $search = $request->query('search', null);

        // Fetch the category and ensure it exists
        $category = Category::findOrFail($category_id);

        // Query archives for this category only
        $archives = $category->archives()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    // Apply search filter strictly within the user's records
                    $subQuery->where('file_name', 'LIKE', "%$search%")
                        ->orWhere('file_path', 'LIKE', "%$search%");
                });
            })
            ->latest();

        // Paginate results
        $data = $archives->paginate($limit, ['*'], 'page', $page);

        return [
            'data' => new ArchivesCollection($data),
            'message' => 'Here are all archives for this category!',
            'statusCode' => ApiCode::SUCCESS
        ];
    }
}
