<?php

namespace App\Repositories\RoomBackground;

use App\Http\Requests\RoomBackgroundRequest;

interface RoomBackgroundsInterface
{
    public function index();

    public function select(RoomBackgroundRequest $request);
}
