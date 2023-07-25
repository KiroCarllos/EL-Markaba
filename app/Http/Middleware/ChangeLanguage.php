<?php

namespace App\Http\Middleware;

use Closure;

class ChangeLanguage
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
        if (is_null($request->header('lang')) || $request->header('lang') == "ar" || $request->header('lang') =="en")
        {
            if($request->header('lang') == 'ar' )
            {
                app()->setLocale('ar');
            }else{
                app()->setLocale('en');
            }
            return $next($request);
        }else{
            return api_response(0,'invalid lang in header','');
        }


    }
}
