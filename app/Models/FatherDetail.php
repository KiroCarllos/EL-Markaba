<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FatherDetail extends Model
{
    protected $table = "father_details";
    protected $fillable = [
        "user_id",
        "area_id",
        "national_image",
    ];
    protected $appends = ["area_name"];
    public $timestamps = false;
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
    public function getAreaNameAttribute()
    {
        return Area::where("id", $this->area_id)->pluck("name_" . app()->getLocale())->first();
    }
    public function getNationalImageAttribute($image){
        return asset($image);
    }

}
