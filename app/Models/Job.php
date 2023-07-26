<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        "title_en",
        "title_ar",
        "user_id",
        "status",
        "description_en",
        "description_ar",
        "work_type",
        "work_hours",
        "contact_email",
        "address",
        "location",
        "expected_salary_from",
        "expected_salary_to",
    ];
    protected $appends = ["image","title","description"];
    protected $casts = [
        "user_id" => "integer"
    ];
    public function scopeActive($q){
        return $q->whereIn("status",["pending","inProgress","active"]);
    }
    public function scopeActiveJob($q){
        return $q->whereIn("status",["active"]);
    }
    public function company()
    {
        return $this->belongsTo(User::class,"user_id","id");
    }
    public function getImageAttribute(){
        return User::whereId($this->user_id)->pluck("image")->first();
    }
    public function getTitleAttribute(){
        $title = app()->getLocale() == "ar" ? $this->title_ar : $this->title_en;
        return $title;
    }
    public function getDescriptionAttribute(){
        $description = app()->getLocale() == "ar" ? $this->description_ar : $this->description_en;
        return $description;
    }
}
