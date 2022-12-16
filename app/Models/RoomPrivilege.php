<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPrivilege extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function room_privilege_roles()
    {
        return $this->hasMany(RoomPrivilegeRole::class);
    }
}
