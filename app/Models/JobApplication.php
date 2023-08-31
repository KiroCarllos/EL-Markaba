<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        "job_id",
        "user_id",
        "status",
    ];
    protected $appends = ["created_ago"];
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
    protected $casts = [
        "job_id" => "integer"
    ];
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
    public function job(){
        return $this->belongsTo(Job::class,"job_id","id");
    }
    public function scopeIgnoreCancel($q){
        return $q->whereNotIn("status",["canceled"]);
    }
    public function getCreatedAgoAttribute(){
        Carbon::setLocale(app()->getLocale());
        // Trim the date string and create Carbon instance
        $dateString = trim($this->created_at);
        $dateString = Carbon::parse($dateString)->toDateTimeString();
        $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $dateString, 'Africa/Cairo')->diffForHumans();
        return $carbonDate;
    }
}
// TODO IF USER RETURN IN APPLICATION OR ANY SERVICE HANDLE IF APPLICATION NOT APPEAR NOR HANDLE DELETE ACCOUNT DATA APPEARS
