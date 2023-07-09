<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        "title",
        "user_id",
        "status",
        "description",
        "work_type",
        "work_hours",
        "contact_email",
        "address",
        "location",
        "expected_salary_from",
        "expected_salary_to",
    ];
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
}
