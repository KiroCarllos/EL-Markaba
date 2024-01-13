<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Faculty;
use App\Models\University;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getAllUniversities(){
        $universities= University::select("id","name_en","name_ar")->get();
        foreach ($universities as $university){
            $name = app()->getLocale() == "ar" ?$university->name_ar:$university->name_en;
            $university->setAttribute("name",$name);
            $university->makeHidden(["name_en","name_ar"]);
        }
        return $universities;
    }
    public function getAllAreas(){
        $areas= Area::select("id","name_en","name_ar")->get();
        foreach ($areas as $area){
            $name = app()->getLocale() == "ar" ?$area->name_ar:$area->name_en;
            $area->setAttribute("name",$name);
            $area->makeHidden(["name_en","name_ar"]);
        }
        return $areas;
    }
    public function getFacultyByUniversityById($university_id){
        $faculties = Faculty::query()->select("id","name_en","name_ar")->where("university_id",$university_id)->get();
        foreach ($faculties as $faculty){
            $name = app()->getLocale() == "ar" ?$faculty->name_ar:$faculty->name_en;
            $faculty->setAttribute("name",$name);
            $faculty->makeHidden(["name_en","name_ar"]);
        }
        return $faculties;
    }

}
