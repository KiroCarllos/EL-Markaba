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
        "created_at",
        "updated_at",
        "image",
    ];
    protected $appends = ["created_ago","title","description"];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_at = Carbon::now()->subHour()->timezone('Africa/Cairo')->toDateTimeString();
            $model->updated_at = Carbon::now()->subHour()->timezone('Africa/Cairo')->toDateTimeString();
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now()->subHour()->timezone('Africa/Cairo')->toDateTimeString();
        });
    }

    public function getCreatedAgoAttribute(){
        Carbon::setLocale(app()->getLocale());
        // Trim the date string and create Carbon instance
        $dateString = trim($this->created_at);
        $dateString = Carbon::parse($dateString)->toDateTimeString();
        $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $dateString, 'Africa/Cairo')->diffForHumans();
        return $carbonDate;
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

    public function getTitleAttribute(){
        $title = app()->getLocale() == "ar" ? $this->title_ar : $this->title_en;
        return $title;
    }
    public function getDescriptionAttribute(){
        $description = app()->getLocale() == "ar" ? $this->description_ar : $this->description_en;
        return $description;
    }
}
