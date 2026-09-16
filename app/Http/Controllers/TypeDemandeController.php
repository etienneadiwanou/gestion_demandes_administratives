<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTypeDemandeRequest;
use App\Http\Requests\UpdateTypeDemandeRequest;
use App\Http\Resources\TypeDemandeResource;
use App\Models\TypeDemande;

class TypeDemandeController extends Controller
{
    public function index()
    {
        return TypeDemandeResource::collection(
            TypeDemande::with('champs')->orderBy('categorie')->orderBy('nom')->get()
        );
    }

    public function store(StoreTypeDemandeRequest $request)
    {
        $this->authorize('create', TypeDemande::class);

        $typeDemande = TypeDemande::create($request->validated());

        return TypeDemandeResource::make($typeDemande)->response()->setStatusCode(201);
    }

    public function show(TypeDemande $typeDemande)
    {
        return TypeDemandeResource::make($typeDemande->load('champs'));
    }

    public function update(UpdateTypeDemandeRequest $request, TypeDemande $typeDemande)
    {
        $this->authorize('update', $typeDemande);

        $typeDemande->update($request->validated());

        return TypeDemandeResource::make($typeDemande);
    }

    public function destroy(TypeDemande $typeDemande)
    {
        $this->authorize('delete', $typeDemande);

        // Désactivation : un type utilisé par des demandes existantes
        // ne doit pas disparaître de l'historique.
        $typeDemande->update(['actif' => false]);

        return response()->json(null, 204);
    }
}
