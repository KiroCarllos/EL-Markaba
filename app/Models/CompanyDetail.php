<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyDetail extends Model
{
    use SoftDeletes;
    protected $table = "company_details";
    protected $fillable = [
        "user_id",
        "administrator_name",
        "administrator_mobile",
        "bio",
        "created_date",
        "address",
        "commercial_record_image",
        "tax_card_image",
    ];
    public $timestamps = false;
    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }
}
