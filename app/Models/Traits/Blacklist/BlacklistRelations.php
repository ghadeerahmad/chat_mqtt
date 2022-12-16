<?php

namespace App\Models\Traits\Blacklist;

use App\Models\User;

trait BlacklistRelations
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
