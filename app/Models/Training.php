<?php

namespace App\Models;

use Carbon\Carbon;
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
        "created_at",
        "updated_at",
        "paid",
        "user_id",
        "image",
    ];
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_at = Carbon::now()->timezone('Africa/Cairo')->toDateTimeString();
            $model->updated_at = Carbon::now()->timezone('Africa/Cairo')->toDateTimeString();
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now()->timezone('Africa/Cairo')->toDateTimeString();
        });
    }
    protected $appends = ["title", "description", "application_status", "applied"];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function applications()
    {
        return $this->hasMany(TrainingApplication::class, "training_id", "id");
    }

    public function scopeActive($q)
    {
        return $q->where("status", "active");
    }

    public function scopeActiveMyTraining($q)
    {
        return $q->whereIn("status", ["pending", "inProgress", "active"]);
    }

    public function getImageAttribute($image)
    {
        return asset($image);
    }

    public function getTitleAttribute()
    {
        $title = app()->getLocale() == "ar" ? $this->title_ar : $this->title_en;
        return $title;
    }

    public function getDescriptionAttribute()
    {
        $description = app()->getLocale() == "ar" ? $this->description_ar : $this->description_en;
        return $description;
    }

    public function getApplicationStatusAttribute()
    {
        $mytraining_ids = TrainingApplication::where("training_id",$this->id)->where("user_id",auth("api")->id())->pluck("training_id")->toArray();
        if(in_array($this->id,$mytraining_ids)){
            return  TrainingApplication::where("training_id",$this->id)->where("user_id",auth("api")->id())->pluck("status")->first();
        }else{
            return null;
        }
    }
    public function getAppliedAttribute()
    {
        $mytraining_ids = TrainingApplication::where("training_id",$this->id)->where("user_id",auth("api")->id())->pluck("training_id")->toArray();
        if(in_array($this->id,$mytraining_ids)){
            return  true;
        }else{
            return false;
        }
    }

}
