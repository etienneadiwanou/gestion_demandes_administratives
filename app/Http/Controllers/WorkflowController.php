<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function __construct(
        private readonly WorkflowService $workflowService,
    ) {
    }

    public function transmettreEnValidation(Request $request, Demande $demande)
    {
        $this->authorize('transmitForValidation', $demande);

        return $this->workflowService->transmettreEnValidation($demande, $request->user());
    }

    public function demanderComplement(Request $request, Demande $demande)
    {
        $this->authorize('requestComplement', $demande);

        $data = $request->validate([
            'commentaire' => ['required', 'string'],
        ]);

        return $this->workflowService->demanderComplement($demande, $request->user(), $data['commentaire']);
    }

    public function archiver(Request $request, Demande $demande)
    {
        $this->authorize('archive', $demande);

        return $this->workflowService->archiver($demande, $request->user());
    }
}
