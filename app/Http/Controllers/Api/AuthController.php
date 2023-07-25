<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function login(Request $request){
//        $email =  $request->has("mobile") &&  !is_null($request->mobile) ? User::query()->where("mobile", $request->mobile)->pluck("email")->first() : $request->email;
        $credentials = ["email" =>$request->email,"password"=>$request->password];
        if (!$token = auth("api")->attempt($credentials)) {
            return api_response(0, "These credentials do not match our records.", "", 401);
        }
        $user = User::where("email", $request->email)->first();
        $user->update(["auth_token" => $token]);
        return api_response(1, __("site.successfully login"), $user);
    }



    public function profile(Request $request){
        dd($request->all());
    }

}//end of controller
