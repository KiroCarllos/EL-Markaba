<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Slider;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
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

}//end of controller
