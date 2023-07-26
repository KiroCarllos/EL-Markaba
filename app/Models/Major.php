<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = [
        "name_en",
        "name_ar",
        "faculty_id",
    ];

}
