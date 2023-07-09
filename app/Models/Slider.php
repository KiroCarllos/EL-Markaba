<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    protected $table = "sliders";
    protected $fillable = [
        "image",
        "status",
        "role",
    ];
    public $timestamps = false;
    protected $casts = [
        "role" => "array"
    ];
    public function scopeActive($q){
        return $this->where("status","active");
    }
    public function getImageAttribute($image){
        return asset($image);
    }
}
