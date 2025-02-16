<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Configuration de Carbon pour utiliser la langue de l'application
        $locale = app()->getLocale();
        \Carbon\Carbon::setLocale($locale);
        setlocale(LC_TIME, $locale.'_'.strtoupper($locale).'.UTF-8');

        // Chargement des traductions pour Vue
        $translations = [];
        $langPath = resource_path('lang');
        foreach (['fr', 'en', 'de'] as $lang) {
            if (File::exists($langPath . '/' . $lang)) {
                $translations[$lang] = [
                    'pages' => File::getRequire($langPath . '/' . $lang . '/pages.php'),
                    'permissions' => File::getRequire($langPath . '/' . $lang . '/permissions.php'),
                ];
            }
        }
        
        Inertia::share([
            'translations' => $translations,
            'locale' => $locale,
        ]);
    }
}
