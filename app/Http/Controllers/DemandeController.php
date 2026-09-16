<?php

namespace App\Http\Controllers;

use App\Enums\RoleSlug;
use App\Http\Requests\StoreDemandeRequest;
use App\Http\Requests\UpdateDemandeRequest;
use App\Models\Demande;
use App\Services\DemandeService;
use Illuminate\Http\Request;

class DemandeController extends Controller
{
    public function __construct(
        private readonly DemandeService $demandeService,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Demande::class);

        // Le filtrage avancé (statut, département, période, priorité...)
        // sera ajouté à l'étape "Recherche et filtres" (Phase 2).
        $query = Demande::with(['typeDemande', 'department', 'user']);

        // Un employé ne voit que ses propres demandes ; agents,
        // validateurs et administrateurs voient l'ensemble (le
        // périmètre par département pourra être affiné plus tard).
        if ($request->user()->hasRole(RoleSlug::Employe) || ! $request->user()->role) {
            $query->where('user_id', $request->user()->id);
        }

        return $query->latest()->paginate(20);
    }

    public function store(StoreDemandeRequest $request)
    {
        $this->authorize('create', Demande::class);

        $demande = $this->demandeService->creer($request->user(), $request->validated());

        return response()->json($demande, 201);
    }

    public function show(Demande $demande)
    {
        $this->authorize('view', $demande);

        return $demande->load(['typeDemande.champs', 'valeurs', 'documents', 'affectations', 'validations']);
    }

    public function update(UpdateDemandeRequest $request, Demande $demande)
    {
        $this->authorize('update', $demande);

        return $this->demandeService->mettreAJour($demande, $request->validated());
    }

    public function submit(Demande $demande)
    {
        $this->authorize('submit', $demande);

        return $this->demandeService->soumettre($demande);
    }
}
