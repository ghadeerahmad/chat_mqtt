<?php

namespace App\Models;

use App\Models\Traits\Favourite\RelationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory, RelationTrait;

    protected $guarded = [];
}
