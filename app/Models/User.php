<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'identifier',
        'token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function room()
    {
        return $this->hasOne(Room::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function room_privileges()
    {
        return $this->hasManyThrough(RoomPrivilege::class, RoomUserPrivilege::class);
    }

    public function userDetails()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function reserved_id()
    {
        return $this->belongsTo(ReservedId::class);
    }
}
