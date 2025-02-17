<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketDocument;
use App\Services\TicketDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Inertia\Response;

class TicketDocumentController extends Controller
{
    public function __construct(
        private TicketDocumentService $documentService
    ) {}

    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'document' => [
                'required',
                File::types(['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'])
                    ->max(10 * 1024), // 10MB max
            ],
        ]);

        $document = $this->documentService->uploadDocument(
            $ticket,
            $request->file('document')
        );

        return response()->json([
            'message' => 'Document ajouté avec succès',
            'document' => $document
        ]);
    }

    public function destroy(Ticket $ticket, TicketDocument $document)
    {
        // Vérifier que le document appartient bien au ticket
        if (!$ticket->documents()->where('ticket_document_id', $document->id)->exists()) {
            abort(404);
        }

        $this->documentService->deleteDocument($document);

        return back()->with('message', 'Document supprimé avec succès');
    }

    public function download(Ticket $ticket, TicketDocument $document)
    {
        // Vérifier que le document appartient bien au ticket
        if (!$ticket->documents()->where('ticket_document_id', $document->id)->exists()) {
            abort(404);
        }

        return Storage::disk('s3')->download(
            $document->path,
            $document->original_name,
            ['Content-Type' => $document->mime_type]
        );
    }
}
