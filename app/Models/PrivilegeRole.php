<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivilegeRole extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function privilege()
    {
        return $this->belongsTo(Privilege::class);
    }
}
