<?php

namespace App\Http\Controllers;

use App\Enums\RoleSlug;
use App\Http\Requests\StoreDemandeRequest;
use App\Http\Requests\UpdateDemandeRequest;
use App\Http\Resources\DemandeResource;
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

        // Le filtrage avancé (département, période, priorité...) sera
        // ajouté à l'étape "Recherche et filtres" (Phase 2). Le filtre
        // par statut existe déjà : nécessaire pour l'écran Affectations.
        $query = Demande::with(['typeDemande', 'department', 'user', 'affectations.agent']);

        // Un employé ne voit que ses propres demandes ; agents,
        // validateurs et administrateurs voient l'ensemble (le
        // périmètre par département pourra être affiné plus tard).
        if ($request->user()->hasRole(RoleSlug::Employe) || ! $request->user()->role) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut'));
        }

        return DemandeResource::collection($query->latest()->paginate(20));
    }

    public function store(StoreDemandeRequest $request)
    {
        $this->authorize('create', Demande::class);

        $demande = $this->demandeService->creer($request->user(), $request->validated());

        return DemandeResource::make($demande->load(['user', 'typeDemande', 'department']))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Demande $demande)
    {
        $this->authorize('view', $demande);

        return DemandeResource::make($demande->load([
            'user', 'typeDemande.champs', 'department', 'valeurs.champDemande',
            'documents.user', 'affectations.agent', 'affectations.affectePar',
            'validations.validateur',
        ]));
    }

    public function update(UpdateDemandeRequest $request, Demande $demande)
    {
        $this->authorize('update', $demande);

        $demande = $this->demandeService->mettreAJour($demande, $request->validated());

        return DemandeResource::make($demande->load(['user', 'typeDemande', 'department']));
    }

    public function submit(Demande $demande)
    {
        $this->authorize('submit', $demande);

        $demande = $this->demandeService->soumettre($demande);

        return DemandeResource::make($demande->load(['user', 'typeDemande', 'department']));
    }
}
