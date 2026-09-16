<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'actif' => $this->actif,
            'role' => new RoleResource($this->whenLoaded('role')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'created_at' => $this->created_at,
        ];
    }
}
