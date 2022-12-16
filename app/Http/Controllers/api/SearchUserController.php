<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SearchUserController extends Controller
{
    /**
     * search user by id or name
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $data = [];
        if ($request['id'] != null) {
            $user = User::where('userId', $request['id'])->first();
            if ($user == null) {
                $user = User::join('reserved_ids', 'users.reserved_id_id', '=', 'reserved_ids.id')
                    ->where('reserved_ids.reserved_id', $request['id'])
                    ->first();
            }
            array_push($data, $user);
        } elseif ($request['name'] != null) {
            $users = User::where('name', 'like', '%'.$request['name'].'%')->get();
            $data = $users;
        }

        return success_response($data);
    }
}
