<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAffectationRequest;
use App\Models\Demande;
use App\Models\User;
use App\Services\AffectationService;

class AffectationController extends Controller
{
    public function __construct(
        private readonly AffectationService $affectationService,
    ) {
    }

    public function store(StoreAffectationRequest $request, Demande $demande)
    {
        $this->authorize('assign', $demande);

        $agent = User::findOrFail($request->validated('agent_id'));

        $affectation = $this->affectationService->affecter(
            $demande,
            $agent,
            $request->user(),
            $request->validated('commentaire'),
        );

        return response()->json($affectation->load('agent', 'affectePar'), 201);
    }
}
