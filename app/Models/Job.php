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
        "job_company_id",
        "address",
        "location",
        "salary",
    ];
    public function job_company()
    {
        return $this->belongsTo(JobCompany::class,"job_company_id","id");
    }
}
