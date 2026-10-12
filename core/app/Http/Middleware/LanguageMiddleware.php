<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            session()->put('lang', $this->getCode());
            app()->setLocale(session('lang',  $this->getCode()));
        } catch (\Exception $e) {
            \Log::error('LanguageMiddleware error: ' . $e->getMessage());
            session()->put('lang', 'en');
            app()->setLocale('en');
        }
        return $next($request);
    }

    public function getCode()
    {
        try {
            if (session()->has('lang')) {
                return session('lang');
            }
            $language = Language::where('is_default', 1)->first();
            return $language ? $language->code : 'en';
        } catch (\Exception $e) {
            \Log::error('LanguageMiddleware getCode error: ' . $e->getMessage());
            return 'en';
        }
    }


}
