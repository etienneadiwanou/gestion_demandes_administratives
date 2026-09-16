<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ValeurDemandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'champ_demande_id' => $this->champ_demande_id,
            'label' => $this->whenLoaded('champDemande', fn () => $this->champDemande->label),
            'valeur' => $this->valeur,
        ];
    }
}
