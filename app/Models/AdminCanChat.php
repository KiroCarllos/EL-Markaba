<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminCanChat extends Model
{
    protected $table = "admin_can_chats";
    protected $fillable = [
        'user_id',  'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
