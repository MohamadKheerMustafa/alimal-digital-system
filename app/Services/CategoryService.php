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
        $limit = $request->query('limit', 12);
        $page = $request->query('page', 1);
        $search = $request->query('search', null);

        $categories = Category::query()->when($search, fn($query) => $query->where('name', 'LIKE', "%$search%"))
            ->latest()
            ->paginate($limit, ['*'], 'page', $page);

        return ['data' => new CategoryCollection($categories), 'message' => 'Here are all categories.', 'statusCode' => ApiCode::SUCCESS];
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
            'parent_id' => isset($request->parent_id) ? $request->parent_id : null
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

        $category = Category::findOrFail($category_id);

        $archives = $category->archives()->when(
            $search,
            fn($query) => $query->where('file_name', 'Like', "% $search %")
                ->orWhere('file_path', 'Like', "%$search%")
        )->latest();

        $data = $archives->paginate($limit, ['*'], 'page', $page);

        return ['data' => new ArchivesCollection($data), 'message' => 'Here are all archives for this category!.', 'statusCode' => ApiCode::SUCCESS];
    }
}
