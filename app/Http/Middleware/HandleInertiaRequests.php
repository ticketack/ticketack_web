<?php

namespace App\Http\Middleware;

use App\Models\NotificationLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $language = $request->session()->get('locale', config('app.locale'));
        app()->setLocale($language);
        \Carbon\Carbon::setLocale($language);
        setlocale(LC_TIME, $language.'_'.strtoupper($language).'.UTF-8');

        // Recharger les traductions
        app()->forgetInstance('translator');
        app()->instance('translator', new \Illuminate\Translation\Translator(
            new \Illuminate\Translation\FileLoader(
                new \Illuminate\Filesystem\Filesystem(),
                base_path('lang')
            ),
            $language
        ));

        // Récupérer le logo depuis la base de données
        $logo = null;
        $logoSetting = Setting::where('key', 'logo')->first();
        
        if ($logoSetting && $logoSetting->value) {
            // Vérifier si c'est une URL complète
            if (filter_var($logoSetting->value, FILTER_VALIDATE_URL)) {
                $logo = $logoSetting->value;
            } else {
                // Si ce n'est pas une URL complète, générer l'URL S3
                $logo = \Storage::disk('s3')->url($logoSetting->value);
            }
        }
         
        // Ajouter l'ID de l'utilisateur pour Laravel Echo dans les props
        $laravelData = null;
        if ($request->user()) {
            $laravelData = [
                'user' => [
                    'id' => $request->user()->id
                ]
            ];
        }
        
        return array_merge(parent::share($request), [
            'laravel' => $laravelData,
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'color' => $request->user()->color,
                    'roles' => $request->user()->roles->map(function($role) {
                        return $role->name;
                    })
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'app' => [
                'name' => config('app.name'),
                'logo' => $logo,
            ],
            'translations' => array_merge([
                // Debug des traductions
                // Traductions système
                'menu' => trans('menu'),
                'pagination' => trans('pagination'),
                'passwords' => trans('passwords'),
                'validation' => trans('validation'),
                'permissions' => trans('permissions'),
                'auth' => trans('auth'),
                'profile' => trans('profile'),
            ], $this->loadTranslationFiles()),
            'locale' => $language,
            'permissions' => $this->getPermissions($request),
            'unreadNotificationsCount' => $this->getUnreadNotificationsCount($request),
        ]);
    }

    /**
     * Load all translation files from the lang directory
     */
    protected function loadTranslationFiles(): array
    {
        $translations = [];
        $path = base_path('lang/' . app()->getLocale());
        
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $key = pathinfo($file, PATHINFO_FILENAME);
                    $translations[$key] = trans($key);
                    \Log::debug('Loading translation file: ' . $file, [
                        'key' => $key,
                        'content' => $translations[$key]
                    ]);
                }
            }
        } else {
            \Log::error('Translation directory not found: ' . $path);
        }
        
        return $translations;
    }

    protected function getPermissions(Request $request): array
    {
        if (!$request->user()) {
            return [];
        }

        return $request->user()->getAllPermissions()->pluck('name')->mapWithKeys(function ($permission) {
            return [$permission => true];
        })->all();
    }
    
    /**
     * Récupérer le nombre de notifications non lues pour l'utilisateur connecté
     */
    protected function getUnreadNotificationsCount(Request $request): int
    {
        if (!$request->user()) {
            return 0;
        }
        
        return NotificationLog::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();
    }
}
