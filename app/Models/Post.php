<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    protected $table = "posts";
    protected $fillable = [
        "status",
        "title_en",
        "title_ar",
        "description_en",
        "description_ar",
        "user_id",
        "image",
    ];
    public $timestamps = false;
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
    public function scopeActive($q){
        return $this->where("status","active");
    }
}
