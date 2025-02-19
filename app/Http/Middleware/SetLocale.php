<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Priorité à la session, puis à la base de données, puis par défaut
        $language = session()->get('locale') ?? 
                   Setting::where('key', 'language')->first()?->value ?? 
                   'fr';

        App::setLocale($language);
        \Carbon\Carbon::setLocale($language);
        setlocale(LC_TIME, $language.'_'.strtoupper($language).'.UTF-8');
        
        return $next($request);
    }
}
