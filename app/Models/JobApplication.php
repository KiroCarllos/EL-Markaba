<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        "job_id",
        "user_id",
        "status",
    ];
    public $timestamps = false;
    protected $casts = [
        "job_id" => "integer"
    ];
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
    public function job(){
        return $this->belongsTo(Job::class,"job_id","id");
    }
}
