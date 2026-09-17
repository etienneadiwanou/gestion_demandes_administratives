<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom_original' => $this->nom_original,
            'type_mime' => $this->type_mime,
            'taille' => $this->taille,
            'uploaded_by' => new UserResource($this->whenLoaded('user')),
            'demande' => $this->whenLoaded('demande', fn () => [
                'id' => $this->demande->id,
                'reference' => $this->demande->reference,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
