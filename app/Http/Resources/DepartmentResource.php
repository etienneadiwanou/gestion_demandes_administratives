<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'code' => $this->code,
            'description' => $this->description,
            'actif' => $this->actif,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
