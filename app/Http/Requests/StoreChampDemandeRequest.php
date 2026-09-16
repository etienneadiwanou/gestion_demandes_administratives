<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChampDemandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_demande_id' => ['required', 'integer', 'exists:type_demandes,id'],
            'label' => ['required', 'string', 'max:255'],
            'nom_technique' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('champ_demandes', 'nom_technique')
                    ->where(fn ($query) => $query->where('type_demande_id', $this->input('type_demande_id'))),
            ],
            'type_champ' => ['required', Rule::in(['texte', 'nombre', 'date', 'heure', 'liste', 'textarea', 'fichier', 'booleen'])],
            'obligatoire' => ['sometimes', 'boolean'],
            'options' => ['required_if:type_champ,liste', 'nullable', 'array'],
            'options.*' => ['string'],
            'ordre' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
