<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        "title",
        "description",
        "type",
        "contact_email",
        "company_id",
        "address",
        "location",
        "salary",
    ];
    public function company()
    {
        return $this->belongsTo(Company::class,"company_id","id");
    }
}
