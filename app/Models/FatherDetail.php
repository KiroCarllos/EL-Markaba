<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FatherDetail extends Model
{
    protected $table = "father_details";
    protected $fillable = [
        "user_id",
        "national_image",
    ];
    public $timestamps = false;
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
    public function getNationalImageAttribute($image){
        return asset($image);
    }

}
