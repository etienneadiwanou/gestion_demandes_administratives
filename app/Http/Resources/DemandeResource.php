<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'statut' => $this->statut,
            'priorite' => $this->priorite,
            'commentaire' => $this->commentaire,
            'date_soumission' => $this->date_soumission,
            'date_cloture' => $this->date_cloture,
            'demandeur' => new UserResource($this->whenLoaded('user')),
            'type_demande' => new TypeDemandeResource($this->whenLoaded('typeDemande')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'valeurs' => ValeurDemandeResource::collection($this->whenLoaded('valeurs')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'affectations' => AffectationResource::collection($this->whenLoaded('affectations')),
            'validations' => ValidationResource::collection($this->whenLoaded('validations')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
