<?php

namespace App\Http\Resources\Profiles;

use App\Models\Archive\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ProfilesResource extends JsonResource
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
            'name' => $this->user->name,
            'user_id' => $this->user_id,
            'department_id' => $this->department_id,
            'department_name' => $this->department->name ?? null,
            'position' => $this->position,
            'contact_number' => $this->contact_number,
            'address' => $this->address,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
