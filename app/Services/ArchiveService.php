<?php

namespace App\Services;

use App\ApiCode;
use App\Http\Resources\Archives\ArchivesCollection;
use App\Http\Resources\Archives\ArchivesResource;
use App\Interfaces\ArchiveInterface;
use App\Jobs\Archives\FileUploadJob;
use App\Models\Archive\Archive;
use App\Models\Archive\Category;
use App\Models\HR\Department;
use Illuminate\Support\Facades\Storage;

class ArchiveService implements ArchiveInterface
{
    public function all($request)
    {
        $limit = $request->query('limit', 12);
        $page = $request->query('page', 1);
        $search = $request->query('search', null);

        $archives = Archive::with(['category', 'profile'])
            ->when($search, fn($query) => $query->where('file_name', 'LIKE', "%$search%"))
            ->latest()
            ->paginate($limit, ['*'], 'page', $page);

        return ['data' => new ArchivesCollection($archives), 'message' => 'Here are all data', 'statusCode' => ApiCode::SUCCESS];
    }

    public function get($id)
    {
        $archive = Archive::with(['category', 'profile'])->findOrFail($id);
        return ['data' => ArchivesResource::make($archive), 'message' => 'Here are all data', 'statusCode' => ApiCode::SUCCESS];
    }

    public function download($request)
    {
        $filePath = storage_path('app/' . $request->query('file_path'));

        abort_unless(file_exists($filePath), 404, 'File not found');

        return response()->download($filePath);
    }

    public function update($request, $id)
    {
        // Implement update logic here
    }

    public function delete($id)
    {
        $archive = Archive::findOrFail($id);

        if (Storage::exists($archive->file_path)) {
            Storage::delete($archive->file_path);
        }

        $archive->delete();

        return ['data' => null, 'message' => 'File deleted successfully', 'statusCode' => ApiCode::SUCCESS];
    }

    public function upload($request)
    {
        $user = auth()->user();
        $isAdmin = $request->input('isAdmin', false);
        $section = $request->input('section');
        $uploadToGeneral = $request->input('uploadToGeneral', false); // New attribute

        // If the user wants to upload to the "General" section
        if ($uploadToGeneral) {
            $generalCategory = Category::where('name', 'General')->first();
            if (!$generalCategory) {
                return [
                    'data' => null,
                    'message' => 'General category not found',
                    'statusCode' => ApiCode::NOT_FOUND
                ];
            }
            $this->uploadFiles($request, $user, $generalCategory);
            return [
                'data' => null,
                'message' => 'Files are being uploaded under the General category',
                'statusCode' => ApiCode::SUCCESS
            ];
        }

        // Admin upload logic
        if ($isAdmin && $user->isAbleTo('upload-archives')) {
            $category = Category::where('name', $section)->first();
            if (!$category) {
                return [
                    'data' => null,
                    'message' => 'Specified section not found',
                    'statusCode' => ApiCode::NOT_FOUND
                ];
            }
            $this->uploadFiles($request, $user, $category);
            return [
                'data' => null,
                'message' => 'Files are being uploaded to the specified section',
                'statusCode' => ApiCode::SUCCESS
            ];
        }

        // Department-based logic for regular users
        $department = Department::findOrFail($user->profile->department_id);
        $category = Category::where('name', $department->name)->first();

        if (!$category) {
            return [
                'data' => null,
                'message' => 'Category not found',
                'statusCode' => ApiCode::NOT_FOUND
            ];
        }

        $permissionMap = [
            'Graphic Design' => 'upload-archive-graphic-design',
            'Software Development' => 'upload-archive-software-development',
            'Marketing' => 'upload-archive-marketing',
            'E-commerce' => 'upload-archive-e-commerce',
        ];

        if (isset($permissionMap[$department->name])) {
            $permission = $permissionMap[$department->name];
            if ($user->isAbleTo($permission)) {
                $this->uploadFiles($request, $user, $category);
                return [
                    'data' => null,
                    'message' => 'Files are being uploaded',
                    'statusCode' => ApiCode::SUCCESS
                ];
            } else {
                return [
                    'data' => null,
                    'message' => 'You do not have permission to upload files in this section',
                    'statusCode' => ApiCode::FORBIDDEN
                ];
            }
        } else {
            $generalCategory = Category::where('name', 'General')->first();
            if (!$generalCategory) {
                return [
                    'data' => null,
                    'message' => 'General category not found',
                    'statusCode' => ApiCode::NOT_FOUND
                ];
            }
            $this->uploadFiles($request, $user, $generalCategory);
            return [
                'data' => null,
                'message' => 'Files are being uploaded under the General category',
                'statusCode' => ApiCode::SUCCESS
            ];
        }
    }

    private function uploadFiles($request, $user, $category)
    {
        $files = $request->file('files');
        if (!$files || !is_array($files)) {
            throw new \Exception('No files provided or invalid format');
        }

        foreach ($files as $file) {
            $tempPath = $file->store('temp');

            // Handle subcategory structure
            $uploadPath = $this->buildUploadPath($category);

            FileUploadJob::dispatch($tempPath, $user, $category, $request->except('files'), $uploadPath);
        }
    }

    private function buildUploadPath($category)
    {
        $path = "uploads/{$category->shortcut}";
        while ($category->parent) {
            $category = $category->parent;
            $path = "uploads/{$category->shortcut}/$path";
        }
        return $path;
    }
}
