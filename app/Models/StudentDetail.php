<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Collection;
class StudentDetail extends Model
{
    protected $fillable = [
        "user_id",
        "gender",
        "major",
        "faculty_id",
        "gender",
        "national_id",
        "education",
        "graduated_at",
        "else_education",
        "address",
        "courses",
        "prior_experiences",
    ];
    protected $casts = [
        "courses" => "json",
        "prior_experiences" => "json",
    ];
    protected $appends = ["faculty_name","university_name"];
    public function getFacultyNameAttribute(){
        return Faculty::where("id",$this->faculty_id)->pluck( "name_".app()->getLocale())->first();
    }
    public function getUniversityNameAttribute(){
        $university_id = Faculty::where("id",$this->faculty_id)->pluck("university_id")->first();
        return University::where("id",$university_id)->pluck( "name_".app()->getLocale())->first();
    }
    public function getPriorExperiencesAttribute($priorExperiencesValue){
        $filteredArray = array_filter(json_decode($priorExperiencesValue), function($value) {
            return $value !== null;
        });
        return $filteredArray;
    }
    public function getCoursesAttribute($coursesvalue){
        $filteredArray = array_filter(json_decode($coursesvalue), function($value) {
            return $value !== null;
        });
        return $filteredArray;
    }
    public function user(){
        return $this->belongsTo(User::class,"user_id",'id');
    }
    public function faculty(){
        return $this->belongsTo(Faculty::class,"faculty_id",'id');
    }
}
