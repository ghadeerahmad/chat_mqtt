<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPrivilegeRole extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function room_role()
    {
        return $this->belongsTo(RoomRole::class);
    }

    public function room_privilege()
    {
        return $this->belongsTo(RoomPrivilege::class);
    }
}
