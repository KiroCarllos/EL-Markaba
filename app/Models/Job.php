<?php

namespace App\Models;

use Carbon\Carbon;
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
    protected $appends = ["image","title","description", "application_status", "applied","created_ago"];
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
    public function getApplicationStatusAttribute()
    {
        $my_job_ids = JobApplication::where("job_id",$this->id)->where("user_id",auth("api")->id())->pluck("job_id")->toArray();
        if(in_array($this->id,$my_job_ids)){
            return  JobApplication::where("job_id",$this->id)->where("user_id",auth("api")->id())->pluck("status")->first();
        }else{
            return null;
        }
    }
    public function getAppliedAttribute()
    {
        $my_job_ids = JobApplication::where("job_id",$this->id)->where("user_id",auth("api")->id())->pluck("job_id")->toArray();
        if(in_array($this->id,$my_job_ids)){
            return  true;
        }else{
            return false;
        }
    }

    public function getCreatedAgoAttribute(){
        Carbon::setLocale(app()->getLocale());
        // Trim the date string and create Carbon instance
        $dateString = trim($this->created_at);
        $dateString = Carbon::parse($dateString)->toDateTimeString();
        $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $dateString, 'Africa/Cairo')->diffForHumans();
        return $carbonDate;
    }
}
