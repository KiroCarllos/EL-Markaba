<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingApplication extends Model
{
    protected $table = "training_applications";
    protected $fillable = [
        "training_id",
        "user_id",
        "receipt_image",
        "status",
    ];
    public $timestamps = false;
    protected $casts = [
        "training_id" => "integer"
    ];
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
    public function training(){
        return $this->belongsTo(Training::class,"training_id","id");
    }
    public function scopeIgnoreCancel($q){
        return $q->whereNotIn("status",["canceled"]);
    }
}
