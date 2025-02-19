<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240'
        ]);

        \DB::beginTransaction();
        try {
            // Créer le commentaire
            $comment = $ticket->comments()->create([
                'content' => $validated['content'],
                'user_id' => auth()->id(),
            ]);

            // Gérer les pièces jointes
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('comments', 's3');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'url' => \Storage::disk('s3')->url($path),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'path' => $path
                    ];
                }
                $comment->attachments = $attachments;
                $comment->save();
            }

            // Ajouter un log pour le nouveau commentaire
            $ticket->addLog('comment_added', 'Nouveau commentaire ajouté', [
                'comment_id' => $comment->id,
                'user_name' => auth()->user()->name
            ]);

            \DB::commit();

            return back()->with('success', 'Commentaire ajouté avec succès');
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Erreur lors de l\'ajout du commentaire:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout du commentaire');
        }
    }

    public function destroy(Ticket $ticket, Comment $comment)
    {
        if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Seul l\'auteur du commentaire ou un administrateur peut supprimer ce commentaire.');
        }

        \DB::beginTransaction();
        try {
            // Supprimer les fichiers associés
            if (!empty($comment->attachments)) {
                foreach ($comment->attachments as $attachment) {
                    if (isset($attachment['path'])) {
                        \Storage::disk('s3')->delete($attachment['path']);
                    }
                }
            }

            // Supprimer le commentaire
            $comment->delete();

            // Ajouter un log pour la suppression
            $ticket->addLog('comment_deleted', 'Commentaire supprimé', [
                'user_name' => auth()->user()->name
            ]);

            \DB::commit();
            return back()->with('success', 'Commentaire supprimé avec succès');
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Erreur lors de la suppression du commentaire:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Une erreur est survenue lors de la suppression du commentaire');
        }
    }
}
