<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeNotDeletedBySender($query, $chat_id)
    {
        return $query->where('delete_sender', 0)
            ->where('chat_id', $chat_id);
    }
    public function scopeNotDeletedByReciever($query, $chat_id)
    {
        return $query->where('delete_reciever', 0)
            ->where('chat_id', $chat_id);
    }
}
