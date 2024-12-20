<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Profiles\ProfilesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'remember_token' => $this->remember_token,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'profile' => ProfilesResource::make($this->whenLoaded('profile')),
            'token' => $this->when(isset($this->token), $this->token),
            'roles' => $this->roles()->with('permissions')->get(),
        ];
    }
}
