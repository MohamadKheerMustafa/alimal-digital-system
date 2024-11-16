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
use Illuminate\Support\Facades\Log;
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

        // if (Storage::exists($archive->file_path)) {
        //     Storage::delete($archive->file_path);
        // }

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

    public function getAllRequests($request)
    {
        $user = auth()->user();

        // Check if the user is a manager
        if (!$user->profile->is_manager) {
            return [
                'data' => null,
                'message' => 'You do not have permission to access data in this section.',
                'statusCode' => ApiCode::FORBIDDEN
            ];
        }

        // Extract query parameters with defaults
        $limit = $request->query('limit', 12);
        $page = $request->query('page', 1);
        $search = $request->query('search', null);

        // Get category IDs for the user's department
        $categoryIds = Category::where('department_id', $user->profile->department_id)
            ->pluck('id')
            ->toArray();

        // Query approvmentRequests through the Archive model
        $approvalRequestsQuery = Archive::query()
            ->whereIn('category_id', $categoryIds)
            ->whereHas('approvmentRequests') // Only include archives with approvmentRequests
            ->with(['approvmentRequests' => function ($query) {
                $query->select(['*']); // Add more fields as needed
            }]);

        // Apply search filter to the file_name column
        if ($search) {
            $approvalRequestsQuery->where('file_name', 'LIKE', "%{$search}%");
        }

        // Paginate results
        $paginatedResults = $approvalRequestsQuery->paginate($limit, ['*'], 'page', $page);

        return [
            'data' => $paginatedResults,
            'message' => 'Here are all requests for the logged-in user.',
            'statusCode' => ApiCode::SUCCESS
        ];
    }

    public function handleStatusChanges($request) {}

    public function askToUpdate($request)
    {
        $user = auth()->user();
        $archive = Archive::findOrFail($request->archive_id);

        $archive->approvmentRequests()->attach($user->profile->id, [
            'request_type' => 'update',
            'status' => 'pending'
        ]);

        return ['data' => null, 'message' => 'Your request for updating archive has been added successfully, wait for your manager to approve', 'statusCode' => ApiCode::SUCCESS];
    }

    public function askToDelete($request)
    {
        $user = auth()->user();
        $archive = Archive::findOrFail($request->archive_id);

        $archive->approvmentRequests()->attach($user->profile->id, [
            'request_type' => 'delete',
            'status' => 'pending'
        ]);

        return ['data' => null, 'message' => 'Your request for deleting archive has been added successfully, wait for your manager to approve', 'statusCode' => ApiCode::SUCCESS];
    }

    // Helper function to handle file upload response
    private function uploadFiles($request, $user, $category, $successMessage)
    {
        $files = $request->file('files');

        // Ensure files are provided
        if (!$files || !is_array($files)) {
            return $this->errorResponse('No files provided.');
        }

        foreach ($files as $file) {
            // Extract file details
            $fileDetails = $this->getFileDetails($file);

            // Store the file temporarily
            $tempPath = $file->store('temp');

            // Build the upload path
            $uploadPath = $this->buildUploadPath($category);

            // Dispatch the job with the necessary file details
            FileUploadJob::dispatch(
                $tempPath,
                $user,
                $category,
                [
                    'file_size' => $fileDetails['size'], // File size
                    'file_extension' => $fileDetails['extension'], // File extension
                ],
                $uploadPath,
                $fileDetails['original_name']
            );
        }

        return [
            'data' => null,
            'message' => $successMessage,
            'statusCode' => ApiCode::SUCCESS,
        ];
    }

    // Helper function to extract file details
    private function getFileDetails($file)
    {
        return [
            'original_name' => $file->getClientOriginalName(), // Original file name
            'size' => $file->getSize(), // File size in bytes
            'extension' => $file->getClientOriginalExtension(), // File extension
        ];
    }

    // Helper function for error responses
    private function errorResponse($message)
    {
        return [
            'data' => null,
            'message' => $message,
            'statusCode' => ApiCode::FORBIDDEN,
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
