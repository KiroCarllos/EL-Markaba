<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    protected $table = "settings";
    protected $fillable = [
        "android_version",
    ];
    public $timestamps = false;
}
