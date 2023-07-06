<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Post;
use App\Models\University;
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
    public function getPosts(){
       $posts = Post::paginate(6);
       return api_response(1,"",$posts);
    }
}//end of controller
