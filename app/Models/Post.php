<?php

namespace App\Models;

use Carbon\Carbon;
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
    protected $appends = ["ago"];
    public function getAgoAttribute(){
        return Carbon::parse($this->created_at)->diffForHumans();
    }
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
    public function replies(){
        return $this->hasMany(PostReply::class,"post_id","id");
    }
    public function scopeActive($q){
        return $q->where("status","active");
    }
    public function getImageAttribute($image){
        return asset($image);
    }
}
