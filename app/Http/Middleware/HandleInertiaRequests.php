<?php

namespace App\Http\Middleware;

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

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'app' => [
                'name' => config('app.name'),
                'logo' => $logo,
            ],
            'translations' => [
                'menu' => trans('menu'),
                'auth' => trans('auth'),
                'pagination' => trans('pagination'),
                'passwords' => trans('passwords'),
                'validation' => trans('validation'),
                'pages' => trans('pages'),
                'permissions' => trans('permissions'),
            ],
            'locale' => $language,
            'permissions' => $this->getPermissions($request),
        ]);
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
}
