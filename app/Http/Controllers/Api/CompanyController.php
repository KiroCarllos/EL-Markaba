<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    // company login
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $credentials = ["email" =>$request->email,"password"=>$request->password];
        if (!$token = auth("api")->attempt($credentials)) {
            return api_response(0, "These credentials do not match our records.", "", 401);
        }
        $user = User::where("email", $request->email)->first();
        if ($user->role == "company" || $user->role == "super_admin"){
            if ($user->status == "active"){
                $user->update(["auth_token" => $token]);
                return api_response(1, "company successfully login", $user);
            }else{
                return api_response(0, "Sorry Your Account is ".$user->status." now");
            }

        }else{
            return api_response(0, "Sorry Your Account Not Be Company", "", 401);
        }
    }
    // company register
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string',
            'mobile' => 'required|string|size:11|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            "administrator_name" => "required|string",
            "administrator_mobile" => "required|string|size:11",
            'bio' => 'required|string',
            'created_date' => 'required|date',
            'address' => 'required|string',
            'logo' => 'required|mimes:jpeg,png,jpg|max:2048',
            'commercial_record_image' => 'required|mimes:jpeg,png,jpg|max:2048',
            'tax_card_image' => 'required|mimes:jpeg,png,jpg|max:2048',
        ]);
        $userData = $request->only(["name","mobile","email"]);
        $userData["password"] = Hash::make($request->password);
        try{
            DB::beginTransaction();
            $user = User::query()->firstOrCreate([
                "mobile" => $userData["mobile"]
            ],[
                "name" => $userData["name"],
                "email" => $userData["email"],
                "password" => $userData["password"],
                "role" =>  "company",
            ]);
            deleteOldFiles("uploads/companies/".$user->id."/logo");
            if ($request->logo) {
                $user->update(["image" => uploadImage($request->logo,"uploads/companies/".$user->id."/logo/".generateBcryptHash($user->id)."/logo")]);
            }
            $user->attachRole('company');
            $companyData = $request->only(["administrator_name","administrator_mobile","bio","address"]);
            $companyData["created_date"] = Carbon::parse($request->created_date)->toDateString();
            $company = CompanyDetail::query()->updateOrCreate([
                "user_id" => $user->id
            ],$companyData);
            deleteOldFiles("uploads/companies/".$user->id."/commercial_record");
            if ($request->commercial_record_image) {
                $company->update(["commercial_record_image" => uploadImage($request->commercial_record_image,"uploads/companies/".$user->id."/commercial_record/".generateBcryptHash($user->id)."/commercial_record")]);
            }
            deleteOldFiles("uploads/companies/".$user->id."/tax_card");
            if ($request->tax_card_image) {
                $company->update(["tax_card_image" => uploadImage($request->tax_card_image,"uploads/companies/".$user->id."/tax_card/".generateBcryptHash($user->id)."/tax_card")]);
            }
            DB::commit();
            return api_response(1,"company created successfully wait admins for approve");
        }catch (\Exception $exception){
            DB::rollBack();
            return api_response(0,$exception);
        }

    }

    // company profile
    public function profile(){
        $user =User::query()->where("id",auth("api")->id())->with("company_details")->first();
      return api_response(1,"profile company get successfully",$user);
    }
    // company logout
    public function logout(){
        $user = auth("api")->user();
        $user->update(["auth_token"=> null]);
        return api_response(1,"company signOut successfully");
    }
}//end of controller
