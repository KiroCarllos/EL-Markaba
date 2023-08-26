<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = "notifications";
    protected $fillable = [
        "type",
        "title",
        "body",
        "model_id",
        "read",
        "user_id",
        "model_json",
    ];
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
    protected $casts = [
        "model_json" => "json"
    ];
    protected $appends = ["created_ago"];
    public function getCreatedAgoAttribute(){

        return Carbon::parse($this->created_at)->diffForHumans();
    }
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
}
