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
        "area_id",
        "gender",
        "national_id",
        "enable_update",
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
    protected $appends = ["faculty_name","university_name","area_name"];
    public function getFacultyNameAttribute(){
        return Faculty::where("id",$this->faculty_id)->pluck( "name_".app()->getLocale())->first();
    }
    public function getAreaNameAttribute(){
        return Area::where("id",$this->area_id)->pluck( "name_".app()->getLocale())->first();
    }
    public function getEnableUpdateAttribute($value){

        return $value == 0 ? false : true;
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
    public function area(){
        return $this->belongsTo(Area::class,"area_id",'id');
    }
    public function scopeMajor($q,$major)
    {
        if(!is_null($major)){
            return $q->where("major","like","%".$major."%");
        }
    }
    public function scopeSearchSuggestion($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('major', 'like', "%".$search."%")
                ->orWhere('gender', 'like', "%".$search."%")
                ->orWhere('graduated_at', 'like', "%".$search."%")
                ->orWhere('national_id', 'like', "%".$search."%")
                ->orWhere(function ($query) use ($search) {
                    $query->whereJsonContains('prior_experiences', $search)
                        ->orWhere('prior_experiences->key', 'like', "%$search%");
                }) ->orWhere(function ($query) use ($search) {
                    $query->whereJsonContains('courses', $search)
                        ->orWhere('courses->key', 'like', "%$search%");
                })
//                ->orWhereHas('area', function ($subquery) use ($search) {
//                $subquery->where('name_en', 'like', "%".$search."%")
//                            ->orWhere('name_ar', 'like', "%".$search."%")
//                            ->orWhere('name_ar', 'like', "%".$search."%");
//            })
                ->orWhereHas('faculty', function ($subquery) use ($search) {
                $subquery->where('name_en', 'like', "%".$search."%")
                            ->orWhere('name_ar', 'like', "%".$search."%")
                    ->orWhereHas('university', function ($subquery) use ($search) {
                        $subquery->where('name_en', 'like', "%".$search."%")
                            ->orWhere('name_ar', 'like', "%".$search."%");
                    });
            });
        });
    }
}
