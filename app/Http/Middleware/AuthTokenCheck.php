<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTokenCheck
{

    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return api_response(0,"Token not provided","",401);
        }
        try {
            $parsedToken = JWTAuth::parseToken($token);
            // Get the payload data from the token
            $payload = $parsedToken->getPayload();
        } catch (JWTException  $e) {
            return api_response(0,"Token expired","",401);
        } catch (Exception $e) {
            return api_response(0," Invalid Token","",401);
        }
        // Assuming you have a `User` model
        $user = User::where('auth_token', $token)->first();
        if (!$user) {
            return api_response(0,"User not found or Invalid Token","",401);
        }
        Auth::guard("api")->login($user);
        return $next($request);
    }
}
