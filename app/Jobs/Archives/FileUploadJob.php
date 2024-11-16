<?php

namespace App\Jobs\Archives;

use App\Models\Archive\Archive;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tempPath;
    protected $user;
    protected $category;
    protected $uploadData;
    protected $uploadPath;
    protected $originalFileName;

    public function __construct($tempPath, $user, $category, $uploadData, $uploadPath, $originalFileName)
    {
        $this->tempPath = $tempPath;
        $this->user = $user;
        $this->category = $category;
        $this->uploadData = $uploadData;
        $this->uploadPath = $uploadPath;
        $this->originalFileName = $originalFileName;
    }

    public function handle()
    {
        try {
            $fileContents = Storage::get($this->tempPath);

            // Generate a unique file name
            $uniqueFileName = uniqid() . '_' . $this->originalFileName;

            // Save the file in the designated location
            Storage::disk('public')->put("{$this->uploadPath}/$uniqueFileName", $fileContents);

            // Remove the temporary file
            Storage::delete($this->tempPath);

            // Create an archive record
            Archive::create([
                'profile_id' => $this->user->profile->id,
                'category_id' => $this->category->id,
                'file_name' => $uniqueFileName,
                'file_path' => "{$this->uploadPath}/$uniqueFileName",
                'file_size' => $this->uploadData['file_size'],
                'file_type' => $this->uploadData['file_extension'],
            ]);
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
        }
    }
}
