<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBlacklist extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blocked_user()
    {
        return $this->belongsTo(User::class, 'blocked_user_id');
    }
    public function scopeWhereBlockedUser($query, $user_id, $blocked_user)
    {
        return $query->where('user_id', $user_id)
            ->where('blocked_user_id', $blocked_user);
    }
}
