<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChampDemandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type_demande_id' => $this->type_demande_id,
            'label' => $this->label,
            'nom_technique' => $this->nom_technique,
            'type_champ' => $this->type_champ,
            'obligatoire' => $this->obligatoire,
            'options' => $this->options,
            'ordre' => $this->ordre,
        ];
    }
}
