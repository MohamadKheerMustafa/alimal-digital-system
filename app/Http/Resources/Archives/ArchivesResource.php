<?php

namespace App\Http\Resources\Archives;

use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Profiles\ProfilesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArchivesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'profile_id' => $this->profile_id,
            'category_id' => $this->category_id,
            'file_name' => $this->file_name,
            'file_path' => asset(Storage::url('/' . $this->file_path)),
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'profile' => ProfilesResource::make($this->whenLoaded('profile', $this->profile)),
            'category' => CategoryResource::make($this->whenLoaded('category', $this->category))
        ];
    }
}
