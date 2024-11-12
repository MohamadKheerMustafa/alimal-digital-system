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
        $uploadToGeneral = $request->input('uploadToGeneral', false);

        // Upload to "General" section
        if ($uploadToGeneral) {
            $generalCategory = Category::where('name', 'General')->first();
            if (!$generalCategory) {
                return $this->errorResponse('General category not found');
            }
            return $this->uploadFiles($request, $user, $generalCategory, 'Files are being uploaded under the General category');
        }

        // Admin upload logic
        if ($isAdmin && $user->isAbleTo('full_system_access')) {
            $category = Category::where('name', $section)->first();
            if (!$category) {
                return $this->errorResponse('Specified section not found');
            }
            return $this->uploadFiles($request, $user, $category, 'Files are being uploaded to ' . $section);
        }

        // Employee upload logic for own archive only
        $category = Category::findOrFail($user->profile->category_id);
        if (!$category || !$user->isAbleTo('manage_own_archive')) {
            return $this->errorResponse('You do not have permission to upload files in this section');
        }

        return $this->uploadFiles($request, $user, $category, 'Files are being uploaded');
    }

    // Helper function to handle file upload response
    private function uploadFiles($request, $user, $category, $successMessage)
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

        return [
            'data' => null,
            'message' => $successMessage,
            'statusCode' => ApiCode::SUCCESS
        ];
    }

    // Helper function for error responses
    private function errorResponse($message)
    {
        return [
            'data' => null,
            'message' => $message,
            'statusCode' => ApiCode::FORBIDDEN
        ];
    }

    // Build the upload path based on category structure
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
