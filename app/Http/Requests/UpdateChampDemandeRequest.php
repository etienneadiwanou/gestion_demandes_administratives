<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChampDemandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $champDemande = $this->route('champ_demande');
        $typeDemandeId = $this->input('type_demande_id', $champDemande?->type_demande_id);

        return [
            'type_demande_id' => ['sometimes', 'required', 'integer', 'exists:type_demandes,id'],
            'label' => ['sometimes', 'required', 'string', 'max:255'],
            'nom_technique' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('champ_demandes', 'nom_technique')
                    ->where(fn ($query) => $query->where('type_demande_id', $typeDemandeId))
                    ->ignore($champDemande),
            ],
            'type_champ' => ['sometimes', 'required', Rule::in(['texte', 'nombre', 'date', 'heure', 'liste', 'textarea', 'fichier', 'booleen'])],
            'obligatoire' => ['sometimes', 'boolean'],
            'options' => ['nullable', 'array'],
            'options.*' => ['string'],
            'ordre' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
