<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDemandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_demande_id' => ['required', 'integer', 'exists:type_demandes,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'priorite' => ['sometimes', Rule::in(['basse', 'normale', 'haute', 'urgente'])],
            'commentaire' => ['nullable', 'string'],
            'valeurs' => ['sometimes', 'array'],
            'valeurs.*.champ_demande_id' => ['required_with:valeurs', 'integer', 'exists:champ_demandes,id'],
            'valeurs.*.valeur' => ['nullable', 'string'],
        ];
    }
}
