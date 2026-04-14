<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'status' => $this->status,
            'avatar' => $this->avatar_path ? asset('storage/' . $this->avatar_path) : null,
            'bio' => $this->bio,
            'xp' => $this->xp ?? 0,
            'level' => $this->level ?? 1,
            'email_verified' => !is_null($this->email_verified_at),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
