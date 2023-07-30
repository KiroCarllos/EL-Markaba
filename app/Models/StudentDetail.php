<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function user(){
        return $this->belongsTo(User::class,"user_id",'id');
    }
    public function faculty(){
        return $this->belongsTo(Faculty::class,"faculty_id",'id');
    }
}
