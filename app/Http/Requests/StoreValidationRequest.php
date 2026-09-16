<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreValidationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in(['approuve', 'rejete', 'complement_demande'])],
            'commentaire' => ['required_if:decision,rejete,complement_demande', 'nullable', 'string'],
        ];
    }
}
