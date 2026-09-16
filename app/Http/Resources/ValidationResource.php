<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ValidationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'decision' => $this->decision,
            'commentaire' => $this->commentaire,
            'validateur' => new UserResource($this->whenLoaded('validateur')),
            'created_at' => $this->created_at,
        ];
    }
}
