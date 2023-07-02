<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        "name_en",
        "name_ar",
        "university_id",
    ];
}
