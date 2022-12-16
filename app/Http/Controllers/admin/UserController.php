<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ReservedId;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('adminRole:view_user')->only('index');
        $this->middleware('adminRole:update_user')->only('update');
        $this->middleware('adminRole:create_user')->only('create');
    }

    /**
     * get users list
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(20);

        return response()->json(['status' => 1, 'data' => $users]);
    }

    /**
     * update user unique id
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $check = User::where('userId', $request['unique_id'])->first();
        if ($check != null) {
            return response()->json(['status' => 0, 'message' => 'id already reserved'], 409);
        }
        $user = User::find($request['user_id']);
        if ($user == null) {
            return response()->json(['status' => 0, 'message' => 'user not found'], 404);
        }
        $user->userId = $request['unique_id'];
        $user->save();
        $id = ReservedId::where('reserved_id', $user->userId)->first();
        $id->status = 'TAKEN';
        $id->save();

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $user], 200);
    }
}
