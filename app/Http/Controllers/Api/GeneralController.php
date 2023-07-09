<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\ResetPassword;
use App\Models\Slider;
use App\Models\University;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class GeneralController extends Controller
{
   public function getUniversities(){
       $universities= University::select("id","name_".app()->getLocale())->get();
       return api_response(1,"",$universities);
   }
    public function getFacultyByUniversity(Request $request){
       $request->validate([
           "university_id" => ["required","numeric",Rule::exists("universities","id")]
       ]);
        return api_response(1,"",Faculty::query()->select("id","name_".app()->getLocale())->where("university_id",$request->university_id)->get());
    }
    public function getMajorByFaculty(Request $request){
        $request->validate([
            "faculty_id" => ["required","numeric",Rule::exists("faculties","id")]
        ]);
        return api_response(1,"",Major::query()->select("id","name_".app()->getLocale())->where("faculty_id",$request->faculty_id)->get());
    }
    public function getSlider(Request $request){
        $request->validate([
            "role" => ["required","in:super_admin,admin,student,job_company,company"]
        ]);
        $sliders = Slider::active()->whereJsonContains("role",$request->role)->pluck("image")->toArray();
        return api_response(1,"",$sliders);
    }



    //
    public function sendMailForReset(Request $request){
        $request->validate([
            "email" => ["required","email","string"]
        ]);
        try {
            $token = rand(10000,50000);
            ResetPassword::where("email", $request->email)->delete();
            ResetPassword::create([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
            $userEmail = $request->email;
            $data["token"] = $token;
            $data["email"] = $userEmail;
            Mail::send("mail.reset_password", ['data' => $data], function ($message) use ($userEmail) {
                $message->from("elmarkaba.careerguidance@gmail.com", 'El Markaba');
                $message->to($userEmail);
                $message->subject('El Markaba Reset Password');
            });
            if (Mail::failures()) {
                return api_response(0, "sorry,some thing went error In Mail please try again");
            }
            return api_response(1, "Sent successfully Please, Check Your Inbox or Spam");
        } catch (\Exception $exception) {
            return api_response(0, $exception->getMessage());
        }
    }

}//end of controller
