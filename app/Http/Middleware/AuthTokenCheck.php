<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class AuthTokenCheck
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
        $authToken =str_replace("Bearer ","",$request->header('Authorization')) ;

        // Check if the 'auth_token' exists in the database
        $user = User::where('auth_token', $authToken)->first();

        if (!$user) {
            return api_response(1,"Invalid or expired tokens","",401);
        }
        return $next($request);
    }
}
