<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    protected $table = 'password_resets';
    protected $guarded = ["id"];
    public $timestamps = false;
}
