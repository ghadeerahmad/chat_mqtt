<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\RoomBlacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomBlacklistController extends Controller
{
    /**
     * get black list for a room
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        if (! check_room_role('view_blacklist', $id)) {
            return response()->json(['status' => 0, 'message' => 'forbeddin'], 403);
        }
        $list = RoomBlacklist::with(['user'])->where('room_id', $id)->get();

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $list]);
    }

    /**
     * add user to blacklist (Bann user)
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }

        if (! check_room_role('update_blacklist', $request['room_id'])) {
            return response()->json(['status' => 0, 'message' => 'forbeddin'], 403);
        }
        $check = RoomBlacklist::where('user_id', $request['user_id'])
            ->where('room_id', $request['room_id'])->first();
        if ($check != null) {
            return response()->json(['status' => 0, 'message' => 'user already added']);
        }
        $item = RoomBlacklist::create(['room_id' => $request['room_id'], 'user_id' => $request['user_id']]);
        if ($item) {
            return response()->json(['status' => 1, 'message' => 'success']);
        }

        return response()->json(['status' => 0, 'message' => 'server error'], 500);
    }

    /**
     * remove user from blacklist
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }

        if (! check_room_role('update_blacklist', $request['room_id'])) {
            return response()->json(['status' => 0, 'message' => 'forbeddin'], 403);
        }
        $check = RoomBlacklist::where('user_id', $request['user_id'])
            ->where('room_id', $request['room_id'])->delete();

        return response()->json(['status' => 1, 'message' => 'success']);
    }
}
