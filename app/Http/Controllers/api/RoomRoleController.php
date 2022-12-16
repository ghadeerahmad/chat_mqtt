<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomPrivilege;
use App\Models\RoomUserPrivilege;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
            'privilege_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $user = auth()->user();
        if (! check_room_role('update_roles', $request['room_id'])) {
            return forbidden_response('forbidden, you don\'t have permission');
        }
        $privilege = RoomPrivilege::find($request['privilege_id']);
        if ($privilege == null) {
            return not_found_response('privilege not found');
        }
        $room = Room::find($request['room_id']);
        if ($room == null) {
            return not_found_response('room not found');
        }
        $user = User::find($request['user_id']);
        if ($user == null) {
            return not_found_response('user not found');
        }
        RoomUserPrivilege::where('user_id', $user->id)
            ->where('room_id', $room->id)
            ->delete();
        $create = RoomUserPrivilege::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'room_privilege_id' => $privilege->id,
        ]);
        if ($create) {
            return success_response();
        }

        return server_error_response();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! check_room_role('view_roles', $id)) {
            return response()->json(['status' => 0, 'message' => 'forbidden, you don\'t have permission'], 403);
        }
        $admins = RoomUserPrivilege::with(['user', 'room_privilege'])->where('room_id', $id)->get();
        $data = [];
        foreach ($admins as $admin) {
            $user = $admin->user->toArray();
            $user['privilege'] = $admin->room_privilege;
            array_push($data, $user);
        }

        return success_response($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $user = auth()->user();
        if (! check_room_role('update_roles', $request['room_id'])) {
            return response()->json(['status' => 0, 'message' => 'forbidden, you don\'t have permission'], 403);
        }

        $room = Room::find($request['room_id']);
        if ($room == null) {
            return not_found_response('room not found');
        }
        $user = User::find($request['user_id']);
        if ($user == null) {
            return not_found_response('user not found');
        }
        $check = RoomUserPrivilege::where('user_id', $user->id)
            ->where('room_id', $room->id)
            ->first();
        if ($check == null) {
            return not_found_response('user not found in this room');
        }
        $check->delete();

        return success_response();
    }
}
