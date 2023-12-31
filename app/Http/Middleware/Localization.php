<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Illuminate\Support\Facades\Auth;
class Localization
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
        $locale = 'en';
        if(Auth::check() && Auth::user()->language){
            $locale = Auth::user()->language;
        }else{
    
            if(session()->has('locale')){
                $locale = session()->get('locale');

            }
            else{
                session()->put('locale', config('configs.language_name'));
                $locale = session()->get('locale');
            }

         
        }

        App::setLocale($locale);


        return $next($request);
    }
}
