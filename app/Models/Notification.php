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
    public $timestamps = false;
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
