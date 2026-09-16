<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AffectationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'statut' => $this->statut,
            'commentaire' => $this->commentaire,
            'date_affectation' => $this->date_affectation,
            'date_fin' => $this->date_fin,
            'agent' => new UserResource($this->whenLoaded('agent')),
            'affecte_par' => new UserResource($this->whenLoaded('affectePar')),
        ];
    }
}
