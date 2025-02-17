<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketDocumentService
{
    public function uploadDocument(Ticket $ticket, UploadedFile $file): TicketDocument
    {
        // Générer un nom unique pour le fichier
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;
        
        // Définir le chemin dans le bucket
        $path = 'tickets/' . $ticket->id . '/documents/' . $fileName;
        
        // Stocker le fichier sur S3
        Storage::disk('s3')->put($path, file_get_contents($file));
        
        // Créer l'enregistrement du document
        $document = TicketDocument::create([
            'name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize()
        ]);
        
        // Attacher le document au ticket
        $ticket->documents()->attach($document->id);
        
        return $document;
    }

    public function deleteDocument(TicketDocument $document): bool
    {
        // La méthode delete du modèle s'occupera de supprimer le fichier de S3
        return $document->delete();
    }

    public function getDocumentUrl(TicketDocument $document): string
    {
        return $document->url;
    }
}
