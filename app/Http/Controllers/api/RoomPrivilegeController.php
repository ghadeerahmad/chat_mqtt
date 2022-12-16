<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\RoomPrivilege;

class RoomPrivilegeController extends Controller
{
    /**
     * get list of rooms privileges
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $privileges = RoomPrivilege::all();

        return success_response($privileges);
    }
}
