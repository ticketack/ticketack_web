<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentDocument;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EquipmentDocumentController extends Controller
{
    /**
     * Affiche la liste des documents pour un équipement spécifique.
     *
     * @param Equipment $equipment
     * @return Response|\Illuminate\Http\JsonResponse
     */
    public function index(Equipment $equipment)
    {
        try {
            $this->authorize('viewAny', EquipmentDocument::class);
            
            $documents = $equipment->documents()->orderBy('created_at', 'desc')->get();
            
            // Vérifier les permissions pour les documents
            $can = [
                'create' => auth()->user()->can('create', EquipmentDocument::class),
                'edit' => auth()->user()->can('update', EquipmentDocument::class),
                'delete' => auth()->user()->can('delete', EquipmentDocument::class)
            ];
            
            // Si la requête est AJAX, renvoyer les données au format JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'documents' => $documents,
                    'can' => $can
                ]);
            }
        } catch (\Exception $e) {
            // Log l'erreur pour débogage
            \Log::error('Erreur lors de la récupération des documents: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'Une erreur est survenue lors de la récupération des documents.'], 500);
            }
        }
        
        // Sinon, rendre la vue Inertia
        return Inertia::render('Equipment/Documents/Index', [
            'equipment' => $equipment,
            'documents' => $documents,
            'can' => $can,
            'translations' => [
                'pages' => trans('pages'),
                'equipment' => trans('equipment'),
                'menu' => trans('menu'),
                'auth' => trans('auth'),
                'permissions' => trans('permissions')
            ]
        ]);
    }

    /**
     * Télécharge un document.
     *
     * @param EquipmentDocument $document
     * @return BinaryFileResponse
     */
    public function download(EquipmentDocument $document)
    {
        try {
            $this->authorize('view', $document);
            
            // Extraire le chemin relatif du fichier à partir de l'URL complète
            $s3Url = Storage::disk('s3')->url('');
            $relativePath = str_replace($s3Url, '', $document->file_path);
            
            // Générer une URL signée temporaire pour le téléchargement
            $temporaryUrl = Storage::disk('s3')->temporaryUrl(
                $relativePath,
                now()->addMinutes(5),
                [
                    'ResponseContentDisposition' => 'attachment; filename="' . $document->original_filename . '"',
                ]
            );
            
            // Rediriger vers l'URL signée
            return redirect()->away($temporaryUrl);
        } catch (\Exception $e) {
            \Log::error('Erreur lors du téléchargement du document: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Impossible de télécharger le document. ' . $e->getMessage());
        }
    }

    /**
     * Enregistre un nouveau document pour un équipement.
     *
     * @param Request $request
     * @param Equipment $equipment
     * @return RedirectResponse
     */
    public function store(Request $request, Equipment $equipment): RedirectResponse
    {
        $this->authorize('create', EquipmentDocument::class);
        
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'description' => 'nullable|string|max:500',
        ]);
        
        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $fileType = $file->getMimeType();
        $fileSize = $file->getSize();
        
        // Générer un nom de fichier unique
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Stocker le fichier sur S3
        $path = $file->storeAs('equipment/documents', $filename, 's3');
        $fileUrl = Storage::disk('s3')->url($path);
        
        // Créer l'enregistrement dans la base de données
        $document = $equipment->documents()->create([
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'file_path' => $fileUrl,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'description' => $validated['description'] ?? null,
        ]);
        
        return redirect()->back()->with('message', 'Document ajouté avec succès.');
    }

    /**
     * Affiche un document spécifique.
     *
     * @param EquipmentDocument $document
     * @return Response
     */
    public function show(EquipmentDocument $document): Response
    {
        $this->authorize('view', $document);
        
        return Inertia::render('Equipment/Documents/Show', [
            'document' => $document,
            'equipment' => $document->equipment,
            'translations' => [
                'pages' => trans('pages'),
                'equipment' => trans('equipment'),
                'documents' => trans('documents'),
                'menu' => trans('menu'),
                'auth' => trans('auth'),
                'permissions' => trans('permissions')
            ]
        ]);
    }

    /**
     * Met à jour les informations d'un document.
     *
     * @param Request $request
     * @param EquipmentDocument $document
     * @return RedirectResponse
     */
    public function update(Request $request, EquipmentDocument $document): RedirectResponse
    {
        $this->authorize('update', $document);
        
        $validated = $request->validate([
            'description' => 'nullable|string|max:500',
        ]);
        
        $document->update([
            'description' => $validated['description'] ?? null,
        ]);
        
        return redirect()->back()->with('message', 'Document mis à jour avec succès.');
    }

    /**
     * Supprime un document.
     *
     * @param EquipmentDocument $document
     * @return RedirectResponse
     */
    public function destroy(EquipmentDocument $document): RedirectResponse
    {
        $this->authorize('delete', $document);
        
        // Supprimer le fichier du stockage
        $path = str_replace(Storage::disk('s3')->url(''), '', $document->file_path);
        Storage::disk('s3')->delete($path);
        
        // Supprimer l'enregistrement de la base de données
        $document->delete();
        
        return redirect()->back()->with('message', 'Document supprimé avec succès.');
    }
}
