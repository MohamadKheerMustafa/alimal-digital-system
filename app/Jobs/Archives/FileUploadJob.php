<?php

namespace App\Jobs\Archives;

use App\Models\Archive\Archive;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;

class FileUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tempPath;
    protected $user;
    protected $category;
    protected $requestData;
    protected $uploadPath;

    public function __construct($tempPath, $user, $category, $requestData, $uploadPath)
    {
        $this->tempPath = $tempPath;
        $this->user = $user;
        $this->category = $category;
        $this->requestData = $requestData;
        $this->uploadPath = $uploadPath;
    }

    public function handle()
    {
        try {
            $fileContents = Storage::get($this->tempPath);
            $originalFileName = basename($this->tempPath);
            $fileName = uniqid() . '_' . $originalFileName;

            Storage::put("{$this->uploadPath}/$fileName", $fileContents);
            Storage::delete($this->tempPath);

            Archive::create(array_merge([
                'profile_id' => $this->user->profile->id,
                'category_id' => $this->category->id,
                'file_name' => $fileName,
                'file_path' => "{$this->uploadPath}/$fileName",
            ], $this->requestData));
        } catch (Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
        }
    }
}
