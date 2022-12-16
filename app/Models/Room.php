<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'identifier',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(RoomChat::class);
    }

    public function user_privileges()
    {
        return $this->hasMany(RoomUserPrivilege::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function users()
    {
        return $this->hasMany(RoomUser::class);
    }

    public function room_blaklist()
    {
        return $this->hasOne(RoomBlacklist::class);
    }
}
