<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('adminRole:view_role')->only(['index', 'show']);
    }

    /**
     * get list of roles
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();

        return response()->json(['status' => 1, 'data' => $roles]);
    }
}
