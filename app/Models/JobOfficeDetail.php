<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOfficeDetail extends Model
{
    use SoftDeletes;
    protected $table = "job_office_details";
    protected $fillable = [
        "user_id",
        "church_name",
        "father_name",
        "father_mobile",
        "amen_name",
        "amen_mobile",
        "logo",
        "amen_national_image",
    ];
    public $timestamps = false;
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
    public function getLogoAttribute($image){
        return asset($image);
    }
    public function getAmenNationalImageAttribute($image){
        return asset($image);
    }
}
