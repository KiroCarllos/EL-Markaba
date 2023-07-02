<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDetail extends Model
{
    protected $fillable = [
      "user_id",
      "gender",
      "major_id",
      "gender",
      "national_id",
      "graduated_at",
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
}