<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = "chat_messages";
    public $timestamps = false;
    protected $fillable = [
        'from_user_id', 'to_user_id', 'message', 'status','created_at','updated_at',
    ];

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
}
