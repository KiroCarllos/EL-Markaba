<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    protected $table = "trainings";
    protected $fillable = [
        "status",
        "title_en",
        "title_ar",
        "description_en",
        "description_ar",
        "paid",
        "user_id",
        "image",
    ];
    public $timestamps = false;
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
    public function applications(){
        return $this->hasMany(TrainingApplication::class,"training_id","id");
    }
    public function scopeActive($q){
        return $q->where("status","active");
    }
    public function scopeActiveMyTraining($q){
        return $q->whereIn("status",["pending","inProgress","active"]);
    }
    public function getImageAttribute($image){
        return asset($image);
    }
}
