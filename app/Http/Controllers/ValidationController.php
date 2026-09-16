<?php

namespace App\Http\Controllers;

use App\Enums\DecisionValidation;
use App\Http\Requests\StoreValidationRequest;
use App\Models\Demande;
use App\Services\ValidationService;

class ValidationController extends Controller
{
    public function __construct(
        private readonly ValidationService $validationService,
    ) {
    }

    public function store(StoreValidationRequest $request, Demande $demande)
    {
        $this->authorize('decide', $demande);

        $validation = $this->validationService->enregistrerDecision(
            $demande,
            $request->user(),
            DecisionValidation::from($request->validated('decision')),
            $request->validated('commentaire'),
        );

        return response()->json($validation, 201);
    }
}
