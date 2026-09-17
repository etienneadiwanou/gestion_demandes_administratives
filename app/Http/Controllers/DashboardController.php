<?php

namespace App\Http\Controllers;

use App\Enums\RoleSlug;
use App\Enums\StatutDemande;
use App\Models\Demande;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $query = Demande::query();

        // Même périmètre de visibilité que DemandeController@index :
        // un employé ne voit que ses propres demandes.
        if ($request->user()->hasRole(RoleSlug::Employe) || ! $request->user()->role) {
            $query->where('user_id', $request->user()->id);
        }

        $total = (clone $query)->count();

        $enCours = (clone $query)->whereIn('statut', [
            StatutDemande::Soumise,
            StatutDemande::EnVerification,
            StatutDemande::ComplementDemande,
            StatutDemande::EnValidation,
        ])->count();

        $validees = (clone $query)->where('statut', StatutDemande::Approuvee)->count();

        return response()->json([
            'data' => [
                'total' => $total,
                'en_cours' => $enCours,
                'validees' => $validees,
            ],
        ]);
    }
}
