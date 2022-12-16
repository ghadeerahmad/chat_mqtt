<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\RoomUser;
use App\Models\User;
use App\Models\UserBlacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $friends = Friend::where('user_id', $user->id)
            ->orWhere('friend_id', $user->id)->get();
        $data = [];
        foreach ($friends as $friend) {
            $isBlocked = UserBlacklist::WhereBlockedUser($user->id, $friend->id)->first();
            if ($isBlocked) break;
            $user = null;
            if ($friend->user_id == auth()->user()->id) {
                $user = User::find($friend->friend_id);
            } else {
                $user = User::find($friend->user_id);
            }
            $userData = $user->toArray();
            $room = RoomUser::with(['room'])->where('user_id', $user->id)->first();
            if ($room != null) {
                $userData['room'] = $room->room;
            } else {
                $userData['room'] = null;
            }
            array_push($data, $userData);
        }

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $data]);
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
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        if (auth()->user()->id == $request['user_id']) {
            return forbidden_response('forbidden');
        }
        $check = Friend::where('user_id', auth()->user()->id)
            ->where('friend_id', $request['user_id'])
            ->first();
        if ($check != null) {
            return error_response('already added');
        }
        $check = Friend::where('friend_id', auth()->user()->id)
            ->where('user_id', $request['user_id'])
            ->first();
        if ($check != null) {
            return error_response('already added');
        }
        $friend = Friend::create([
            'user_id' => auth()->user()->id,
            'friend_id' => $request['user_id'],
        ]);
        if ($friend) {
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
        //
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        Friend::WhereFriend($id, $user->id)
            ->delete();
        Friend::WhereUser($id, $user->id)->delete();

        return success_response();
    }
}
