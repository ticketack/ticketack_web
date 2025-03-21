<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,txt|max:10240'
        ]);

        \DB::beginTransaction();
        try {
            // Purifier le HTML pour éviter les attaques XSS
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('HTML.Allowed', 'p,b,i,strong,em,u,a[href|title],ul,ol,li,br,code,pre');
            $purifier = new \HTMLPurifier($config);
            $cleanHtml = $purifier->purify($validated['content']);

            // Créer le commentaire avec le HTML purifié
            $comment = $ticket->comments()->create([
                'content' => $cleanHtml,
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
            
            // Envoyer des notifications aux personnes concernées
            $this->sendCommentNotifications($ticket, $comment);

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
    
    /**
     * Envoie des notifications aux personnes concernées lors de l'ajout d'un commentaire
     *
     * @param Ticket $ticket Le ticket commenté
     * @param Comment $comment Le commentaire ajouté
     * @return void
     */
    private function sendCommentNotifications(Ticket $ticket, Comment $comment): void
    {
        $currentUser = Auth::user();
        $notificationService = app(NotificationService::class);
        $content = "Un nouveau commentaire a été ajouté au ticket #{$ticket->id} '{$ticket->title}' par {$currentUser->name}";
        
        // Récupérer les destinataires (auteur et assignés)
        $recipients = collect();
        
        // Ajouter l'auteur s'il n'est pas la personne qui commente
        if ($ticket->creator && $ticket->creator->id !== $currentUser->id) {
            $recipients->push($ticket->creator);
        }
        
        // Ajouter les assignés qui ne sont pas la personne qui commente
        foreach ($ticket->assignees as $assignee) {
            if ($assignee->id !== $currentUser->id) {
                $recipients->push($assignee);
            }
        }
        
        // Envoyer la notification à chaque destinataire
        foreach ($recipients->unique('id') as $recipient) {
            $notificationService->sendNotification(
                $recipient,
                'ticket_commented',
                $content,
                [
                    'ticket_id' => $ticket->id,
                    'comment_id' => $comment->id,
                    'commented_by' => $currentUser->id,
                    'commented_by_name' => $currentUser->name
                ]
            );
        }
    }
}
