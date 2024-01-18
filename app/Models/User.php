<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;
use function asset;

class User extends Authenticatable implements JWTSubject
{
    use SoftDeletes, LaratrustUserTrait, Notifiable;

    protected $connection = "mysql";
    protected $fillable = [
        'name', "status", "role", "mobile", 'email', 'password', 'image', 'auth_token', 'device_token', 'created_by'
    ];
    protected $appends = ["education", "age"];

    public function getEducationAttribute()
    {
        $studentDetail = StudentDetail::where("user_id", $this->id)->first();
        if (!is_null($studentDetail)) {
            return $studentDetail->education == "high" ? $studentDetail->faculty_name : $studentDetail->else_education;
        }
        return null;
    }

    public function getAgeAttribute()
    {
        $studentDetail = StudentDetail::where("user_id", $this->id)->first();
        if (!is_null($studentDetail)) {
            return calculateAgeFromNationalId($studentDetail->national_id);
        }
        return null;
    }

    public function chats()
    {
        return $this->hasMany(ChatMessage::class, "to_user_id", "id");
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopeCompany($q)
    {
        return $q->where("role", "company")->whereIn("status", ["pending", "inProgress", "active", "blocked"]);
    }

    public function scopeActive($q)
    {
        return $q->where("status", "active");
    }

    public function scopeStudent($q)
    {
        return $q->where("role", "student")->whereIn("status", ["pending", "inProgress", "active", "blocked"]);
    }    public function scopeFather($q)
    {
        return $q->where("role", "father")->whereIn("status", ["pending", "inProgress", "active", "blocked"]);
    }

    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }//end of get first name

    public function getLastNameAttribute($value)
    {
        return ucfirst($value);

    }//end of get last name

    public function getImageAttribute($image)
    {
        return asset($image);
    }

    public function company_details()
    {
        return $this->hasOne(CompanyDetail::class, "user_id", "id");
    }

    public function father_details()
    {
        return $this->hasOne(FatherDetail::class, "user_id", "id");
    }

    public function student_details()
    {
        return $this->hasOne(StudentDetail::class, "user_id", "id");
    }



    ////////////////////////// Filter & Suggest
    ///
    public function scopeName($q, $name)
    {
        if (!is_null($name)) {
            return $q->where("name", "like", "%" . $name . "%");
        }
    }
    public function scopeMobile($q, $mobile)
    {
        if (!is_null($mobile)) {
            return $q->where("mobile", "like", "%" . $mobile . "%");
        }
    }
    public function scopeEmail($q, $email)
    {
        if (!is_null($email)) {
            return $q->where("email", "like", "%" . $email . "%");
        }
    }
//    public function scopeMajor($q, $name)
//    {
//        if (!is_null($name)) {
//            return $q->where("name", "like", "%" . $name . "%");
//        }
//    }
    public function scopeSearchSuggestion($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('mobile', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        });
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
