<?php

namespace App\Http\Controllers;

use App\Enums\RoleSlug;
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

    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $query = Document::with(['user', 'demande']);

        // Même périmètre que DemandeController@index : un employé ne
        // voit que les documents joints à ses propres demandes.
        if ($request->user()->hasRole(RoleSlug::Employe) || ! $request->user()->role) {
            $query->whereHas('demande', fn ($q) => $q->where('user_id', $request->user()->id));
        }

        return DocumentResource::collection($query->latest()->paginate(20));
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
