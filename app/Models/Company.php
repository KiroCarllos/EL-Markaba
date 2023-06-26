<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;
    protected $table = "companiess";
    protected $fillable = [
        "user_id",
        "bio",
        "code",
        "fax",
        "commercial_record",
        "tax_card",
        "address",
        "created_date",
    ];
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
}
