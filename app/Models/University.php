<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    protected $table = "universities";
    protected $fillable = [
        "name_en",
        "name_ar",
    ];
}
