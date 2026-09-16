<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChampDemandeRequest;
use App\Http\Requests\UpdateChampDemandeRequest;
use App\Models\ChampDemande;

class ChampDemandeController extends Controller
{
    // La liste des champs d'un type de demande se consulte via
    // TypeDemandeController@show (relation "champs" déjà chargée).

    public function store(StoreChampDemandeRequest $request)
    {
        $this->authorize('create', ChampDemande::class);

        $champDemande = ChampDemande::create($request->validated());

        return response()->json($champDemande, 201);
    }

    public function update(UpdateChampDemandeRequest $request, ChampDemande $champDemande)
    {
        $this->authorize('update', $champDemande);

        $champDemande->update($request->validated());

        return $champDemande;
    }

    public function destroy(ChampDemande $champDemande)
    {
        $this->authorize('delete', $champDemande);

        $champDemande->delete();

        return response()->json(null, 204);
    }
}
