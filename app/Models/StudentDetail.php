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
    protected $appends = ["major"];

    public function getMajorAttribute(){
        return Major::query()->select(["id","name_".app()->getLocale()])->where("id",$this->major_id)->first();
    }
    public function user(){
        return $this->belongsTo(User::class,"user_id",'id');
    }
    public function major(){
        return $this->belongsTo(Major::class,"major_id",'id');
    }
}
