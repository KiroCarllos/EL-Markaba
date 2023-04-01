<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserStudentDetail extends Model
{
    protected $fillable = [
      "user_id",
      "faculty",
      "university",
      "gender",
      "national_id",
      "graduated_at",
      "address",
    ];
    public function user(){
        return $this->belongsTo(User::class,"user_id",'id');
    }
}
