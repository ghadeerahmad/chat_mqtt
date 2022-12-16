<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomBackground extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function background()
    {
        return $this->belongsTo(Background::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
