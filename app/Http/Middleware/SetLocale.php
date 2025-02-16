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
        $language = Setting::where('key', 'language')->first()?->value ?? 'fr';
        App::setLocale($language);
        session()->put('locale', $language);
        
        return $next($request);
    }
}
