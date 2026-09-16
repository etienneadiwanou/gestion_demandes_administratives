<?php

namespace App\Services;

use App\Models\Demande;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function __construct(
        private readonly AuditLogService $auditLog,
    ) {
    }

    /**
     * Stocke un fichier sur un disque privé et l'associe à la demande.
     * Le disque "local" (storage/app) n'est pas exposé publiquement :
     * l'accès aux documents doit toujours passer par une route
     * contrôlée (permissions vérifiées côté Controller/Policy).
     */
    public function ajouter(Demande $demande, User $auteur, UploadedFile $fichier): Document
    {
        $chemin = $fichier->store("demandes/{$demande->id}", 'local');

        $document = $demande->documents()->create([
            'user_id' => $auteur->id,
            'nom_original' => $fichier->getClientOriginalName(),
            'chemin_fichier' => $chemin,
            'type_mime' => $fichier->getClientMimeType(),
            'taille' => $fichier->getSize(),
        ]);

        $this->auditLog->log($auteur->id, 'ajout_document', $demande, null, ['document_id' => $document->id]);

        return $document;
    }

    public function supprimer(Document $document, User $auteur): void
    {
        Storage::disk('local')->delete($document->chemin_fichier);

        $this->auditLog->log($auteur->id, 'suppression_document', $document->demande, $document->toArray(), null);

        $document->delete();
    }
}
