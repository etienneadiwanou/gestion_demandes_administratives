<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Demande;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {
    }

    public function store(StoreDocumentRequest $request, Demande $demande)
    {
        $this->authorize('uploadDocument', $demande);

        $document = $this->documentService->ajouter($demande, $request->user(), $request->file('fichier'));

        return DocumentResource::make($document->load('user'))->response()->setStatusCode(201);
    }

    public function download(Document $document)
    {
        $this->authorize('view', $document);

        return Storage::disk('local')->download($document->chemin_fichier, $document->nom_original);
    }

    public function destroy(Request $request, Document $document)
    {
        $this->authorize('delete', $document);

        $this->documentService->supprimer($document, $request->user());

        return response()->json(null, 204);
    }
}
