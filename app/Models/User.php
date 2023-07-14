<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;
use function asset;

class User extends Authenticatable  implements JWTSubject
{
    use LaratrustUserTrait,Notifiable;
    protected $connection = "mysql";
    protected $fillable = [
        'name',"status","role","mobile", 'email', 'password', 'image','auth_token'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function scopeCompany($q){
        return $q->where("role","company")->whereIn("status",["pending","inProgress","active","blocked"]);
    }
    public function scopeStudent($q){
        return $q->where("role","student")->whereIn("status",["pending","inProgress","active","blocked"]);
    }

    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }//end of get first name

    public function getLastNameAttribute($value)
    {
        return ucfirst($value);

    }//end of get last name

    public function getImageAttribute($image){
        return asset($image);
    }
    public function company_details(){
        return $this->hasOne(CompanyDetail::class,"user_id","id");
    }
    public function student_details(){
        return $this->hasOne(StudentDetail::class,"user_id","id");
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}//end of model
