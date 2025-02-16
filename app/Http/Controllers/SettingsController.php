<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(): Response
    {
        $settings = Setting::pluck('value', 'key')->all();
        $logo = $settings['logo'] ?? null;
        
        return Inertia::render('Settings/Index', [
            'settings' => [
                'company_name' => $settings['company_name'] ?? '',
                'logo' => $logo,
                'language' => $settings['language'] ?? 'fr'
            ]
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Request data:', [
            'all' => $request->all(),
            'input' => $request->input(),
            'has_language' => $request->has('language'),
            'language' => $request->input('language'),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'path' => $request->path(),
            'url' => $request->url()
        ]);

        $rules = [
            'language' => 'required|string|in:fr,en,de'
        ];

        // Ajouter les autres règles seulement si elles sont présentes dans la requête
        if ($request->has('company_name')) {
            $rules['company_name'] = 'required|string|max:255';
        }
        if ($request->hasFile('logo')) {
            $rules['logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }

        $validated = $request->validate($rules);

        \Log::info('Validated data:', $validated);

        try {
            \DB::beginTransaction();

            // Mettre à jour le nom de l'entreprise s'il est présent
            if (isset($validated['company_name'])) {
                Setting::updateOrCreate(
                    ['key' => 'company_name'],
                    ['value' => $validated['company_name']]
                );
            }

            // Mettre à jour la langue
            $languageSetting = Setting::updateOrCreate(
                ['key' => 'language'],
                ['value' => $validated['language']]
            );

            \Log::info('Language setting after update:', ['setting' => $languageSetting->toArray()]);

            // Gérer le logo s'il est présent
            if ($request->hasFile('logo')) {
                \Log::info('Logo upload: Starting logo processing');
                
                $file = $request->file('logo');
                // Utiliser le disque s3 au lieu de public
                $path = $file->store('logos', 's3');
                \Log::info('Logo upload: File details', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'stored_path' => $path
                ]);
                
                // Supprimer l'ancien logo s'il existe
                $oldLogo = Setting::where('key', 'logo')->first();
                \Log::info('Logo upload: Old logo found', ['oldLogo' => $oldLogo]);
                
                if ($oldLogo && Storage::disk('s3')->exists($oldLogo->value)) {
                    Storage::disk('s3')->delete($oldLogo->value);
                    \Log::info('Logo upload: Old logo deleted');
                }
                
                // Générer l'URL complète du S3
                $logoUrl = Storage::disk('s3')->url($path);
                \Log::info('Logo upload: Generated S3 URL', ['url' => $logoUrl]);
                
                // Sauvegarder le nouveau logo dans la base de données avec l'URL complète
                $logoSetting = Setting::updateOrCreate(
                    ['key' => 'logo'],
                    ['value' => $logoUrl]
                );
                \Log::info('Logo upload: New logo saved in database', [
                    'logoSetting' => $logoSetting->toArray(),
                    'url' => $logoUrl
                ]);
                // Mettre à jour le logo dans la session
                session()->put('logo', $logoUrl);
                \Log::info('Logo upload: Session updated', ['url' => $logoUrl]);
                \Log::info('Logo upload: Session updated', ['sessionLogo' => session()->get('logo')]);
            }

            \DB::commit();

            // Mettre à jour la langue dans la session et l'application
            session()->put('locale', $validated['language']);
            app()->setLocale($validated['language']);
            \Carbon\Carbon::setLocale($validated['language']);
            setlocale(LC_TIME, $validated['language'].'_'.strtoupper($validated['language']).'.UTF-8');

            \Log::info('Session and app locale:', [
                'session_locale' => session()->get('locale'),
                'app_locale' => app()->getLocale(),
                'db_language' => Setting::where('key', 'language')->first()?->value
            ]);

            // Récupérer les paramètres mis à jour
            $settings = Setting::pluck('value', 'key')->all();
            $logo = $settings['logo'] ?? null;

            return redirect()->route('settings.index')->with([
                'success' => 'Paramètres mis à jour avec succès',
                'settings' => [
                    'company_name' => $settings['company_name'] ?? '',
                    'logo' => $logo,
                    'language' => $settings['language'] ?? 'fr'
                ]
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Error saving settings:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la sauvegarde des paramètres');
        }
    }

    public function deleteLogo()
    {
        try {
            \Log::info('Logo deletion: Starting process');
            
            $logo = Setting::where('key', 'logo')->first();
            \Log::info('Logo deletion: Found logo setting', ['logo' => $logo]);
            
            if ($logo) {
                // Extraire le chemin du fichier de l'URL
                $logoPath = parse_url($logo->value, PHP_URL_PATH);
                $logoPath = ltrim($logoPath, '/'); // Enlever le slash initial
                
                $exists = Storage::disk('s3')->exists($logoPath);
                \Log::info('Logo deletion: File exists check', ['path' => $logoPath, 'exists' => $exists]);
                
                if ($exists) {
                    Storage::disk('s3')->delete($logoPath);
                    \Log::info('Logo deletion: File deleted');
                }
                
                $logo->delete();
                \Log::info('Logo deletion: Database record deleted');
            }

            // Récupérer les paramètres mis à jour
            $settings = Setting::pluck('value', 'key')->all();
            \Log::info('Logo deletion: Current settings', ['settings' => $settings]);
            
            return redirect()->route('settings.index')->with([
                'success' => 'Logo supprimé avec succès',
                'settings' => [
                    'company_name' => $settings['company_name'] ?? '',
                    'logo' => null,
                    'language' => $settings['language'] ?? 'fr'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Logo deletion: Error occurred', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression du logo');
        }
    }
}
