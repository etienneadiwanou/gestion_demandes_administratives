<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTypeDemandeRequest;
use App\Http\Requests\UpdateTypeDemandeRequest;
use App\Models\TypeDemande;

class TypeDemandeController extends Controller
{
    public function index()
    {
        return TypeDemande::with('champs')->orderBy('categorie')->orderBy('nom')->get();
    }

    public function store(StoreTypeDemandeRequest $request)
    {
        $this->authorize('create', TypeDemande::class);

        $typeDemande = TypeDemande::create($request->validated());

        return response()->json($typeDemande, 201);
    }

    public function show(TypeDemande $typeDemande)
    {
        return $typeDemande->load('champs');
    }

    public function update(UpdateTypeDemandeRequest $request, TypeDemande $typeDemande)
    {
        $this->authorize('update', $typeDemande);

        $typeDemande->update($request->validated());

        return $typeDemande;
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
