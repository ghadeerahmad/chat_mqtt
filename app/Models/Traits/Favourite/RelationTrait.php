<?php

namespace App\Models\Traits\Favourite;

use App\Models\Room;
use App\Models\User;

trait RelationTrait
{
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
