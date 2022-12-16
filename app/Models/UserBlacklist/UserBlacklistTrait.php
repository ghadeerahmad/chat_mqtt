<?php

namespace App\Models\Traits\UserBlacklist;

trait UserBlacklistTrait
{

    public function scopeWhereBlockedUser($query, $user_id, $blocked_user)
    {
        return $query->where('user_id', $user_id)
            ->where('blocked_user_id', $blocked_user);
    }
}
