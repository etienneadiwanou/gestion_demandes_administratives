<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDemandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Une demande n'est modifiable par son auteur que tant qu'elle
        // est en brouillon ou en attente de complément. Cette règle
        // métier sera affinée dans la Policy dédiée.
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'priorite' => ['sometimes', Rule::in(['basse', 'normale', 'haute', 'urgente'])],
            'commentaire' => ['nullable', 'string'],
            'valeurs' => ['sometimes', 'array'],
            'valeurs.*.champ_demande_id' => ['required_with:valeurs', 'integer', 'exists:champ_demandes,id'],
            'valeurs.*.valeur' => ['nullable', 'string'],
        ];
    }
}
