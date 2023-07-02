<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GeneralController extends Controller
{
   public function getUniversities(){
       $universities= University::select("name_".App()->getLocale())->get();
       return api_response(1,"",$universities);
   }
    public function getFacultyByUniversity(Request $request){
       $request->validate([
           "university_id" => ["required","numeric",Rule::exists("universities","id")]
       ]);
        return api_response(1,"",Faculty::query()->select("name_".App()->getLocale())->where("university_id",$request->university_id)->get());
    }
    public function getMajorByFaculty(Request $request){
        $request->validate([
            "faculty_id" => ["required","numeric",Rule::exists("faculties","id")]
        ]);
        return api_response(1,"",Major::query()->select("name_".App()->getLocale())->where("faculty_id",$request->faculty_id)->get());
    }
}//end of controller
