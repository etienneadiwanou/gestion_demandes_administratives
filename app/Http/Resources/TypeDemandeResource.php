<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TypeDemandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'code' => $this->code,
            'categorie' => $this->categorie,
            'description' => $this->description,
            'actif' => $this->actif,
            'champs' => ChampDemandeResource::collection($this->whenLoaded('champs')),
        ];
    }
}
