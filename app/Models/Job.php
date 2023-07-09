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
    protected $appends = ["image"];
    public function scopeActive($q){
        return $this->whereIn("status",["pending","inProgress","active"]);
    }
    public function scopeActiveJob($q){
        return $this->whereIn("status",["active"]);
    }
    public function company()
    {
        return $this->belongsTo(User::class,"user_id","id");
    }
    public function getImageAttribute(){
        return User::whereId($this->user_id)->pluck("image")->first();
    }
}
