<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWhereFriend($query, $friend_id, $user_id)
    {

        return $query->where('user_id', $user_id)
            ->where('friend_id', $friend_id);
    }
    public function scopeWhereUser($query, $friend_id, $user_id)
    {

        return $query->where('user_id', $friend_id)
            ->where('friend_id', $user_id);
    }
}
