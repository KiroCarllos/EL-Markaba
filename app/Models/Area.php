<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = "areas";
    protected $fillable = [
        "name_en",
        "name_ar",
    ];
}
