<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    public function index(): Response
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        
        $data = [
            'settings' => [
                'company_name' => $settings['company_name'] ?? '',
                'logo' => $settings['logo'] ?? ''
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ];
        
        \Log::info('Data complète envoyée au composant:', [
            'settings' => $data['settings'],
            'flash' => $data['flash'],
            'session_data' => session()->all()
        ]);
        
        return Inertia::render('Settings/Index', $data);
    }

    public function update(Request $request): RedirectResponse
    {
        \Log::info('Début de la mise à jour des paramètres');
        \Log::info('Files reçus:', $request->allFiles());

        $validated = $request->validate([
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,tiff,webp|max:5120',
            'company_name' => 'required|string|max:255',
        ]);

        \Log::info('Validation passée', $validated);

        // Gérer le company_name
        Setting::updateOrCreate(
            ['key' => 'company_name'],
            ['value' => $validated['company_name']]
        );

        // Gérer l'upload du logo si présent
        if ($request->hasFile('logo')) {
            try {
                $file = $request->file('logo');
                $extension = $file->getClientOriginalExtension();
                $filename = 'logo.' . $extension;
                $path = $file->storeAs('logos', $filename, 's3');
                
                // Générer l'URL directe du bucket
                $bucket = config('filesystems.disks.s3.bucket');
                $region = config('filesystems.disks.s3.region');
                $url = "https://{$bucket}.s3.{$region}.scw.cloud/{$path}";
                
                \Log::info('Upload du logo réussi:', [
                    'url' => $url,
                    'path' => $path
                ]);
                
                Setting::updateOrCreate(
                    ['key' => 'logo'],
                    ['value' => $url]
                );
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'upload:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()
                ->with('error', 'Erreur lors de l\'upload du logo: ' . $e->getMessage());
            }
        }

        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return redirect()->back()
            ->with('success', 'Paramètres mis à jour avec succès.')
            ->with('settings', [
                'company_name' => $settings['company_name'] ?? '',
                'logo' => $settings['logo'] ?? ''
            ]);
    }

    public function deleteLogo(): RedirectResponse
    {
        try {
            // Récupérer le paramètre du logo
            $logoSetting = Setting::where('key', 'logo')->first();
            
            if ($logoSetting) {
                // Extraire le chemin du fichier de l'URL
                $url = $logoSetting->value;
                $path = parse_url($url, PHP_URL_PATH);
                $path = ltrim($path, '/'); // Enlever le slash initial s'il existe
                
                // Supprimer le fichier du bucket S3
                if (Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
                
                // Supprimer le paramètre
                $logoSetting->delete();
            }
            
            $response = redirect()->back()
                ->with('success', 'Logo supprimé avec succès')
                ->with('settings', [
                    'company_name' => Setting::getValue('company_name', ''),
                    'logo' => ''
                ]);
            
            \Log::info('Response avec flash messages:', [
                'success' => session('success'),
                'error' => session('error'),
                'settings' => session('settings')
            ]);
            
            return $response;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du logo:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du logo: ' . $e->getMessage());
        }
    }
}